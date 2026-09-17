<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="color-scheme" content="light">

    <title>@yield('title', 'Dashboard') · TalentFlow AI</title>

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo.png') }}"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])



</head>


<body>

    <a
        class="skip-link"
        href="#main-content"
    >
        Skip to content
    </a>


    <div class="dashboard-shell">

        {{-- =====================================================
             SIDEBAR
        ====================================================== --}}

        <aside
            class="sidebar"
            id="sidebar"
            aria-label="Administration"
        >

            {{-- BRAND --}}
            <a
                class="brand"
                href="{{ route('overview') }}"
                aria-label="TalentFlow AI overview"
            >

                <span class="brand-symbol">

                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="TalentFlow AI"
                        style="
                            width: 100%;
                            height: 100%;
                            object-fit: contain;
                            display: block;
                        "
                    >

                </span>


                <span class="brand-copy">

                    <strong>
                        TalentFlow <span>AI</span>
                    </strong>

                    <small>
                        Admin Console
                    </small>

                </span>

            </a>


            {{-- WORKSPACE --}}
            <div class="workspace">

                <span class="workspace-avatar">
                    G
                </span>

                <span class="sidebar-label">

                    Global workspace

                    <small>
                        Platform administration
                    </small>

                </span>

                <x-icon name="lock" />

            </div>


            {{-- =================================================
                 NAVIGATION
            ================================================== --}}

            <nav
                class="main-navigation"
                aria-label="Main navigation"
            >

                <p class="nav-caption sidebar-label">
                    WORKSPACE
                </p>


                {{-- OVERVIEW --}}
                <a
                    href="{{ route('overview') }}"
                    @class([
                        'nav-link',
                        'active' => request()->routeIs('overview'),
                    ])
                    title="Overview"
                    @if(request()->routeIs('overview'))
                        aria-current="page"
                    @endif
                >

                    <x-icon name="grid" />

                    <span class="sidebar-label">
                        Overview
                    </span>

                    <x-icon
                        name="chevron"
                        class="nav-chevron"
                    />

                </a>


                {{-- =================================================
                     ORGANIZATIONS GROUP
                ================================================== --}}

                @php
                    $organizationsActive =
                        request()->routeIs('organizations.*');

                    $companyRequestsActive =
                        request()->routeIs('company-requests.*');

                    $organizationsGroupActive =
                        $organizationsActive
                        || $companyRequestsActive;
                @endphp


                <div
                    @class([
                        'sidebar-nav-group',
                        'active' => $organizationsGroupActive,
                    ])
                >

                    {{-- ORGANIZATIONS --}}
                    <a
                        href="{{ route('organizations.index') }}"
                        @class([
                            'nav-link',
                            'active' => $organizationsActive,
                        ])
                        title="Organizations"
                        @if($organizationsActive)
                            aria-current="page"
                        @endif
                    >

                        <x-icon name="building" />

                        <span class="sidebar-label">
                            Organizations
                        </span>

                        @if(! $companyRequestsActive)
                            <x-icon
                                name="chevron"
                                class="nav-chevron"
                            />
                        @endif

                    </a>


                    {{-- COMPANY REQUESTS CHILD --}}
                    <div class="sidebar-subnav sidebar-label">

                        <a
                            href="{{ route('company-requests.index') }}"
                            @class([
                                'sidebar-subnav-link',
                                'active' => $companyRequestsActive,
                            ])
                            @if($companyRequestsActive)
                                aria-current="page"
                            @endif
                        >

                            <span
                                class="sidebar-subnav-node"
                                aria-hidden="true"
                            ></span>

                            <span>
                                Company Requests
                            </span>

                            @if($companyRequestsActive)
                                <x-icon
                                    name="chevron"
                                    class="sidebar-subnav-chevron"
                                />
                            @endif

                        </a>

                    </div>

                </div>


                {{-- USERS --}}
                <a
                    href="{{ route('users.index') }}"
                    @class([
                        'nav-link',
                        'active' => request()->routeIs('users.*'),
                    ])
                    title="Users & access"
                >

                    <x-icon name="users" />

                    <span class="sidebar-label">
                        Users & access
                    </span>

                    <x-icon
                        name="chevron"
                        class="nav-chevron"
                    />

                </a>


                {{-- =================================================
                     JOBS GROUP
                ================================================== --}}

                @php
                    $jobsActive =
                        request()->routeIs('jobs.*');

                    $jobCategoriesActive =
                        request()->routeIs('job-categories.*')
                        || request()->is('job-categories*');

                    $jobsGroupActive =
                        $jobsActive
                        || $jobCategoriesActive;
                @endphp


                <div
                    @class([
                        'sidebar-nav-group',
                        'active' => $jobsGroupActive,
                    ])
                >

                    {{-- JOBS --}}
                    <a
                        href="{{ route('jobs.index') }}"
                        @class([
                            'nav-link',
                            'active' => $jobsActive,
                        ])
                        title="Jobs"
                        @if($jobsActive)
                            aria-current="page"
                        @endif
                    >

                        <x-icon name="briefcase" />

                        <span class="sidebar-label">
                            Jobs
                        </span>

                        @if(! $jobCategoriesActive)
                            <x-icon
                                name="chevron"
                                class="nav-chevron"
                            />
                        @endif

                    </a>


                    {{-- JOB CATEGORIES CHILD --}}
                    <div class="sidebar-subnav sidebar-label">

                        <a
                            href="{{ route('job-categories.index') }}"
                            @class([
                                'sidebar-subnav-link',
                                'active' => $jobCategoriesActive,
                            ])
                            @if($jobCategoriesActive)
                                aria-current="page"
                            @endif
                        >

                            <span
                                class="sidebar-subnav-node"
                                aria-hidden="true"
                            ></span>

                            <span>
                                Job Categories
                            </span>

                            @if($jobCategoriesActive)
                                <x-icon
                                    name="chevron"
                                    class="sidebar-subnav-chevron"
                                />
                            @endif

                        </a>

                    </div>

                </div>


                {{-- AI INTERVIEWS --}}
                <a
                    href="{{ route('interviews.index') }}"
                    @class([
                        'nav-link',
                        'active' => request()->routeIs('interviews.*'),
                    ])
                    title="AI interviews"
                >

                    <x-icon name="interview" />

                    <span class="sidebar-label">
                        AI interviews
                    </span>

                    <x-icon
                        name="chevron"
                        class="nav-chevron"
                    />

                </a>


                {{-- USAGE --}}
                <a
                    href="{{ route('usage.index') }}"
                    @class([
                        'nav-link',
                        'active' => request()->routeIs('usage.*'),
                    ])
                    title="Usage & tokens"
                >

                    <x-icon name="coins" />

                    <span class="sidebar-label">
                        Usage & tokens
                    </span>

                    <x-icon
                        name="chevron"
                        class="nav-chevron"
                    />

                </a>


                {{-- =================================================
                     OPERATIONS
                ================================================== --}}

                <p class="nav-caption sidebar-label">
                    OPERATIONS
                </p>


                {{-- SYSTEM HEALTH --}}
                <a
                    href="{{ route('health') }}"
                    @class([
                        'nav-link',
                        'active' => request()->routeIs('health'),
                    ])
                    title="System health"
                >

                    <x-icon name="activity" />

                    <span class="sidebar-label">
                        System health
                    </span>

                    <x-icon
                        name="chevron"
                        class="nav-chevron"
                    />

                </a>


                {{-- AUDIT --}}
                <a
                    href="{{ route('audit.index') }}"
                    @class([
                        'nav-link',
                        'active' => request()->routeIs('audit.*'),
                    ])
                    title="Audit logs"
                >

                    <x-icon name="audit" />

                    <span class="sidebar-label">
                        Audit logs
                    </span>

                    <x-icon
                        name="chevron"
                        class="nav-chevron"
                    />

                </a>


                {{-- SECURITY --}}
                <a
                    href="{{ route('security.index') }}"
                    @class([
                        'nav-link',
                        'active' => request()->routeIs('security.*'),
                    ])
                    title="Security"
                >

                    <x-icon name="shield" />

                    <span class="sidebar-label">
                        Security
                    </span>

                    <x-icon
                        name="chevron"
                        class="nav-chevron"
                    />

                </a>


                {{-- SETTINGS --}}
                <a
                    href="{{ route('settings') }}"
                    @class([
                        'nav-link',
                        'active' => request()->routeIs('settings'),
                    ])
                    title="Settings"
                >

                    <x-icon name="settings" />

                    <span class="sidebar-label">
                        Settings
                    </span>

                    <x-icon
                        name="chevron"
                        class="nav-chevron"
                    />

                </a>

            </nav>


            {{-- =================================================
                 SIDEBAR FOOTER
            ================================================== --}}

            <div class="sidebar-footer">

                <div class="admin-identity">

                    <span class="avatar">

                        {{
                            mb_strtoupper(
                                mb_substr(
                                    session('admin.profile.full_name')
                                    ?: session(
                                        'admin.profile.email',
                                        'A'
                                    ),
                                    0,
                                    1
                                )
                            )
                        }}

                    </span>


                    <span class="sidebar-label">

                        <strong>
                            {{
                                session('admin.profile.full_name')
                                ?: 'Super admin'
                            }}
                        </strong>

                        <small
                            title="{{ session('admin.profile.email') }}"
                        >
                            {{ session('admin.profile.email') }}
                        </small>

                    </span>

                </div>


                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    data-loading-form
                >

                    @csrf

                    <button
                        type="submit"
                        class="nav-link logout-button"
                        title="Sign out"
                    >

                        <x-icon name="logout" />

                        <span class="sidebar-label">
                            Sign out
                        </span>

                    </button>

                </form>

            </div>

        </aside>


        <div
            class="sidebar-backdrop"
            data-sidebar-close
            hidden
        ></div>


        {{-- =====================================================
             MAIN WORKSPACE
        ====================================================== --}}

        <div class="workspace-main">

            <header class="topbar">

                <button
                    type="button"
                    class="icon-button"
                    data-sidebar-toggle
                    aria-controls="sidebar"
                    aria-expanded="true"
                    aria-label="Toggle navigation"
                >
                    <x-icon name="menu" />
                </button>


                <div class="breadcrumb">

                    <span>
                        Workspace
                    </span>

                    <x-icon name="chevron" />

                    <strong>
                        @yield('title', 'Dashboard')
                    </strong>

                </div>


                <div class="header-actions">

                    <a
                        class="access-label"
                        href="{{ route('settings') }}"
                    >

                        <x-icon name="shield" />

                        <span>
                            Super admin
                        </span>

                    </a>


                    <button
                        class="icon-button"
                        type="button"
                        data-refresh
                        aria-label="Refresh page"
                        title="Refresh data"
                    >
                        <x-icon name="refresh" />
                    </button>

                </div>

            </header>


            <main
                id="main-content"
                class="page-content"
                tabindex="-1"
            >

                <x-feedback />

                @yield('content')


                <footer class="page-footer">

                    <span>

                        TalentFlow AI

                        <span class="footer-dot">
                            ·
                        </span>

                        System Dashboard

                    </span>

                    <span>
                        All timestamps in UTC
                    </span>

                </footer>

            </main>

        </div>

    </div>


    <div
        class="loading-indicator"
        role="status"
        aria-live="polite"
        hidden
    >
        Loading latest data…
    </div>


    <dialog
        id="confirmation-dialog"
        aria-labelledby="confirmation-title"
        aria-describedby="confirmation-description"
    >

        <div class="dialog-icon">
            <x-icon name="shield" />
        </div>

        <h2 id="confirmation-title">
            Confirm organization change
        </h2>

        <p id="confirmation-description">
            This change will be saved to the organization and recorded
            in the audit history with your identity and reason.
        </p>


        <div class="dialog-actions">

            <button
                type="button"
                class="button button-secondary"
                data-confirm-cancel
            >
                Keep editing
            </button>

            <button
                type="button"
                class="button button-primary"
                data-confirm-accept
            >
                Confirm and save
            </button>

        </div>

    </dialog>



</body>

</html>