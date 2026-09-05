<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create account | {{ __('app_name') }}</title>
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
                <span class="auth-kicker">A clearer way to care</span>
                <h1>Make room for<br><em>what matters.</em></h1>
                <p>Build your flock's home base in minutes. Everything you need to care for your birds, ready when you are.</p>
            </div>
            <div class="intro-mark" aria-hidden="true">W</div>
        </section>

        <section class="auth-panel">
            <div class="auth-form-wrap">
                <div class="mobile-brand"><span>Wakr</span><span class="status-dot"></span></div>
                <div class="form-heading">
                    <span class="eyebrow">Start your journey</span>
                    <h2>Create your<br>free workspace</h2>
                    <p>Set up your account and bring your flock into focus.</p>
                </div>

                <form method="POST" action="{{ url('/register') }}" class="auth-form">
                    @csrf
                    <div class="field-group">
                        <label for="name">Your name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Your name" autocomplete="name" required autofocus>
                        @error('name') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="password">Create a password</label>
                        <input id="password" name="password" type="password" placeholder="At least 8 characters" autocomplete="new-password" required>
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Repeat your password" autocomplete="new-password" required>
                    </div>
                    <button class="auth-submit" type="submit">Create workspace <span aria-hidden="true">&#8594;</span></button>
                </form>

                <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
                <p class="auth-legal">By creating an account, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.</p>
            </div>
        </section>
    </main>
    @include('components.toast')
</body>
</html>
