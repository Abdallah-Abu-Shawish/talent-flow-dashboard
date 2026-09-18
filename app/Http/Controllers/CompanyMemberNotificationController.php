<?php

namespace App\Http\Controllers;

use App\Exceptions\SupabaseException;
use App\Mail\CompanyMemberActionMail;
use App\Services\Supabase\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

final class CompanyMemberNotificationController extends Controller
{
    public function __construct(
        private Client $client,
    ) {}

    public function send(
        Request $request,
        string $id,
    ): JsonResponse {
        if (! Str::isUuid($id)) {
            return response()->json(
                [
                    'message' =>
                        'Invalid event id.',
                ],
                422,
            );
        }

        $token =
            trim(
                (string)
                $request->bearerToken()
            );

        if ($token === '') {
            return response()->json(
                [
                    'message' =>
                        'Authentication required.',
                ],
                401,
            );
        }

        try {
            $rows =
                $this->client->rpc(
                    'claim_company_member_action_email',
                    [
                        'p_event_id' =>
                            $id,
                    ],
                    $token,
                );
        } catch (SupabaseException $e) {
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

        $payload =
            isset($rows[0])
            && is_array($rows[0])
                ? $rows[0]
                : null;

        if ($payload === null) {
            return response()->json(
                [
                    'message' =>
                        'Notification payload was not returned.',
                ],
                409,
            );
        }

        if (
            ! (bool) (
                $payload[
                    'should_send'
                ]
                ?? false
            )
        ) {
            return response()->json([
                'ok' => true,
                'already_sent' =>
                    true,
            ]);
        }

        $action =
            trim(
                (string) (
                    $payload[
                        'event_action'
                    ]
                    ?? ''
                )
            );

        if (
            ! in_array(
                $action,
                [
                    'suspended',
                    'reactivated',
                    'removed',
                ],
                true,
            )
        ) {
            $this->releaseClaim(
                $id,
                $token,
            );

            return response()->json(
                [
                    'message' =>
                        'Unsupported member action.',
                ],
                422,
            );
        }

        $recipientEmail =
            trim(
                (string) (
                    $payload[
                        'recipient_email'
                    ]
                    ?? ''
                )
            );

        $recipientName =
            trim(
                (string) (
                    $payload[
                        'recipient_name'
                    ]
                    ?? 'TalentFlow User'
                )
            );

        $companyName =
            trim(
                (string) (
                    $payload[
                        'company_name'
                    ]
                    ?? 'the company'
                )
            );

        if ($recipientEmail === '') {
            $this->releaseClaim(
                $id,
                $token,
            );

            return response()->json(
                [
                    'message' =>
                        'Employee email is missing.',
                ],
                422,
            );
        }

        try {
            Mail::to(
                $recipientEmail
            )->send(
                new CompanyMemberActionMail(
                    recipientName:
                        $recipientName,
                    companyName:
                        $companyName,
                    action:
                        $action,
                )
            );
        } catch (Throwable $e) {
            $this->releaseClaim(
                $id,
                $token,
            );

            Log::error(
                'Company member action email failed.',
                [
                    'event_id' =>
                        $id,
                    'action' =>
                        $action,
                    'exception' =>
                        $e::class,
                ],
            );

            return response()->json(
                [
                    'message' =>
                        'The company action was completed, but the email could not be sent.',
                ],
                503,
            );
        }

        return response()->json([
            'ok' => true,
            'already_sent' =>
                false,
            'action' =>
                $action,
        ]);
    }

    private function releaseClaim(
        string $eventId,
        string $token,
    ): void {
        try {
            $this->client->rpc(
                'release_company_member_action_email',
                [
                    'p_event_id' =>
                        $eventId,
                ],
                $token,
            );
        } catch (Throwable $e) {
            Log::warning(
                'Unable to release company member email claim.',
                [
                    'event_id' =>
                        $eventId,
                    'exception' =>
                        $e::class,
                ],
            );
        }
    }
}
