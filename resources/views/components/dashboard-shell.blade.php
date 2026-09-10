@php
    $pageUrls = [
        'dashboard' => $isArabic ? url('/ar/dashboard') : route('dashboard'),
        'birds' => $isArabic ? url('/ar/birds') : route('birds'),
        'cages' => $isArabic ? url('/ar/cages') : route('cages'),
        'breeding' => $isArabic ? url('/ar/breeding') : route('breeding'),
        'records' => $isArabic ? url('/ar/records') : route('records'),
        'program' => $isArabic ? url('/ar/program') : route('program'),
    ];
    $languageUrl = $pageUrls[$activePage];
    $languageRoute = $isArabic ? route($activePage === 'dashboard' ? 'dashboard' : $activePage) : url('/ar/' . ($activePage === 'dashboard' ? 'dashboard' : $activePage));
@endphp

<div class="dashboard-shell">
    <aside class="dashboard-sidebar" id="dashboard-sidebar">
        <div class="sidebar-brand">
            <a href="{{ $pageUrls['dashboard'] }}" class="dashboard-logo"><img src="{{ asset('images/wakr/wakrlogo.png') }}"
                    alt="{{ __('app_name') }}"><span>{{ __('logo_text') }}</span></a>
            <button class="sidebar-close" type="button" data-sidebar-close
                aria-label="{{ __('dashboard_close_menu') }}">&times;</button>
        </div>
        <div class="sidebar-label">{{ __('dashboard_workspace') }}</div>
        <nav class="sidebar-nav" aria-label="{{ __('dashboard_navigation') }}">
            <a href="{{ $pageUrls['dashboard'] }}" class="sidebar-link {{ $activePage === 'dashboard' ? 'is-active' : '' }}">
                <img src=" https://cdn-user-icons.flaticon.com/111755/111755784/1789060163500.svg?token=exp=1789061064~hmac=b8e39972d7a5e3ceeaf11a1e5cde8374" alt="Dashboard Icon" class="nav-icon" aria-hidden="true">
                <span>{{ __('dashboard_overview') }}</span>
            </a>
            <a href="{{ $pageUrls['birds'] }}" class="sidebar-link {{ $activePage === 'birds' ? 'is-active' : '' }}">
                <img src="https://cdn-user-icons.flaticon.com/111755/111755784/1789060118813.svg?token=exp=1789061019~hmac=6c35979dba16d8d3938a32f53ca02a3f" alt="Birds Icon" class="nav-icon" aria-hidden="true">
                <span>{{ __('dashboard_birds') }}</span></a>
            <a href="{{ $pageUrls['cages'] }}" class="sidebar-link {{ $activePage === 'cages' ? 'is-active' : '' }}">
                <img src=" https://cdn-user-icons.flaticon.com/111755/111755784/1789060073738.svg?token=exp=1789060974~hmac=6d3266f5742dcc800e0ede5a77178eac" alt="Cages Icon" class="nav-icon w-5!" aria-hidden="true">
                <span>{{ __('dashboard_cages') }}</span>
            </a>
            <a href="{{ $pageUrls['breeding'] }}" class="sidebar-link {{ $activePage === 'breeding' ? 'is-active' : '' }}">
                <img src="https://cdn-user-icons.flaticon.com/111755/111755784/1789059961915.svg?token=exp=1789060862~hmac=57e3995bc41b8b54c102fa30a78aac1d" alt="Breeding Icon" class="nav-icon" aria-hidden="true">
                <span>{{ __('dashboard_breeding') }}</span>
            </a>
            <a href="{{ $pageUrls['records'] }}" class="sidebar-link {{ $activePage === 'records' ? 'is-active' : '' }}">
                <img src="https://cdn-user-icons.flaticon.com/111755/111755784/1789059912807.svg?token=exp=1789060813~hmac=06b7f4d9a1ca99e00e1ca031987164ea" alt="Records Icon" class="nav-icon" aria-hidden="true">
                <span>{{ __('dashboard_records') }}</span>
            </a>
            <a href="{{ $pageUrls['program'] }}" class="sidebar-link {{ $activePage === 'program' ? 'is-active' : '' }}">
                <img src="https://cdn-user-icons.flaticon.com/111755/111755784/1789059860646.svg?token=exp=1789060761~hmac=c8d159416a8e347aa6e1906ccc95f35f" alt="Program Icon" class="nav-icon" aria-hidden="true">
                <span>{{ __('program_title') }}</span>
            </a>
        </nav>
        <div class="sidebar-bottom">
            <div class="sidebar-tip"><span class="tip-mark">+</span><strong>{{ __('dashboard_tip_title') }}</strong>
                <p>{{ __('dashboard_tip_text') }}</p>
            </div>
        </div>
    </aside>

    <div class="dashboard-main">
        <header class="dashboard-navbar">
            <button class="sidebar-open" type="button" data-sidebar-open
                aria-label="{{ __('dashboard_open_menu') }}">&#9776;</button>
            <div class="dashboard-search"><span aria-hidden="true">&#9906;</span><input type="search"
                    placeholder="{{ __('dashboard_search_placeholder') }}"
                    aria-label="{{ __('dashboard_search_placeholder') }}"><kbd>⌘ K</kbd></div>
            <div class="navbar-actions">
                <label class="dashboard-language-toggle" for="dashboard-language-toggle">
                    <input type="checkbox" id="dashboard-language-toggle" class="dashboard-language-input"
                        data-en-url="{{ $languageUrl }}" data-ar-url="{{ $languageRoute }}"
                        aria-label="{{ $isArabic ? __('language_en') : __('language_ar') }}"
                        {{ $isArabic ? 'checked' : '' }}>
                    <span class="dashboard-language-slider"><span class="dashboard-language-label">EN</span><span
                            class="dashboard-language-label">AR</span></span>
                </label>
                <button class="notification-button" type="button"
                    aria-label="{{ __('dashboard_notifications') }}"><span
                        aria-hidden="true">&#128276;</span><i></i></button>
                <details class="profile-menu">
                    <summary><span class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span
                            class="profile-details"><strong>{{ auth()->user()->name }}</strong><small>{{ __('dashboard_owner') }}</small></span><span
                            class="profile-chevron" aria-hidden="true">&#8964;</span></summary>
                    <div class="profile-dropdown"><a href="#profile">{{ __('dashboard_profile') }}</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button
                                type="submit">{{ __('dashboard_log_out') }}</button></form>
                    </div>
                </details>
            </div>
        </header>

        {{ $slot }}
    </div>
</div>
<div class="sidebar-overlay" data-sidebar-close></div>
