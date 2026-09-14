<?php

namespace Tests\Unit;

use App\Support\SafeDisplay;
use Tests\TestCase;

class SafeDisplayTest extends TestCase
{
    public function test_redacts_bearer_tokens_and_jwts(): void
    {
        $input = 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIn0.signature';
        $sanitized = SafeDisplay::text($input);

        $this->assertStringNotContainsString('eyJhbGci', $sanitized);
        $this->assertStringContainsString('[redacted]', $sanitized);
    }

    public function test_redacts_api_keys_and_passwords(): void
    {
        $input = 'Gemini key AIzaSyD1234567890abcdefghijklmnopqrstuvw and password: secretpassword123';
        $sanitized = SafeDisplay::text($input);

        $this->assertStringNotContainsString('AIzaSyD1234567890', $sanitized);
        $this->assertStringNotContainsString('secretpassword123', $sanitized);
        $this->assertStringContainsString('[redacted]', $sanitized);
    }

    public function test_audit_snapshot_omits_unlisted_sensitive_fields(): void
    {
        $snapshot = [
            'id' => '00000000-0000-0000-0000-000000000001',
            'plan' => 'enterprise',
            'password_hash' => '$2y$12$abcdef123456',
            'secret_token' => 'supersecret',
        ];

        $result = SafeDisplay::auditSnapshot($snapshot);

        $this->assertArrayHasKey('Id', $result);
        $this->assertArrayHasKey('Plan', $result);
        $this->assertArrayNotHasKey('Password hash', $result);
        $this->assertArrayNotHasKey('Secret token', $result);
    }
}
