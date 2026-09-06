<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth_login_title') }} | {{ __('app_name') }}</title>
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-intro">
            <a class="auth-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/wakr/wakrlogo.png') }}" alt="Wakr">
                <span>{{ __('app_name') }}</span>
            </a>
            <div class="intro-copy hidden md:block">
                <span class="auth-kicker">{{ __('auth_login_kicker') }}</span>
                <h1>{!! __('auth_login_intro_title') !!}</h1>
                <p>{{ __('auth_login_intro_desc') }}</p>
            </div>
            <div class="intro-mark hidden md:block" aria-hidden="true">W</div>
        </section>

        <section class="auth-panel">
            <div class="auth-form-wrap">
                <div class="mobile-brand"><span>Wakr</span><span class="status-dot"></span></div>
                <div class="form-heading">
                    <span class="eyebrow">{{ __('auth_welcome_back') }}</span>
                    <h2>{!! __('auth_sign_in_workspace') !!}</h2>
                    <p>{{ __('auth_login_desc') }}</p>
                </div>

                @if (session('status'))
                    <div class="auth-notice">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ url('/login') }}" class="auth-form" data-login-form>
                    @csrf
                    <div class="field-group">
                        <label for="email">{{ __('auth_email') }}</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('auth_email_placeholder') }}" autocomplete="email" required autofocus>
                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                        <span class="field-error" data-login-error hidden></span>
                    </div>
                    <div class="field-group">
                        <div class="label-row"><label for="password">{{ __('auth_password') }}</label><a href="#">{{ __('auth_forgot_password') }}</a></div>
                        <input id="password" name="password" type="password" placeholder="{{ __('auth_password_placeholder') }}" autocomplete="current-password" required>
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <label class="check-row"><input type="checkbox" name="remember"><span>{{ __('auth_keep_signed_in') }}</span></label>
                    <button class="auth-submit" type="submit">{{ __('auth_sign_in') }} <span aria-hidden="true">&#8594;</span></button>
                </form>

                <p class="auth-switch">{{ __('auth_new_to_wakr') }} <a href="{{ app()->getLocale() === 'ar' ? url('/ar/register') : route('register') }}">{{ __('auth_create_account') }}</a></p>
                <p class="auth-legal">{{ __('auth_terms_login') }} <a href="#">{{ __('auth_terms') }}</a> {{ __('auth_privacy') }}.</p>
            </div>
        </section>
    </main>
    @include('components.toast')
</body>
</html>
