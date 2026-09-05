<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | {{ __('app_name') }}</title>
    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-intro">
            <a class="auth-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/wakr/wakrlogo.png') }}" alt="Wakr">
                <span>{{ __('app_name') }}</span>
            </a>
            <div class="intro-copy">
                <span class="auth-kicker">Your flock, in focus</span>
                <h1>Good to see you<br><em>again.</em></h1>
                <p>Pick up where you left off and keep every bird, cage, and breeding detail beautifully organized.</p>
            </div>
            <div class="intro-mark" aria-hidden="true">W</div>
        </section>

        <section class="auth-panel">
            <div class="auth-form-wrap">
                <div class="mobile-brand"><span>Wakr</span><span class="status-dot"></span></div>
                <div class="form-heading">
                    <span class="eyebrow">Welcome back</span>
                    <h2>Sign in to your<br>workspace</h2>
                    <p>Enter your details to continue managing your flock.</p>
                </div>

                @if (session('status'))
                    <div class="auth-notice">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ url('/login') }}" class="auth-form">
                    @csrf
                    <div class="field-group">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required autofocus>
                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <div class="label-row"><label for="password">Password</label><a href="#">Forgot password?</a></div>
                        <input id="password" name="password" type="password" placeholder="Enter your password" autocomplete="current-password" required>
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <label class="check-row"><input type="checkbox" name="remember"><span>Keep me signed in</span></label>
                    <button class="auth-submit" type="submit">Sign in <span aria-hidden="true">&#8594;</span></button>
                </form>

                <p class="auth-switch">New to Wakr? <a href="{{ route('register') }}">Create an account</a></p>
                <p class="auth-legal">By continuing, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.</p>
            </div>
        </section>
    </main>
    @include('components.toast')
</body>
</html>
