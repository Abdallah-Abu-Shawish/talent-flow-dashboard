<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that root route requires authentication.
     */
    public function test_root_route_requires_super_admin_authentication(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }
}
