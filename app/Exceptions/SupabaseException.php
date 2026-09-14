<?php

namespace App\Exceptions;

use RuntimeException;

final class SupabaseException extends RuntimeException
{
    public function __construct(public readonly int $status = 503, public readonly int $retryAfter = 30)
    {
        parent::__construct(match ($status) {
            401 => 'Your session has expired. Please sign in again.',
            403 => 'This action requires current super-admin access.',
            404 => 'The requested record no longer exists.',
            409 => 'The record changed or this request conflicts with an earlier action. Reload it before trying again.',
            422 => 'The change did not satisfy the data contract. Review the form and try again.',
            429 => 'The data service is busy. Wait before trying again.',
            default => 'The data service is unavailable or its dashboard contract is not installed. Please try again later.',
        });
    }
}
