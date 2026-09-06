<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth_register_title') }} | {{ __('app_name') }}</title>
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-intro auth-intro-register">
            <a class="auth-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/wakr/wakrlogo.png') }}" alt="Wakr">
                <span>{{ __('app_name') }}</span>
            </a>
            <div class="intro-copy">
                <span class="auth-kicker">{{ __('auth_register_kicker') }}</span>
                <h1>{!! __('auth_register_intro_title') !!}</h1>
                <p>{{ __('auth_register_intro_desc') }}</p>
            </div>
            <div class="intro-mark" aria-hidden="true">W</div>
        </section>

        <section class="auth-panel">
            <div class="auth-form-wrap">
                <div class="mobile-brand"><span>Wakr</span><span class="status-dot"></span></div>
                <div class="form-heading">
                    <a class="auth-language" href="{{ app()->getLocale() === 'ar' ? url('/register') : url('/ar/register') }}">{{ app()->getLocale() === 'ar' ? __('auth_switch_to_en') : __('auth_switch_to_ar') }}</a>
                    <span class="eyebrow">{{ __('auth_start_journey') }}</span>
                    <h2>{!! __('auth_create_workspace') !!}</h2>
                    <p>{{ __('auth_register_desc') }}</p>
                </div>

                <form method="POST" action="{{ url('/register') }}" class="auth-form">
                    @csrf
                    <div class="field-group">
                        <label for="name">{{ __('auth_name') }}</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="{{ __('auth_name_placeholder') }}" autocomplete="name" required autofocus>
                        @error('name') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="email">{{ __('auth_email') }}</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('auth_email_placeholder') }}" autocomplete="email" required>
                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="password">{{ __('auth_create_password') }}</label>
                        <input id="password" name="password" type="password" placeholder="{{ __('auth_password_min') }}" autocomplete="new-password" required>
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="password_confirmation">{{ __('auth_confirm_password') }}</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="{{ __('auth_repeat_password') }}" autocomplete="new-password" required>
                    </div>
                    <button class="auth-submit" type="submit">{{ __('auth_create_workspace_button') }} <span aria-hidden="true">&#8594;</span></button>
                </form>

                <p class="auth-switch">{{ __('auth_already_account') }} <a href="{{ app()->getLocale() === 'ar' ? url('/ar/login') : route('login') }}">{{ __('auth_sign_in_link') }}</a></p>
                <p class="auth-legal">{{ __('auth_terms_register') }} <a href="#">{{ __('auth_terms') }}</a> {{ __('auth_privacy') }}.</p>
            </div>
        </section>
    </main>
    @include('components.toast')
</body>
</html>
