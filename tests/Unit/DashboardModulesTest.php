<?php

namespace Tests\Unit;

use App\Support\DashboardModules;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class DashboardModulesTest extends TestCase
{
    public function test_all_expected_modules_are_defined(): void
    {
        $modules = [
            'organizations',
            'users',
            'jobs',
            'interviews',
            'usage',
            'credits',
            'audit',
            'commands',
            'security',
            'auth-events',
            'members',
        ];

        foreach ($modules as $module) {
            $definition = DashboardModules::get($module);
            $this->assertArrayHasKey('table', $definition);
            $this->assertArrayHasKey('title', $definition);
            $this->assertArrayHasKey('columns', $definition);
            $this->assertArrayHasKey('select', $definition);
        }
    }

    public function test_invalid_module_throws_404(): void
    {
        $this->expectException(NotFoundHttpException::class);
        DashboardModules::get('non_existent_module');
    }
}
