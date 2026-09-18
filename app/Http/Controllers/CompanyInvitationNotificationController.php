<?php

namespace App\Http\Controllers;

use App\Exceptions\SupabaseException;
use App\Mail\CompanyInvitationWithdrawnMail;
use App\Services\Supabase\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;
use App\Mail\CompanyInvitationResponseMail;

class CompanyInvitationNotificationController extends Controller
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function withdrawn(
        Request $request,
        string $id,
    ): JsonResponse {
        // =====================================================
        // VALIDATE INVITATION ID
        // =====================================================

        if (! Str::isUuid($id)) {
            return response()->json(
                [
                    'message' => 'Invalid invitation id.',
                ],
                422,
            );
        }

        // =====================================================
        // GET SUPABASE USER TOKEN
        // =====================================================

        $token = trim(
            (string) $request->bearerToken()
        );

        if ($token === '') {
            return response()->json(
                [
                    'message' => 'Authentication required.',
                ],
                401,
            );
        }

        // =====================================================
        // VERIFY INVITATION + COMPANY ADMIN
        // =====================================================

        try {
            $rows = $this->client->rpc(
                'claim_company_invitation_withdrawal_email',
                [
                    'p_invitation_id' => $id,
                ],
                $token,
            );
        } catch (SupabaseException $e) {
            Log::warning(
                'Withdrawal email authorization failed.',
                [
                    'invitation_id' => $id,
                ],
            );

            return response()->json(
                [
                    'message' =>
                        'Unable to authorize this notification.',
                ],
                in_array(
                    $e->status ?? null,
                    [
                        401,
                        403,
                    ],
                    true,
                )
                    ? 403
                    : 409,
            );
        }

        // =====================================================
        // GET RPC RESULT
        // =====================================================

        $payload =
            isset($rows[0]) &&
            is_array($rows[0])
                ? $rows[0]
                : null;

        if ($payload === null) {
            return response()->json(
                [
                    'message' =>
                        'Invitation notification data was not returned.',
                ],
                409,
            );
        }

        // =====================================================
        // ALREADY SENT
        // =====================================================

        $shouldSend =
            (bool) (
                $payload['should_send']
                ?? false
            );

        if (! $shouldSend) {
            return response()->json(
                [
                    'ok' => true,
                    'already_sent' => true,
                ],
            );
        }

        // =====================================================
        // EMAIL DATA
        // =====================================================

        $recipientEmail = trim(
            (string) (
                $payload['invited_email']
                ?? ''
            )
        );

        $recipientName = trim(
            (string) (
                $payload['invited_name']
                ?? 'TalentFlow User'
            )
        );

        $companyName = trim(
            (string) (
                $payload['company_name']
                ?? 'the company'
            )
        );

        if ($recipientEmail === '') {
            $this->releaseEmailClaim(
                $id,
                $token,
            );

            return response()->json(
                [
                    'message' =>
                        'Invitation recipient email is missing.',
                ],
                422,
            );
        }

        // =====================================================
        // SEND EMAIL
        // =====================================================

        try {
            Mail::to(
                $recipientEmail
            )->send(
                new CompanyInvitationWithdrawnMail(
                    recipientName:
                        $recipientName,
                    companyName:
                        $companyName,
                )
            );
        } catch (Throwable $e) {
            // لو الإرسال فشل، نسمح بمحاولة ثانية لاحقاً.
            $this->releaseEmailClaim(
                $id,
                $token,
            );

            Log::error(
                'Company invitation withdrawal email failed.',
                [
                    'invitation_id' => $id,
                    'exception' => $e::class,
                ],
            );

            return response()->json(
                [
                    'message' =>
                        'Invitation was withdrawn, but the email could not be sent.',
                ],
                503,
            );
        }

        // =====================================================
        // SUCCESS
        // =====================================================

        return response()->json(
            [
                'ok' => true,
                'already_sent' => false,
            ],
        );
    }

    // =========================================================
    // RELEASE EMAIL CLAIM IF SENDING FAILED
    // =========================================================

    private function releaseEmailClaim(
        string $invitationId,
        string $token,
    ): void {
        try {
            $this->client->rpc(
                'release_company_invitation_withdrawal_email',
                [
                    'p_invitation_id' =>
                        $invitationId,
                ],
                $token,
            );
        } catch (Throwable $e) {
            Log::warning(
                'Unable to release withdrawal email claim.',
                [
                    'invitation_id' =>
                        $invitationId,
                    'exception' =>
                        $e::class,
                ],
            );
        }
    }
    public function responded(
    Request $request,
    string $id,
): JsonResponse {
    if (! Str::isUuid($id)) {
        return response()->json([
            'ok' => false,
            'message' => 'Invalid invitation ID.',
        ], 422);
    }

    $token = $request->bearerToken();

    if ($token === null || trim($token) === '') {
        return response()->json([
            'ok' => false,
            'message' => 'Missing access token.',
        ], 401);
    }

    try {
        $rows = $this->client->rpc(
            'claim_company_invitation_response_email',
            [
                'p_invitation_id' => $id,
            ],
            $token,
        );

        $row = $rows[0] ?? null;

        if (! is_array($row)) {
            return response()->json([
                'ok' => false,
                'message' => 'Invitation response data was not returned.',
            ], 500);
        }

        $shouldSend =
            (bool) ($row['should_send'] ?? false);

        if (! $shouldSend) {
            return response()->json([
                'ok' => true,
                'already_sent' => true,
            ]);
        }

        $managerEmail =
            trim(
                (string) (
                    $row['manager_email']
                    ?? ''
                )
            );

        $managerName =
            trim(
                (string) (
                    $row['manager_name']
                    ?? 'Company Manager'
                )
            );

        $employeeName =
            trim(
                (string) (
                    $row['employee_name']
                    ?? 'TalentFlow User'
                )
            );

        $companyName =
            trim(
                (string) (
                    $row['company_name']
                    ?? 'the company'
                )
            );

        $status =
            trim(
                (string) (
                    $row['invitation_status']
                    ?? ''
                )
            );

        if (
            $managerEmail === ''
            || ! in_array(
                $status,
                [
                    'accepted',
                    'declined',
                ],
                true,
            )
        ) {
            $this->releaseResponseEmailClaim(
                $id,
                $token,
            );

            return response()->json([
                'ok' => false,
                'message' => 'Invalid invitation response email data.',
            ], 422);
        }

        try {
            Mail::to(
                $managerEmail
            )->send(
                new CompanyInvitationResponseMail(
                    managerName:
                        $managerName,
                    employeeName:
                        $employeeName,
                    companyName:
                        $companyName,
                    status:
                        $status,
                )
            );
        } catch (Throwable $exception) {
            $this->releaseResponseEmailClaim(
                $id,
                $token,
            );

            Log::error(
                'Failed to send company invitation response email.',
                [
                    'invitation_id' => $id,
                    'status' => $status,
                    'error' => $exception->getMessage(),
                ],
            );

            return response()->json([
                'ok' => false,
                'message' => 'Unable to send response email.',
            ], 503);
        }

        return response()->json([
            'ok' => true,
            'already_sent' => false,
            'status' => $status,
        ]);
    } catch (SupabaseException $exception) {
        Log::warning(
            'Supabase rejected company invitation response email request.',
            [
                'invitation_id' => $id,
                'error' => $exception->getMessage(),
            ],
        );

        return response()->json([
            'ok' => false,
            'message' => $exception->getMessage(),
        ], 403);
    } catch (Throwable $exception) {
        Log::error(
            'Company invitation response email request failed.',
            [
                'invitation_id' => $id,
                'error' => $exception->getMessage(),
            ],
        );

        return response()->json([
            'ok' => false,
            'message' => 'Unable to process response email.',
        ], 500);
    }
}
private function releaseResponseEmailClaim(
    string $invitationId,
    string $token,
): void {
    try {
        $this->client->rpc(
            'release_company_invitation_response_email',
            [
                'p_invitation_id' =>
                    $invitationId,
            ],
            $token,
        );
    } catch (Throwable $exception) {
        Log::error(
            'Failed to release invitation response email claim.',
            [
                'invitation_id' =>
                    $invitationId,
                'error' =>
                    $exception->getMessage(),
            ],
        );
    }
}
}