<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="{{ app()->getLocale() === 'ar' ? 'rtl' : '' }}" id="htmlRoot">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ __('app_name') }} - {{ __('hero_title') }}</title>
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.js', 'resources/js/landing.js'])
</head>
<body>
    @php
        $loginUrl = app()->getLocale() === 'ar' ? url('/ar/login') : route('login');
        $registerUrl = app()->getLocale() === 'ar' ? url('/ar/register') : route('register');
    @endphp
    <!-- HEADER / NAVIGATION -->
    <header>
        <nav>
            <a href="/" class="logo-section">
                <div class=""><img src="{{ asset('images/wakr/wakrlogo.png') }}" alt="Wakr Logo"></div>
                <div class="logo-text">{{ __('logo_text') }}</div>
            </a>

            <button class="mobile-menu-button" type="button" aria-label="{{ __('nav_menu') }}" aria-controls="mobile-menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <div class="nav-center">
                <ul class="nav-links">
                    <li><a href="#home">{{ __('nav_home') }}</a></li>
                    <li><a href="#features">{{ __('nav_features') }}</a></li>
                    <li><a href="#how-it-works">{{ __('nav_how_it_works') }}</a></li>
                    <li><a href="#about">{{ __('nav_about') }}</a></li>
                </ul>
            </div>

            <div class="nav-right">
                <div class="language-switcher">
                    <label class="lang-toggle mt-2!" for="lang-toggle-checkbox">
                        <input type="checkbox" id="lang-toggle-checkbox" class="lang-toggle-input" onchange="switchLanguage(this.checked ? 'ar' : 'en')" {{ app()->getLocale() === 'ar' ? 'checked' : '' }}>
                        <span class="lang-toggle-slider">
                            <span class="lang-toggle-label en-label">EN</span>
                            <span class="lang-toggle-label ar-label">AR</span>
                        </span>
                    </label>
                </div>
                @auth
                    <div class="nav-auth-user">
                        <span class="nav-user-name">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-login">Log out</button>
                        </form>
                    </div>
                @else
                    <div class="nav-auth-guest">
                        <a href="{{ $loginUrl }}" class="btn-login">{{ __('nav_login') }}</a>
                        <a href="{{ $registerUrl }}" class="btn-primary">{{ __('nav_get_started') }}</a>
                    </div>
                @endauth
            </div>
        </nav>
    </header>

    <div class="mobile-menu-backdrop" data-mobile-menu-close></div>
    <aside class="mobile-menu" id="mobile-menu" aria-hidden="true">
        <div class="mobile-menu-header">
            <div class="logo-text">{{ __('logo_text') }}</div>
            <button class="mobile-menu-close" type="button" aria-label="{{ __('nav_close') }}" data-mobile-menu-close>&times;</button>
        </div>
        <ul class="mobile-nav-links">
            <li><a href="#home">{{ __('nav_home') }}</a></li>
            <li><a href="#features">{{ __('nav_features') }}</a></li>
            <li><a href="#how-it-works">{{ __('nav_how_it_works') }}</a></li>
            <li><a href="#about">{{ __('nav_about') }}</a></li>
        </ul>
        <div class="mobile-menu-actions">
            <label class="lang-toggle" for="mobile-lang-toggle-checkbox">
                <input type="checkbox" id="mobile-lang-toggle-checkbox" class="lang-toggle-input" onchange="switchLanguage(this.checked ? 'ar' : 'en')" {{ app()->getLocale() === 'ar' ? 'checked' : '' }}>
                <span class="lang-toggle-slider">
                    <span class="lang-toggle-label en-label">EN</span>
                    <span class="lang-toggle-label ar-label">AR</span>
                </span>
            </label>
            @auth
                <span class="nav-user-name">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-login">Log out</button>
                </form>
            @else
                <a href="{{ $loginUrl }}" class="btn-login">{{ __('nav_login') }}</a>
                <a href="{{ $registerUrl }}" class="btn-primary">{{ __('nav_get_started') }}</a>
            @endauth
        </div>
    </aside>

    <!-- HERO SECTION -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>{{ __('hero_title') }}</h1>
            <p>{{ __('hero_subtitle') }}</p>
            <div class="hero-buttons">
                <a href="{{ $registerUrl }}" class="btn-primary">{{ __('hero_btn_started') }}</a>
                <a href="{{ $loginUrl }}" class="btn-secondary">{{ __('hero_btn_login') }}</a>
            </div>
        </div>

        <div class="hidden md:block hero-visual">
            <div class="hero-illustratcion">
                <div class="hero-brand-lockup mb-20!">
                    <img class="hero-brand-icon" src="{{ asset('images/wakr/wakrlogoxl.png') }}" alt="Wakr">
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="features" id="features">
        <div class="section-header">
            <h2>{{ __('features_heading') }}</h2>
            <p>{{ __('features_subheading') }}</p>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <img
                        src="https://cdn-icons-png.flaticon.com/512/12783/12783551.png"
                        alt="Icon"
                        class="icon w-15"
                    >
                </div>
                <h3>{{ __('feature_birds_title') }}</h3>
                <p>{{ __('feature_birds_desc') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img
                        src="https://cdn-icons-png.flaticon.com/512/3376/3376001.png"
                        alt="Icon"
                        class="icon w-15"
                    >
                </div>
                <h3>{{ __('feature_cage_title') }}</h3>
                <p>{{ __('feature_cage_desc') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img
                        src="   https://cdn-icons-png.flaticon.com/512/2706/2706373.png "
                        alt="Icon"
                        class="icon w-15"
                    ></div>
                <h3>{{ __('feature_couples_title') }}</h3>
                <p>{{ __('feature_couples_desc') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img
                        src="   https://cdn-icons-png.flaticon.com/512/3031/3031439.png "
                        class="icon w-15"
                    ></div>
                <h3>{{ __('feature_eggs_title') }}</h3>
                <p>{{ __('feature_eggs_desc') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img
                        src="https://cdn-user-icons.flaticon.com/111755/111755784/1788543790084.svg?token=exp=1788544690~hmac=19251f0c4ac487a90b4caef3a2d8767b"
                        alt="Icon"
                        class="icon w-15"
                    >
                </div>
                <h3>{{ __('feature_tree_title') }}</h3>
                <p>{{ __('feature_tree_desc') }}</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <img
                        src="https://cdn-user-icons.flaticon.com/111755/111755784/1788543386069.svg?token=exp=1788544286~hmac=5abd37288f8ed35a7dc524f6b5412ad6"
                        alt="Icon"
                        class="icon w-15"
                    ></div>
                <h3>{{ __('feature_qr_title') }}</h3>
                <p>{{ __('feature_qr_desc') }}</p>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS SECTION -->
    <section class="how-it-works" id="how-it-works">
        <div class="section-header">
            <h2>{{ __('how_heading') }}</h2>
            <p>{{ __('how_subheading') }}</p>
        </div>

        <div class="timeline">
            <div class="timeline-step">
                <div class="step-number">1</div>
                <h3>{{ __('how_step_1') }}</h3>
            </div>
            <div class="timeline-step">
                <div class="step-number">2</div>
                <h3>{{ __('how_step_2') }}</h3>
            </div>
            <div class="timeline-step">
                <div class="step-number">3</div>
                <h3>{{ __('how_step_3') }}</h3>
            </div>
            <div class="timeline-step">
                <div class="step-number">4</div>
                <h3>{{ __('how_step_4') }}</h3>
            </div>
        </div>
    </section>

    <!-- QR CODE FEATURE SECTION -->
    <section class="qr-feature" id="qr">
        <div class="qr-content">
            <h2>{{ __('qr_heading') }}</h2>
            <p>{{ __('qr_desc') }}</p>
            <button class="btn-primary">{{ __('qr_btn_learn') }}</button>
        </div>

        <div class="qr-visual hidden md:block">
            <div class="cage-with-qr">
                <img src="https://cdn-user-icons.flaticon.com/111755/111755784/1788547328788.svg?token=exp=1788548231~hmac=b0ca865df8a48ad738ce4f1e747d3cc5" alt="QR code illustration">
            </div>
        </div>
    </section>

    <!-- BENEFITS SECTION -->
    <section class="benefits" id="benefits">
        <div class="benefits-layout">
            <div class="benefits-intro">
                <span class="section-eyebrow">{{ __('benefits_eyebrow') }}</span>
                <h2>{{ __('benefits_heading') }}</h2>
                <p>{{ __('benefits_subheading') }}</p>
            </div>
        </div>
    </section>

    <!-- FINAL CTA SECTION -->
    <section class="final-cta">
        <h2>{{ __('cta_heading') }}</h2>
        <div class="cta-buttons">
            <a href="{{ $registerUrl }}" class="btn-primary">{{ __('cta_btn_account') }}</a>
            <a href="{{ $loginUrl }}" class="btn-secondary">{{ __('cta_btn_login') }}</a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo">
                    <div class="">
                        <img class="w-10" src="{{ asset('images/wakr/wakrlogo.png') }}" alt="Wakr Logo">
                    </div>
                    <div class="footer-logo-text ADLaM ">{{ __('app_name') }}</div>
                </div>
                <p class="footer-description">{{ __('footer_desc') }}</p>
            </div>

            <div class="footer-section">
                <h3 class="ADLaM">{{ __('footer_features') }}</h3>
                <ul>
                    <li><a href="#">{{ __('footer_bird_mgmt') }}</a></li>
                    <li><a href="#">{{ __('footer_cage_tracking') }}</a></li>
                    <li><a href="#">{{ __('footer_breeding_couples') }}</a></li>
                    <li><a href="#">{{ __('footer_qr_codes') }}</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="ADLaM">{{ __('footer_contact') }}</h3>
                <ul>
                    <li><a href="#">{{ __('footer_support') }}</a></li>
                    <li><a href="#">{{ __('footer_documentation') }}</a></li>
                    <li><a href="#">{{ __('footer_email') }}</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3 class="ADLaM">{{ __('footer_legal') }}</h3>
                <ul>
                    <li><a href="#">{{ __('footer_privacy') }}</a></li>
                    <li><a href="#">{{ __('footer_terms') }}</a></li>
                    <li><a href="#">{{ __('footer_cookies') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-copyright">{{ __('footer_copyright') }}</div>
            <div class="footer-lang-switcher">
                <label class="lang-toggle-footer" for="lang-toggle-footer-checkbox">
                    <input type="checkbox" id="lang-toggle-footer-checkbox" class="lang-toggle-input" onchange="switchLanguage(this.checked ? 'ar' : 'en')" {{ app()->getLocale() === 'ar' ? 'checked' : '' }}>
                    <span class="lang-toggle-slider-footer">
                        <span class="lang-toggle-label en-label">EN</span>
                        <span class="lang-toggle-label ar-label">AR</span>
                    </span>
                </label>
            </div>
        </div>
    </footer>

    @include('components.toast')
</body>
</html>
