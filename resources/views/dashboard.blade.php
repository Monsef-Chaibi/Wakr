<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('dashboard_title') }} | {{ __('app_name') }}</title>
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
</head>
<body class="dashboard-page">
    @php
        $isArabic = app()->getLocale() === 'ar';
        $dashboardUrl = $isArabic ? url('/ar/dashboard') : route('dashboard');
        $languageUrl = $isArabic ? route('dashboard') : url('/ar/dashboard');
    @endphp

    <div class="dashboard-shell">
        <aside class="dashboard-sidebar" id="dashboard-sidebar">
            <div class="sidebar-brand">
                <a href="{{ $dashboardUrl }}" class="dashboard-logo"><img src="{{ asset('images/wakr/wakrlogo.png') }}" alt="{{ __('app_name') }}"><span>{{ __('logo_text') }}</span></a>
                <button class="sidebar-close" type="button" data-sidebar-close aria-label="{{ __('dashboard_close_menu') }}">&times;</button>
            </div>
            <div class="sidebar-label">{{ __('dashboard_workspace') }}</div>
            <nav class="sidebar-nav" aria-label="{{ __('dashboard_navigation') }}">
                <a href="{{ $dashboardUrl }}" class="sidebar-link is-active"><span class="nav-icon" aria-hidden="true">&#9632;</span><span>{{ __('dashboard_overview') }}</span></a>
                <a href="#birds" class="sidebar-link"><span class="nav-icon" aria-hidden="true">&#9670;</span><span>{{ __('dashboard_birds') }}</span></a>
                <a href="#cages" class="sidebar-link"><span class="nav-icon" aria-hidden="true">&#9633;</span><span>{{ __('dashboard_cages') }}</span></a>
                <a href="#breeding" class="sidebar-link"><span class="nav-icon" aria-hidden="true">&#9672;</span><span>{{ __('dashboard_breeding') }}</span></a>
                <a href="#records" class="sidebar-link"><span class="nav-icon" aria-hidden="true">&#9776;</span><span>{{ __('dashboard_records') }}</span></a>
            </nav>
            <div class="sidebar-bottom">
                <a href="#settings" class="sidebar-link"><span class="nav-icon" aria-hidden="true">&#9881;</span><span>{{ __('dashboard_settings') }}</span></a>
                <div class="sidebar-tip"><span class="tip-mark">+</span><strong>{{ __('dashboard_tip_title') }}</strong><p>{{ __('dashboard_tip_text') }}</p></div>
            </div>
        </aside>

        <div class="dashboard-main">
            <header class="dashboard-navbar">
                <button class="sidebar-open" type="button" data-sidebar-open aria-label="{{ __('dashboard_open_menu') }}">&#9776;</button>
                <div class="dashboard-search"><span aria-hidden="true">&#9906;</span><input type="search" placeholder="{{ __('dashboard_search_placeholder') }}" aria-label="{{ __('dashboard_search_placeholder') }}"><kbd>⌘ K</kbd></div>
                <div class="navbar-actions">
                    <a class="language-button" href="{{ $languageUrl }}" aria-label="{{ $isArabic ? __('language_en') : __('language_ar') }}"><span aria-hidden="true">&#127760;</span><span>{{ $isArabic ? 'EN' : 'AR' }}</span></a>
                    <button class="notification-button" type="button" aria-label="{{ __('dashboard_notifications') }}"><span aria-hidden="true">&#128276;</span><i></i></button>
                    <details class="profile-menu"><summary><span class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><span class="profile-details"><strong>{{ auth()->user()->name }}</strong><small>{{ __('dashboard_owner') }}</small></span><span class="profile-chevron" aria-hidden="true">&#8964;</span></summary><div class="profile-dropdown"><a href="#profile">{{ __('dashboard_profile') }}</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">{{ __('dashboard_log_out') }}</button></form></div></details>
                </div>
            </header>

            <main class="dashboard-content">
                <div class="welcome-row"><div><span class="dashboard-eyebrow">{{ now()->locale(app()->getLocale())->translatedFormat('l, F j, Y') }}</span><h1>{{ __('dashboard_greeting', ['name' => auth()->user()->name]) }}</h1><p>{{ __('dashboard_subtitle') }}</p></div><button class="dashboard-primary-button" type="button"><span>+</span>{{ __('dashboard_add_bird') }}</button></div>
                <section class="stats-grid" aria-label="{{ __('dashboard_summary') }}">
                    <article class="stat-card stat-card-green"><span class="stat-icon">&#9670;</span><span class="stat-label">{{ __('dashboard_total_birds') }}</span><strong>128</strong><small><b>+12%</b> {{ __('dashboard_this_month') }}</small></article>
                    <article class="stat-card stat-card-yellow"><span class="stat-icon">&#9633;</span><span class="stat-label">{{ __('dashboard_active_cages') }}</span><strong>24</strong><small><b>+3</b> {{ __('dashboard_this_month') }}</small></article>
                    <article class="stat-card stat-card-coral"><span class="stat-icon">&#9672;</span><span class="stat-label">{{ __('dashboard_breeding_pairs') }}</span><strong>16</strong><small><b>+2</b> {{ __('dashboard_this_month') }}</small></article>
                    <article class="stat-card stat-card-blue"><span class="stat-icon">&#10003;</span><span class="stat-label">{{ __('dashboard_eggs_hatching') }}</span><strong>32</strong><small><b>8 {{ __('dashboard_due_soon') }}</b></small></article>
                </section>
                <div class="dashboard-grid">
                    <section class="panel activity-panel" id="records"><div class="panel-heading"><div><span class="panel-kicker">{{ __('dashboard_recent_activity_kicker') }}</span><h2>{{ __('dashboard_recent_activity') }}</h2></div><a href="#records">{{ __('dashboard_view_all') }} <span aria-hidden="true">&#8594;</span></a></div><div class="activity-list"><div class="activity-item"><span class="activity-dot green"></span><div><strong>{{ __('dashboard_activity_bird') }}</strong><p>{{ __('dashboard_activity_bird_desc') }}</p></div><time>{{ __('dashboard_today') }}, 09:42</time></div><div class="activity-item"><span class="activity-dot yellow"></span><div><strong>{{ __('dashboard_activity_eggs') }}</strong><p>{{ __('dashboard_activity_eggs_desc') }}</p></div><time>{{ __('dashboard_yesterday') }}, 16:20</time></div><div class="activity-item"><span class="activity-dot coral"></span><div><strong>{{ __('dashboard_activity_pair') }}</strong><p>{{ __('dashboard_activity_pair_desc') }}</p></div><time>{{ __('dashboard_yesterday') }}, 11:05</time></div></div></section>
                    <section class="panel quick-panel"><div class="panel-heading"><div><span class="panel-kicker">{{ __('dashboard_shortcuts_kicker') }}</span><h2>{{ __('dashboard_quick_actions') }}</h2></div></div><div class="quick-actions"><a href="#birds"><span class="quick-icon green">+</span><span>{{ __('dashboard_add_new_bird') }}</span><b>&#8594;</b></a><a href="#cages"><span class="quick-icon yellow">+</span><span>{{ __('dashboard_add_cage') }}</span><b>&#8594;</b></a><a href="#breeding"><span class="quick-icon coral">+</span><span>{{ __('dashboard_new_pair') }}</span><b>&#8594;</b></a></div></section>
                </div>
                <section class="panel upcoming-panel" id="breeding"><div class="panel-heading"><div><span class="panel-kicker">{{ __('dashboard_calendar_kicker') }}</span><h2>{{ __('dashboard_upcoming') }}</h2></div><a href="#calendar">{{ __('dashboard_open_calendar') }} <span aria-hidden="true">&#8594;</span></a></div><div class="upcoming-list"><div class="upcoming-date"><strong>18</strong><span>JUN</span></div><div class="upcoming-copy"><strong>{{ __('dashboard_event_hatch') }}</strong><span>{{ __('dashboard_event_hatch_desc') }}</span></div><span class="event-tag green-tag">{{ __('dashboard_event_tag') }}</span><div class="upcoming-date"><strong>21</strong><span>JUN</span></div><div class="upcoming-copy"><strong>{{ __('dashboard_event_check') }}</strong><span>{{ __('dashboard_event_check_desc') }}</span></div><span class="event-tag yellow-tag">{{ __('dashboard_event_tag_check') }}</span></div></section>
            </main>
        </div>
    </div>
    <div class="sidebar-overlay" data-sidebar-close></div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('dashboard-sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const setSidebar = (open) => { sidebar.classList.toggle('is-open', open); overlay.classList.toggle('is-visible', open); };
            document.querySelector('[data-sidebar-open]').addEventListener('click', () => setSidebar(true));
            document.querySelectorAll('[data-sidebar-close]').forEach((button) => button.addEventListener('click', () => setSidebar(false)));
        });
    </script>
</body>
</html>
