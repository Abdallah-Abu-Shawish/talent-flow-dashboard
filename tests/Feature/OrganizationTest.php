<?php

namespace Tests\Feature;

use Tests\TestCase;

class OrganizationTest extends TestCase
{
    public function test_unauthenticated_user_cannot_access_create_form(): void
    {
        $response = $this->get('/organizations/create');
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_cannot_store_organization(): void
    {
        $response = $this->post('/organizations', [
            'name' => 'Acme Corp',
            'slug' => 'acme-corp',
            'plan' => 'pro',
            'reason' => 'Initial onboarding of Acme Corp',
            'request_id' => '00000000-0000-0000-0000-000000000001',
            'confirmed' => '1',
        ]);

        $response->assertRedirect('/login');
    }
}
