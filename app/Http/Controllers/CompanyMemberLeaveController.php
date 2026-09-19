<?php

namespace App\Http\Controllers;

use App\Mail\CompanyMemberLeftMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CompanyMemberLeaveController extends Controller
{
    public function sendLeaveEmail(Request $request): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            [
                'manager_email' => ['required', 'email'],
                'manager_name' => ['required', 'string', 'max:255'],
                'company_name' => ['required', 'string', 'max:255'],
                'member_name' => ['required', 'string', 'max:255'],
                'member_email' => ['required', 'email'],
                'member_role' => ['required', 'string', 'max:255'],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid leave email data.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            Mail::to($request->string('manager_email')->toString())->send(
                new CompanyMemberLeftMail(
                    managerName: $request->string('manager_name')->toString(),
                    companyName: $request->string('company_name')->toString(),
                    memberName: $request->string('member_name')->toString(),
                    memberEmail: $request->string('member_email')->toString(),
                    memberRole: $request->string('member_role')->toString(),
                )
            );

            Log::info('COMPANY MEMBER LEFT EMAIL SENT', [
                'manager_email' => $request->string('manager_email')->toString(),
                'company_name' => $request->string('company_name')->toString(),
                'member_email' => $request->string('member_email')->toString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave notification email sent successfully.',
            ]);
        } catch (Throwable $e) {
            Log::error('COMPANY MEMBER LEFT EMAIL FAILED', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not send leave notification email.',
            ], 500);
        }
    }
}