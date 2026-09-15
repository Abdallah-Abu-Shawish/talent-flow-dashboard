<?php

namespace App\Http\Controllers;

use App\Exceptions\SupabaseException;
use App\Services\Supabase\Client;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\CompanyRequestStatusMail;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class CompanyRequestController extends Controller
{
    public function __construct(
        private Client $client,
    ) {}

    // =========================================================
    // LIST ALL COMPANY REQUESTS
    // =========================================================

    public function index(): View
    {
        $requests = [];
        $loadError = null;

        try {
            $response = $this->client->serviceSelect(
                'company_creation_requests',
                [
                    'select' => implode(',', [
                        'id',
                        'requested_by',
                        'company_name',
                        'requested_slug',
                        'business_email',
                        'phone_number',
                        'website',
                        'industry',
                        'company_size',
                        'country',
                        'city',
                        'description',
                        'status',
                        'reviewed_by',
                        'review_note',
                        'reviewed_at',
                        'company_id',
                        'created_at',
                        'updated_at',
                    ]),
                    'order' => 'created_at.desc',
                ],
            );

            $data = $response->json();

            $requests =
                is_array($data)
                    ? $data
                    : [];
        } catch (SupabaseException $e) {
            Log::warning(
                'Unable to load company creation requests.',
                [
                    'status' => $e->status ?? null,
                ],
            );

            $loadError =
                'Unable to load company requests right now.';
        }

        return view(
            'company-requests.index',
            [
                'requests' => $requests,
                'loadError' => $loadError,
            ],
        );
    }


    // =========================================================
    // SHOW REQUEST DETAILS
    // =========================================================

    public function show(string $id): View
    {
        if (! Str::isUuid($id)) {
            abort(404);
        }

        try {
            // =================================================
            // COMPANY REQUEST
            // =================================================

            $requestRows = $this->client->serviceSelect(
                'company_creation_requests',
                [
                    'select' => implode(',', [
                        'id',
                        'requested_by',
                        'company_name',
                        'requested_slug',
                        'business_email',
                        'phone_number',
                        'website',
                        'industry',
                        'company_size',
                        'country',
                        'city',
                        'description',
                        'status',
                        'reviewed_by',
                        'review_note',
                        'reviewed_at',
                        'company_id',
                        'created_at',
                        'updated_at',
                    ]),
                    'id' => 'eq.'.$id,
                    'limit' => 1,
                ],
            )->json();

            $companyRequest =
                is_array($requestRows)
                    ? ($requestRows[0] ?? null)
                    : null;

            if (! is_array($companyRequest)) {
                abort(404);
            }

            // =================================================
            // REQUESTER PROFILE
            // =================================================

            $requesterRows = $this->client->serviceSelect(
                'profiles',
                [
                    'select' => implode(',', [
                        'id',
                        'full_name',
                        'email',
                        'phone_number',
                        'role',
                        'company_request_enabled',
                    ]),
                    'id' => 'eq.'.$companyRequest['requested_by'],
                    'limit' => 1,
                ],
            )->json();

            $requester =
                is_array($requesterRows)
                    ? ($requesterRows[0] ?? [])
                    : [];

            return view(
                'company-requests.show',
                [
                    'companyRequest' => $companyRequest,
                    'requester' => $requester,
                ],
            );
        } catch (SupabaseException $e) {
            Log::warning(
                'Unable to load company creation request details.',
                [
                    'request_id' => $id,
                    'status' => $e->status ?? null,
                ],
            );

            abort(503);
        }
    }


    // =========================================================
    // APPROVE REQUEST
    // =========================================================

    public function approve(
        Request $request,
        string $id,
    ): RedirectResponse {
        if (! Str::isUuid($id)) {
            abort(404);
        }

        $token = $request->session()->get(
            'admin.access_token'
        );

        if (! is_string($token) || $token === '') {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Your admin session has expired. Please sign in again.'
                );
        }

        try {
            $result = $this->client->rpc(
                'approve_company_creation_request',
                [
                    'p_request_id' => $id,
                    'p_review_note' => null,
                ],
                $token,
            );

            if (($result['success'] ?? false) !== true) {
                return redirect()
                    ->route(
                        'company-requests.show',
                        $id
                    )
                    ->with(
                        'error',
                        'The company request could not be approved.'
                    );
            }
            $this->sendCompanyRequestStatusEmail(
    requestId: $id,
    status: 'approved',
);

            return redirect()
                ->route(
                    'company-requests.show',
                    $id
                )
                ->with(
                    'success',
                    'Company request approved successfully. The company was created and the requester was assigned as Company Admin.'
                );
        } catch (SupabaseException $e) {
            Log::warning(
                'Unable to approve company creation request.',
                [
                    'request_id' => $id,
                    'status' => $e->status ?? null,
                ],
            );

            $message = match ($e->status ?? null) {
                401 =>
                    'Your admin session has expired. Please sign in again.',

                403 =>
                    'You are not authorized to approve this request.',

                404 =>
                    'The company request could not be found.',

                409 =>
                    'This request cannot be approved. It may already have been reviewed or the company slug may already exist.',

                422 =>
                    'The company request contains invalid data.',

                default =>
                    'Unable to approve the company request right now.',
            };

            return redirect()
                ->route(
                    'company-requests.show',
                    $id
                )
                ->with(
                    'error',
                    $message
                );
        }
    }


    // =========================================================
    // REJECT REQUEST
    // =========================================================

    public function reject(
        Request $request,
        string $id,
    ): RedirectResponse {
        if (! Str::isUuid($id)) {
            abort(404);
        }

        $validated = $request->validate([
            'review_note' => [
                'required',
                'string',
                'min:3',
                'max:1000',
            ],
        ]);

        $token = $request->session()->get(
            'admin.access_token'
        );

        if (! is_string($token) || $token === '') {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Your admin session has expired. Please sign in again.'
                );
        }

        try {
            $result = $this->client->rpc(
                'reject_company_creation_request',
                [
                    'p_request_id' => $id,
                    'p_review_note' =>
                        trim($validated['review_note']),
                ],
                $token,
            );

            if (($result['success'] ?? false) !== true) {
                return redirect()
                    ->route(
                        'company-requests.show',
                        $id
                    )
                    ->with(
                        'error',
                        'The company request could not be rejected.'
                    );
            }

            return redirect()
                ->route(
                    'company-requests.show',
                    $id
                )
                ->with(
                    'success',
                    'Company request rejected successfully.'
                );
        } catch (SupabaseException $e) {
            Log::warning(
                'Unable to reject company creation request.',
                [
                    'request_id' => $id,
                    'status' => $e->status ?? null,
                ],
            );

            $message = match ($e->status ?? null) {
                401 =>
                    'Your admin session has expired. Please sign in again.',

                403 =>
                    'You are not authorized to reject this request.',

                404 =>
                    'The company request could not be found.',

                409 =>
                    'This request cannot be rejected. It may already have been reviewed.',

                422 =>
                    'Please provide a valid rejection reason.',

                default =>
                    'Unable to reject the company request right now.',
            };

            return redirect()
                ->route(
                    'company-requests.show',
                    $id
                )
                ->with(
                    'error',
                    $message
                );
        }
    }

    private function sendCompanyRequestStatusEmail(
    string $requestId,
    string $status,
    ?string $reviewNote = null,
): void {
    try {
        // Get company request
        $requestRows = $this->client->serviceSelect(
            'company_creation_requests',
            [
                'select' => 'requested_by,company_name',
                'id' => 'eq.'.$requestId,
                'limit' => 1,
            ],
        )->json();

        $companyRequest =
            is_array($requestRows)
                ? ($requestRows[0] ?? null)
                : null;

        if (! is_array($companyRequest)) {
            Log::warning(
                'Company request email skipped: request not found.',
                [
                    'request_id' => $requestId,
                ],
            );

            return;
        }

        $requesterId =
            $companyRequest['requested_by'] ?? null;

        $companyName =
            $companyRequest['company_name'] ?? 'Company';

        if (! is_string($requesterId) || $requesterId === '') {
            Log::warning(
                'Company request email skipped: requester missing.',
                [
                    'request_id' => $requestId,
                ],
            );

            return;
        }

        // Get requester email
        $profileRows = $this->client->serviceSelect(
            'profiles',
            [
                'select' => 'email',
                'id' => 'eq.'.$requesterId,
                'limit' => 1,
            ],
        )->json();

        $profile =
            is_array($profileRows)
                ? ($profileRows[0] ?? null)
                : null;

        $email =
            is_array($profile)
                ? ($profile['email'] ?? null)
                : null;

        if (
            ! is_string($email)
            || ! filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            Log::warning(
                'Company request email skipped: valid requester email not found.',
                [
                    'request_id' => $requestId,
                    'requester_id' => $requesterId,
                ],
            );

            return;
        }

        Mail::to($email)->send(
            new CompanyRequestStatusMail(
                status: $status,
                companyName: $companyName,
                reviewNote: $reviewNote,
            ),
        );
    } catch (Throwable $e) {
        // Email failure must NOT undo approve/reject.
        Log::warning(
            'Unable to send company request status email.',
            [
                'request_id' => $requestId,
                'status' => $status,
                'exception' => $e::class,
            ],
        );
    }
}
}