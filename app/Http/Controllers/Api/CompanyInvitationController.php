<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CompanyInvitationMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CompanyInvitationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // =====================================================
        // VALIDATE INPUT
        // =====================================================

        $validator = Validator::make(
            $request->all(),
            [
                'company_id' => [
                    'required',
                    'uuid',
                ],

                'invited_user_id' => [
                    'required',
                    'uuid',
                ],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid invitation data.',
                'errors' => $validator->errors(),
            ], 422);
        }


        // =====================================================
        // SUPABASE USER ACCESS TOKEN
        // =====================================================

        $accessToken = $request->bearerToken();

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token is missing.',
            ], 401);
        }


        // =====================================================
        // SUPABASE CONFIG
        // =====================================================

        $supabaseUrl = config('services.supabase.url');
        $supabaseAnonKey = config('services.supabase.anon_key');

        if (
            empty($supabaseUrl) ||
            empty($supabaseAnonKey)
        ) {
            Log::error(
                'Supabase configuration is missing for company invitations.'
            );

            return response()->json([
                'success' => false,
                'message' => 'Server configuration error.',
            ], 500);
        }


        try {
            // =================================================
            // CALL SECURE SUPABASE RPC
            //
            // مهم:
            // نرسل JWT الخاص بالمستخدم نفسه
            // لذلك auth.uid() داخل RPC سيعمل بشكل صحيح.
            // =================================================

            $rpcResponse = Http::timeout(15)
                ->withHeaders([
                    'apikey' => $supabaseAnonKey,
                    'Authorization' =>
                        'Bearer ' . $accessToken,
                    'Accept' =>
                        'application/json',
                    'Content-Type' =>
                        'application/json',
                ])
                ->post(
                    rtrim($supabaseUrl, '/') .
                    '/rest/v1/rpc/send_company_invitation',
                    [
                        'p_company_id' =>
                            $request->string(
                                'company_id'
                            )->toString(),

                        'p_invited_user_id' =>
                            $request->string(
                                'invited_user_id'
                            )->toString(),
                    ]
                );


            // =================================================
            // RPC FAILED
            // =================================================

            if ($rpcResponse->failed()) {
                $error =
                    $rpcResponse->json();

                Log::warning(
                    'SEND COMPANY INVITATION RPC FAILED',
                    [
                        'status' =>
                            $rpcResponse->status(),

                        'response' =>
                            $error,
                    ]
                );

                $message =
                    $error['message'] ??
                    'Could not create the invitation.';

                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $this->resolveRpcStatus(
                    $rpcResponse->status()
                ));
            }


            // =================================================
            // READ RPC RESULT
            // =================================================

            $rpcData =
                $rpcResponse->json();

            if (
                !is_array($rpcData) ||
                empty($rpcData)
            ) {
                throw new \RuntimeException(
                    'Invitation RPC returned no data.'
                );
            }

            $rpcInvitation =
                $rpcData[0] ?? null;

            if (
                !is_array($rpcInvitation) ||
                empty(
                    $rpcInvitation[
                        'invitation_id'
                    ]
                )
            ) {
                throw new \RuntimeException(
                    'Invitation ID was not returned.'
                );
            }

            $invitationId =
                $rpcInvitation[
                    'invitation_id'
                ];


            // =================================================
            // LOAD INVITATION DETAILS
            // =================================================

            $invitation =
                DB::table(
                    'company_invitations as ci'
                )
                    ->join(
                        'companies as c',
                        'c.id',
                        '=',
                        'ci.company_id'
                    )
                    ->join(
                        'profiles as invited',
                        'invited.id',
                        '=',
                        'ci.invited_user_id'
                    )
                    ->join(
                        'profiles as inviter',
                        'inviter.id',
                        '=',
                        'ci.invited_by'
                    )
                    ->where(
                        'ci.id',
                        $invitationId
                    )
                    ->select([
                        'ci.id',
                        'ci.invited_email',
                        'ci.status',
                        'ci.expires_at',

                        'c.id as company_id',
                        'c.name as company_name',

                        'invited.id as invited_user_id',
                        'invited.full_name as invited_user_name',

                        'inviter.id as inviter_id',
                        'inviter.full_name as inviter_name',
                    ])
                    ->first();


            if (!$invitation) {
                throw new \RuntimeException(
                    'Invitation could not be loaded after creation.'
                );
            }


            // =================================================
            // INVITATION URL
            // =================================================

            $invitationUrl =
                rtrim(
                    config('app.url'),
                    '/'
                )
                . '/invite/'
                . $invitation->id;


            // =================================================
            // SEND EMAIL
            // =================================================

            Mail::to(
                $invitation->invited_email
            )->send(
                new CompanyInvitationMail(
                    invitedUserName:
                        $invitation
                            ->invited_user_name,

                    inviterName:
                        $invitation
                            ->inviter_name,

                    companyName:
                        $invitation
                            ->company_name,

                    invitationUrl:
                        $invitationUrl,
                )
            );


            // =================================================
            // SUCCESS
            // =================================================

            Log::info(
                'COMPANY INVITATION SENT',
                [
                    'invitation_id' =>
                        $invitation->id,

                    'company_id' =>
                        $invitation->company_id,

                    'invited_user_id' =>
                        $invitation->invited_user_id,

                    'inviter_id' =>
                        $invitation->inviter_id,
                ]
            );


            return response()->json([
                'success' => true,

                'message' =>
                    'Invitation sent successfully.',

                'data' => [
                    'invitation_id' =>
                        $invitation->id,

                    'status' =>
                        $invitation->status,

                    'company_name' =>
                        $invitation->company_name,

                    'invited_user_name' =>
                        $invitation
                            ->invited_user_name,

                    'expires_at' =>
                        $invitation->expires_at,
                ],
            ], 201);

        } catch (Throwable $e) {
            Log::error(
                'SEND COMPANY INVITATION ERROR',
                [
                    'message' =>
                        $e->getMessage(),

                    'trace' =>
                        $e->getTraceAsString(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'Could not send the invitation.',
            ], 500);
        }
    }


    // =========================================================
    // RPC STATUS
    // =========================================================

    private function resolveRpcStatus(
        int $status
    ): int {
        if ($status === 401) {
            return 401;
        }

        if ($status === 403) {
            return 403;
        }

        return 422;
    }
}