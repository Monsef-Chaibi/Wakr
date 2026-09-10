<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('birds_title') }} | {{ __('app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=ADLaM+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
</head>

<body class="dashboard-page birds-page">
    @php
        $isArabic = app()->getLocale() === 'ar';
    @endphp

    <x-dashboard-shell :is-arabic="$isArabic" active-page="birds">
            <main class="dashboard-content birds-content">
                <div class="birds-page-heading">
                    <div>
                        <span class="dashboard-eyebrow">{{ __('dashboard_birds') }}</span>
                        <h1 class="ADLaM!">{{ __('birds_title') }}</h1>
                        <p>{{ __('birds_subtitle') }}</p>
                    </div>
                    <button class="dashboard-primary-button" type="button"><span>+</span>{{ __('birds_add') }}</button>
                </div>

                <section class="birds-stats" aria-label="{{ __('birds_title') }}">
                    <article class="birds-stat"><span class="birds-stat-icon birds-stat-green">&#9670;</span><div><span>{{ __('birds_total') }}</span><strong>128</strong></div></article>
                    <article class="birds-stat"><span class="birds-stat-icon birds-stat-blue">&#10003;</span><div><span>{{ __('birds_active') }}</span><strong>112</strong></div></article>
                    <article class="birds-stat"><span class="birds-stat-icon birds-stat-coral">&#9672;</span><div><span>{{ __('birds_pairs') }}</span><strong>16</strong></div></article>
                </section>

                <section class="birds-panel panel">
                    <div class="birds-toolbar">
                        <label class="birds-search"><span aria-hidden="true">&#9906;</span><input type="search"
                                placeholder="{{ __('birds_search') }}" aria-label="{{ __('birds_search') }}"></label>
                        <div class="birds-filters" role="group" aria-label="{{ __('birds_filter_status') }}">
                            <button class="birds-filter is-selected" type="button" data-bird-filter="all">{{ __('birds_filter_all') }}</button>
                            <button class="birds-filter" type="button" data-bird-filter="male">{{ __('birds_filter_male') }}</button>
                            <button class="birds-filter" type="button" data-bird-filter="female">{{ __('birds_filter_female') }}</button>
                        </div>
                    </div>
                    <div class="birds-table-wrap">
                        <table class="birds-table">
                            <thead><tr><th>{{ __('birds_table_bird') }}</th><th>{{ __('birds_table_species') }}</th><th>{{ __('birds_table_sex') }}</th><th>{{ __('birds_table_cage') }}</th><th>{{ __('birds_table_status') }}</th><th>{{ __('birds_table_added') }}</th></tr></thead>
                            <tbody>
                                <tr data-bird-sex="male"><td><div class="bird-identity"><span class="bird-avatar bird-avatar-green">S</span><span><strong>Sky</strong><small>BRD-00128</small></span></div></td><td>Budgerigar</td><td>{{ __('birds_male') }}</td><td>Cage 04</td><td><span class="bird-status status-active">{{ __('birds_status_active') }}</span></td><td>Jun 14, 2026</td></tr>
                                <tr data-bird-sex="female"><td><div class="bird-identity"><span class="bird-avatar bird-avatar-yellow">L</span><span><strong>Luna</strong><small>BRD-00127</small></span></div></td><td>Lovebird</td><td>{{ __('birds_female') }}</td><td>Cage 02</td><td><span class="bird-status status-pair">{{ __('birds_status_pair') }}</span></td><td>Jun 12, 2026</td></tr>
                                <tr data-bird-sex="male"><td><div class="bird-identity"><span class="bird-avatar bird-avatar-coral">S</span><span><strong>Sol</strong><small>BRD-00126</small></span></div></td><td>Lovebird</td><td>{{ __('birds_male') }}</td><td>Cage 02</td><td><span class="bird-status status-pair">{{ __('birds_status_pair') }}</span></td><td>Jun 12, 2026</td></tr>
                                <tr data-bird-sex="female"><td><div class="bird-identity"><span class="bird-avatar bird-avatar-blue">P</span><span><strong>Peach</strong><small>BRD-00125</small></span></div></td><td>Cockatiel</td><td>{{ __('birds_female') }}</td><td>{{ __('birds_no_cage') }}</td><td><span class="bird-status status-resting">{{ __('birds_status_resting') }}</span></td><td>Jun 09, 2026</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
    </x-dashboard-shell>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('dashboard-sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            const languageToggle = document.getElementById('dashboard-language-toggle');
            const birdSearch = document.querySelector('.birds-search input');
            const birdRows = [...document.querySelectorAll('.birds-table tbody tr')];
            let activeBirdFilter = 'all';
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
            const filterBirds = () => {
                const searchTerm = birdSearch.value.trim().toLowerCase();
                birdRows.forEach((row) => {
                    const matchesFilter = activeBirdFilter === 'all' || row.dataset.birdSex === activeBirdFilter;
                    const matchesSearch = row.textContent.toLowerCase().includes(searchTerm);
                    row.hidden = !matchesFilter || !matchesSearch;
                });
            };
            birdSearch.addEventListener('input', filterBirds);
            document.querySelectorAll('[data-bird-filter]').forEach((button) => button.addEventListener('click', () => {
                activeBirdFilter = button.dataset.birdFilter;
                document.querySelectorAll('[data-bird-filter]').forEach((filterButton) => filterButton.classList.toggle('is-selected', filterButton === button));
                filterBirds();
            }));
        });
    </script>
</body>

</html>
