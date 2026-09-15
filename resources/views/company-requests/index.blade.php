@extends('layouts.app')

@section('title', 'Company Requests')

@section('content')

    <x-page-heading
        title="Company Requests"
        description="Review company creation requests submitted by users."
    />

    @php
        $rows = collect($requests)->map(function ($request) {
            $country = $request['country'] ?? null;
            $city = $request['city'] ?? null;

            $location = collect([
                $city,
                $country,
            ])
                ->filter()
                ->implode(', ');

            $submittedAt = null;

            if (! empty($request['created_at'])) {
                $submittedAt = \Illuminate\Support\Carbon::parse(
                    $request['created_at']
                )->format('Y-m-d H:i');
            }

            return [
    'company' =>
        $request['company_name'] ?? '—',

    'business_email' =>
        $request['business_email'] ?? '—',

    'industry' =>
        $request['industry'] ?? '—',

    'company_size' =>
        $request['company_size'] ?? '—',

    'location' =>
        $location !== ''
            ? $location
            : '—',

    'submitted_at' =>
        $submittedAt ?? '—',

    'status' =>
        ucfirst(
            $request['status'] ?? 'pending'
        ),

    '_url' => route(
        'company-requests.show',
        $request['id']
    ),
];
        })->all();

        $table = [
            'title' => 'Company Requests',

            'description' =>
    count($rows) === 1
        ? '1 company creation request.'
        : count($rows).' company creation requests.',

            'columns' => [
                [
                    'key' => 'company',
                    'label' => 'Company',
                ],
                [
                    'key' => 'business_email',
                    'label' => 'Business Email',
                ],
                [
                    'key' => 'industry',
                    'label' => 'Industry',
                ],
                [
                    'key' => 'company_size',
                    'label' => 'Company Size',
                ],
                [
                    'key' => 'location',
                    'label' => 'Location',
                ],
                [
                    'key' => 'submitted_at',
                    'label' => 'Submitted',
                ],
                [
                    'key' => 'status',
                    'label' => 'Status',
                    'type' => 'badge',
                ],
            ],

            'rows' => $rows,
        ];
    @endphp


    @if($loadError)

        <section class="card">
            <div class="card-heading">
                <div>
                    <h2>Unable to load requests</h2>

                    <p>
                        {{ $loadError }}
                    </p>
                </div>
            </div>
        </section>

    @else

        <x-data-table
            :table="$table"
            :filters="false"
        />

    @endif

@endsection