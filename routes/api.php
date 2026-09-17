<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CompanyInvitationController;

Route::post(
    '/company-invitations',
    [CompanyInvitationController::class, 'store']
);