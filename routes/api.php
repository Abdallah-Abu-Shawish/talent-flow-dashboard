<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CompanyInvitationController;
use App\Http\Controllers\CompanyMemberLeaveController;

Route::post(
    '/company-invitations',
    [CompanyInvitationController::class, 'store']
);

Route::post(
    '/company-memberships/leave-email',
    [CompanyMemberLeaveController::class, 'sendLeaveEmail']
)->name('api.company-memberships.leave-email');