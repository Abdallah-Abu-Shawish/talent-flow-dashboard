<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">

    <title>Sign in · TalentFlow AI</title>

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo.png') }}"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="auth-body">

<div class="auth-layout auth-layout-clean">

    <!-- LEFT SIDE -->
    <aside class="auth-brand-panel auth-brand-panel-clean">

        <div class="auth-side-brand">

            <img
                src="{{ asset('images/logo2.png') }}"
                alt="TalentFlow AI"
                class="auth-side-logo"
            >

            <div>
                <strong>
                    TalentFlow <span>AI</span>
                </strong>

                <small>
                    Admin Console
                </small>
            </div>

        </div>


        <div class="auth-intro-clean">

            <span class="auth-small-label">
                TALENTFLOW AI
            </span>

            <h1>
                Hire smarter.<br>
                Decide faster.
            </h1>

            <p>
                Manage your TalentFlow AI platform from one
                simple and secure workspace.
            </p>


            <div class="auth-features">

                <div class="auth-feature">
                    <span class="auth-feature-icon">
                        <x-icon name="building" />
                    </span>

                    <div>
                        <strong>Organizations</strong>
                        <small>
                            Manage companies and platform access
                        </small>
                    </div>
                </div>


                <div class="auth-feature">
                    <span class="auth-feature-icon">
                        <x-icon name="interview" />
                    </span>

                    <div>
                        <strong>AI Interviews</strong>
                        <small>
                            Monitor interview activity
                        </small>
                    </div>
                </div>


                <div class="auth-feature">
                    <span class="auth-feature-icon">
                        <x-icon name="activity" />
                    </span>

                    <div>
                        <strong>Platform Insights</strong>
                        <small>
                            Track usage and system activity
                        </small>
                    </div>
                </div>

            </div>

        </div>


        <div class="auth-panel-footer-clean">
            <x-icon name="shield" />

            <span>
                Secure access for authorized administrators
            </span>
        </div>

    </aside>


    <!-- RIGHT SIDE -->
    <main class="auth-main auth-main-clean" id="main-content">

        <div class="login-card login-card-clean">

            <div class="login-logo-mobile">
                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="TalentFlow AI"
                >
            </div>


            <span class="login-badge">
                ADMIN PORTAL
            </span>

            <h2>
                Welcome back
            </h2>

            <p class="login-description">
                Sign in to continue to your administrator dashboard.
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
                        placeholder="you@example.com"
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
                            placeholder="Enter your password"
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
                Super administrator access only.
            </p>

        </div>


        <footer class="auth-footer">
            © {{ date('Y') }} TalentFlow AI
            <span>·</span>
            Admin Console
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