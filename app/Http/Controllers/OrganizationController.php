<?php

namespace App\Http\Controllers;

use App\Exceptions\SupabaseException;
use App\Http\Requests\OrganizationRequest;
use App\Repositories\DashboardRepository;
use App\Services\AuthEventRecorder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class OrganizationController extends Controller
{
    public function create(): mixed
    {
        return view('organizations.form', ['organization' => null, 'requestId' => (string) Str::uuid()]);
    }

    public function edit(Request $request, string $id, DashboardRepository $repository): mixed
    {
        return view('organizations.form', [
            'organization' => $repository->find('organizations', $id, $request->session()->get('admin.access_token')),
            'requestId' => (string) Str::uuid(),
        ]);
    }

    public function store(OrganizationRequest $request, DashboardRepository $repository, AuthEventRecorder $events): mixed
    {
        return $this->save($request, $repository, $events, null);
    }

    public function update(OrganizationRequest $request, string $id, DashboardRepository $repository, AuthEventRecorder $events): mixed
    {
        return $this->save($request, $repository, $events, $id);
    }

    private function save(OrganizationRequest $request, DashboardRepository $repository, AuthEventRecorder $events, ?string $id): mixed
    {
        try {
            $organization = $repository->saveOrganization($request->validated(), $id, $request->session()->get('admin.access_token'));
        } catch (SupabaseException $e) {
            $events->record('action_failed', 'failure', $request->attributes->get('admin_profile')['id']);
            if (in_array($e->status, [409, 422])) {
                return back()->withInput($request->safe()->except('confirmed'))->withErrors(['organization' => $e->getMessage()]);
            }
            throw $e;
        }
        if (! Str::isUuid($organization['id'] ?? '')) {
            throw new SupabaseException;
        }
        return redirect()->route('organizations.show', $organization['id'])->with('status', 'Organization saved. The change and your reason have been recorded.');
    }
}
