<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="color-scheme" content="light dark">

    <title>Sign in · TalentFlow AI</title>

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo.png') }}"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="auth-body">

    <div class="auth-layout">

        <aside class="auth-brand-panel">

            <!-- Brand -->
            <div class="brand">

                <span class="brand-symbol">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="TalentFlow AI"
                        class="auth-brand-logo"
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

            </div>

         
            <!-- Intro -->
            <div class="auth-intro">

                <span class="eyebrow">
                    PLATFORM ADMINISTRATION
                </span>

                <h1>
                    A clearer view of<br>
                    your interview platform.
                </h1>

                <p>
                    One workspace for organizations,
                    interview operations and accountable administration.
                </p>

                <div class="auth-capabilities">

                    <span>
                        <x-icon name="building" />
                        Organization oversight
                    </span>

                    <span>
                        <x-icon name="activity" />
                        Recorded platform activity
                    </span>

                    <span>
                        <x-icon name="shield" />
                        Auditable administration
                    </span>

                </div>

            </div>

            <div class="auth-panel-footer">
                <x-icon name="lock" />

                <span>
                    Restricted to authorized super administrators
                </span>
            </div>

        </aside>

        <main
            class="auth-main"
            id="main-content"
        >

            <button
                class="icon-button auth-theme"
                type="button"
                data-theme-toggle
                aria-label="Switch to dark theme"
                title="Switch theme"
            >
                <x-icon name="moon" />
            </button>

            <div class="login-card">

                <span class="login-symbol">
                    <x-icon name="lock" />
                </span>

                <h2>
                    Welcome back
                </h2>

                <p class="login-description">
                    Sign in to your administrator workspace.
                </p>

                <x-feedback />

                @if(!$configured)

                    <div
                        class="alert alert-warning"
                        role="status"
                    >

                        <x-icon name="info" />

                        <span>
                            Dashboard authentication is not configured.
                            Ask your system administrator to configure
                            the Supabase connection.
                        </span>

                    </div>

                @endif

                <form
                    method="POST"
                    action="{{ route('login.store') }}"
                    data-loading-form
                >

                    @csrf

                    <div class="form-field">

                        <label for="email">
                            Email address
                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="username"
                            maxlength="254"
                            required
                            autofocus
                            @disabled(!$configured)
                            @error('email')
                                aria-invalid="true"
                            @enderror
                        >

                    </div>

                    <div class="form-field">

                        <label for="password">
                            Password
                        </label>

                        <div class="password-input">

                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                @disabled(!$configured)
                            >

                            <button
                                type="button"
                                data-password-toggle
                                aria-controls="password"
                                aria-label="Show password"
                                @disabled(!$configured)
                            >
                                Show
                            </button>

                        </div>

                    </div>

                    <button
                        type="submit"
                        class="button button-primary login-submit"
                        @disabled(!$configured)
                    >

                        Sign in

                        <x-icon name="arrow" />

                    </button>

                </form>

                <p class="login-help">

                    <x-icon name="shield" />

                    Use the Supabase account assigned a super-admin role.

                </p>

            </div>

            <footer class="auth-footer">
                TalentFlow AI
                <span>·</span>
                System Dashboard
            </footer>

        </main>

    </div>

    <div
        class="loading-indicator"
        role="status"
        aria-live="polite"
        hidden
    >
        Signing in…
    </div>

</body>

</html>