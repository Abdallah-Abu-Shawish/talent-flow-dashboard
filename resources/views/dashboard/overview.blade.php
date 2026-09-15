@extends('layouts.app')

@section('title', 'Overview')

@section('content')

    <section class="overview-header">
        <div>
            <span class="overview-eyebrow">
                TALENTFLOW AI
            </span>

            <h1>
                Welcome to your dashboard
            </h1>

            <p>
                A simple overview of your platform,
                users and AI interviews.
            </p>
        </div>

        <a
            class="overview-primary-action"
            href="{{ route('usage.index', $filters) }}"
        >
            View usage
            <x-icon name="arrow" />
        </a>
    </section>


    <x-date-filters
        :filters="$filters"
        :action="route('overview')"
    />


    <div class="overview-metrics">

        <a
            href="{{ route('organizations.index') }}"
            class="overview-metric"
        >
            <div class="overview-metric-icon">
                <x-icon name="building" />
            </div>

            <div>
                <span>Organizations</span>

                <strong>
                    {{
                        isset($summary['organizations'])
                            ? number_format($summary['organizations'])
                            : '—'
                    }}
                </strong>

                <small>
                    Total organizations
                </small>
            </div>
        </a>


        <a
            href="{{ route('users.index') }}"
            class="overview-metric"
        >
            <div class="overview-metric-icon">
                <x-icon name="users" />
            </div>

            <div>
                <span>Users</span>

                <strong>
                    {{
                        isset($summary['users'])
                            ? number_format($summary['users'])
                            : '—'
                    }}
                </strong>

                <small>
                    Registered users
                </small>
            </div>
        </a>


        <a
            href="{{ route('interviews.index') }}"
            class="overview-metric"
        >
            <div class="overview-metric-icon">
                <x-icon name="interview" />
            </div>

            <div>
                <span>AI Interviews</span>

                <strong>
                    {{
                        isset($summary['interviews'])
                            ? number_format($summary['interviews'])
                            : '—'
                    }}
                </strong>

                <small>
                    Selected period
                </small>
            </div>
        </a>


        <a
            href="{{ route('usage.index', $filters) }}"
            class="overview-metric"
        >
            <div class="overview-metric-icon overview-metric-icon-cyan">
                <x-icon name="coins" />
            </div>

            <div>
                <span>Tokens Used</span>

                <strong>
                    {{
                        isset($summary['tokens'])
                            ? number_format($summary['tokens'])
                            : '—'
                    }}
                </strong>

                <small>
                    AI token consumption
                </small>
            </div>
        </a>

    </div>


    <div class="overview-analytics">

        <x-usage-chart
            :daily="$summary['daily'] ?? []"
        />

        <x-usage-breakdown
            :rows="$summary['by_service'] ?? []"
            title="Usage by service"
            label-key="service"
            description="AI services consumption"
        />

    </div>


    <section class="overview-interviews">

        <div class="overview-section-title">

            <div>
                <h2>
                    Interview activity
                </h2>

                <p>
                    Current AI interview activity
                </p>
            </div>

            <a href="{{ route('interviews.index') }}">
                View all
                <x-icon name="arrow" />
            </a>

        </div>


        <div class="overview-activity-grid">

            <div>
                <span>Completed</span>

                <strong>
                    {{
                        isset($summary['completed'])
                            ? number_format($summary['completed'])
                            : '—'
                    }}
                </strong>

                <small>
                    Completed interviews
                </small>
            </div>


            <div>
                <span>In progress</span>

                <strong>
                    {{
                        isset($summary['in_progress'])
                            ? number_format($summary['in_progress'])
                            : '—'
                    }}
                </strong>

                <small>
                    Active interviews
                </small>
            </div>


            <div>
                <span>Integrity events</span>

                <strong>
                    {{
                        isset($summary['integrity_events'])
                            ? number_format($summary['integrity_events'])
                            : '—'
                    }}
                </strong>

                <small>
                    Selected period
                </small>
            </div>

        </div>

    </section>

@endsection