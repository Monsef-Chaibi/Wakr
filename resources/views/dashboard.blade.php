<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('dashboard_title') }} | {{ __('app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=ADLaM+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
</head>

<body class="dashboard-page">
    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <x-dashboard-shell :is-arabic="$isArabic" active-page="dashboard">
            <main class="dashboard-content">
                <div class="welcome-row">
                    <div><span
                            class="dashboard-eyebrow">{{ now()->locale(app()->getLocale())->translatedFormat('l, F j, Y') }}</span>
                        <h1 class="ADLaM">{{ __('dashboard_greeting', ['name' => auth()->user()->name]) }}</h1>
                        <p>{{ __('dashboard_subtitle') }}</p>
                    </div><button class="dashboard-primary-button"
                        type="button"><span>+</span>{{ __('dashboard_add_bird') }}</button>
                </div>
                <section class="stats-grid" aria-label="{{ __('dashboard_summary') }}">
                    <article class="stat-card stat-card-green"><span class="stat-icon">&#9670;</span><span
                            class="stat-label">{{ __('dashboard_total_birds') }}</span><strong>128</strong><small><b>+12%</b>
                            {{ __('dashboard_this_month') }}</small></article>
                    <article class="stat-card stat-card-yellow"><span class="stat-icon">&#9633;</span><span
                            class="stat-label">{{ __('dashboard_active_cages') }}</span><strong>24</strong><small><b>+3</b>
                            {{ __('dashboard_this_month') }}</small></article>
                    <article class="stat-card stat-card-coral"><span class="stat-icon">&#9672;</span><span
                            class="stat-label">{{ __('dashboard_breeding_pairs') }}</span><strong>16</strong><small><b>+2</b>
                            {{ __('dashboard_this_month') }}</small></article>
                    <article class="stat-card stat-card-blue"><span class="stat-icon">&#10003;</span><span
                            class="stat-label">{{ __('dashboard_eggs_hatching') }}</span><strong>32</strong><small><b>8
                                {{ __('dashboard_due_soon') }}</b></small></article>
                </section>
                <div class="dashboard-grid">
                    <section class="panel activity-panel" id="records">
                        <div class="panel-heading">
                            <div><span class="panel-kicker">{{ __('dashboard_recent_activity_kicker') }}</span>
                                <h2>{{ __('dashboard_recent_activity') }}</h2>
                            </div><a href="#records">{{ __('dashboard_view_all') }} <span
                                    aria-hidden="true">&#8594;</span></a>
                        </div>
                        <div class="activity-list">
                            <div class="activity-item"><span class="activity-dot green"></span>
                                <div><strong>{{ __('dashboard_activity_bird') }}</strong>
                                    <p>{{ __('dashboard_activity_bird_desc') }}</p>
                                </div><time>{{ __('dashboard_today') }}, 09:42</time>
                            </div>
                            <div class="activity-item"><span class="activity-dot yellow"></span>
                                <div><strong>{{ __('dashboard_activity_eggs') }}</strong>
                                    <p>{{ __('dashboard_activity_eggs_desc') }}</p>
                                </div><time>{{ __('dashboard_yesterday') }}, 16:20</time>
                            </div>
                            <div class="activity-item"><span class="activity-dot coral"></span>
                                <div><strong>{{ __('dashboard_activity_pair') }}</strong>
                                    <p>{{ __('dashboard_activity_pair_desc') }}</p>
                                </div><time>{{ __('dashboard_yesterday') }}, 11:05</time>
                            </div>
                        </div>
                    </section>
                    <section class="panel quick-panel">
                        <div class="panel-heading">
                            <div><span class="panel-kicker">{{ __('dashboard_shortcuts_kicker') }}</span>
                                <h2>{{ __('dashboard_quick_actions') }}</h2>
                            </div>
                        </div>
                        <div class="quick-actions"><a href="#birds"><span
                                    class="quick-icon green">+</span><span>{{ __('dashboard_add_new_bird') }}</span><b>&#8594;</b></a><a
                                href="#cages"><span
                                    class="quick-icon yellow">+</span><span>{{ __('dashboard_add_cage') }}</span><b>&#8594;</b></a><a
                                href="#breeding"><span
                                    class="quick-icon coral">+</span><span>{{ __('dashboard_new_pair') }}</span><b>&#8594;</b></a>
                        </div>
                    </section>
                </div>
                <section class="panel upcoming-panel" id="breeding">
                    <div class="panel-heading">
                        <div><span class="panel-kicker">{{ __('dashboard_calendar_kicker') }}</span>
                            <h2>{{ __('dashboard_upcoming') }}</h2>
                        </div><a href="{{ $isArabic ? url('/ar/program') : route('program') }}">{{ __('dashboard_open_calendar') }} <span
                                aria-hidden="true">&#8594;</span></a>
                    </div>
                    <div class="upcoming-list">
                        <div class="upcoming-date"><strong>18</strong><span>JUN</span></div>
                        <div class="upcoming-copy">
                            <strong>{{ __('dashboard_event_hatch') }}</strong><span>{{ __('dashboard_event_hatch_desc') }}</span>
                        </div><span class="event-tag green-tag">{{ __('dashboard_event_tag') }}</span>
                        <div class="upcoming-date"><strong>21</strong><span>JUN</span></div>
                        <div class="upcoming-copy">
                            <strong>{{ __('dashboard_event_check') }}</strong><span>{{ __('dashboard_event_check_desc') }}</span>
                        </div><span class="event-tag yellow-tag">{{ __('dashboard_event_tag_check') }}</span>
                    </div>
                </section>
            </main>
    </x-dashboard-shell>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('dashboard-sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const languageToggle = document.getElementById('dashboard-language-toggle');
            const setSidebar = (open) => {
                sidebar.classList.toggle('is-open', open);
                overlay.classList.toggle('is-visible', open);
            };
            document.querySelector('[data-sidebar-open]').addEventListener('click', () => setSidebar(true));
            document.querySelectorAll('[data-sidebar-close]').forEach((button) => button.addEventListener('click',
            () => setSidebar(false)));
            languageToggle.addEventListener('change', () => {
                window.location.href = languageToggle.checked ? languageToggle.dataset.arUrl : languageToggle.dataset.enUrl;
            });
        });
    </script>
</body>

</html>
