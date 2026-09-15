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

    <a class="skip-link" href="#main-content">
        Skip to content
    </a>

    <div class="dashboard-shell">

        <aside
            class="sidebar"
            id="sidebar"
            aria-label="Administration"
        >

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
                    <small>Admin Console</small>
                </span>

            </a>

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

            <nav
                class="main-navigation"
                aria-label="Main navigation"
            >

                @php
                    $navigation = [
                        ['overview', 'Overview', 'grid', 'overview'],
                        ['organizations.index', 'Organizations', 'building', 'organizations.*'],
                        ['company-requests.index', 'Company Requests', 'building', 'company-requests.*'],
                        ['users.index', 'Users & access', 'users', 'users.*'],
                        ['jobs.index', 'Jobs', 'briefcase', 'jobs.*'],
                        ['interviews.index', 'AI interviews', 'interview', 'interviews.*'],
                        ['usage.index', 'Usage & tokens', 'coins', 'usage.*'],
                        ['health', 'System health', 'activity', 'health'],
                        ['audit.index', 'Audit logs', 'audit', 'audit.*'],
                        ['security.index', 'Security', 'shield', 'security.*'],
                        ['settings', 'Settings', 'settings', 'settings'],
                    ];
                @endphp

                <p class="nav-caption sidebar-label">
                    WORKSPACE
                </p>

                @foreach ($navigation as [$destination, $label, $icon, $pattern])

                    @if ($loop->index === 7)
                        <p class="nav-caption sidebar-label">
                            OPERATIONS
                        </p>
                    @endif

                    <a
                        href="{{ route($destination) }}"
                        @class([
                            'nav-link',
                            'active' => request()->routeIs($pattern),
                        ])
                        title="{{ $label }}"
                        @if(request()->routeIs($pattern))
                            aria-current="page"
                        @endif
                    >

                        <x-icon :name="$icon" />

                        <span class="sidebar-label">
                            {{ $label }}
                        </span>

                        <x-icon
                            name="chevron"
                            class="nav-chevron"
                        />

                    </a>

                @endforeach

            </nav>

            <div class="sidebar-footer">

                <div class="admin-identity">

                    <span class="avatar">
                        {{
                            mb_strtoupper(
                                mb_substr(
                                    session('admin.profile.full_name')
                                    ?: session('admin.profile.email', 'A'),
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
                        <span class="footer-dot">·</span>
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
            This change will be saved to the organization and recorded in the audit history with your identity and reason.
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