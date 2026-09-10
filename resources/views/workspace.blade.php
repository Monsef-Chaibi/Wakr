<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __($workspaceType . '_title') }} | {{ __('app_name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=ADLaM+Display&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
</head>
<body class="dashboard-page workspace-page">
    @php
        $isArabic = app()->getLocale() === 'ar';
        $workspaceData = [
            'cages' => ['add' => 'cages_add', 'stats' => ['cages_total', 'cages_occupied', 'cages_available'], 'search' => 'cages_search'],
            'breeding' => ['add' => 'breeding_add', 'stats' => ['breeding_total', 'breeding_nesting', 'breeding_hatching'], 'search' => 'breeding_search'],
            'records' => ['add' => 'records_add', 'stats' => ['records_total', 'records_health', 'records_breeding'], 'search' => 'records_search'],
        ][$workspaceType];
    @endphp
    <x-dashboard-shell :is-arabic="$isArabic" :active-page="$workspaceType">
            <main class="dashboard-content workspace-content">
                <div class="birds-page-heading"><div><span class="dashboard-eyebrow">{{ __('dashboard_workspace') }}</span><h1>{{ __($workspaceType . '_title') }}</h1><p>{{ __($workspaceType . '_subtitle') }}</p></div><button class="dashboard-primary-button" type="button"><span>+</span>{{ __($workspaceData['add']) }}</button></div>
                <section class="birds-stats" aria-label="{{ __($workspaceType . '_title') }}"><article class="birds-stat"><span class="birds-stat-icon birds-stat-green">&#9670;</span><div><span>{{ __($workspaceData['stats'][0]) }}</span><strong>{{ $workspaceType === 'cages' ? '24' : ($workspaceType === 'breeding' ? '16' : '248') }}</strong></div></article><article class="birds-stat"><span class="birds-stat-icon birds-stat-blue">&#10003;</span><div><span>{{ __($workspaceData['stats'][1]) }}</span><strong>{{ $workspaceType === 'cages' ? '18' : ($workspaceType === 'breeding' ? '7' : '84') }}</strong></div></article><article class="birds-stat"><span class="birds-stat-icon birds-stat-coral">&#9672;</span><div><span>{{ __($workspaceData['stats'][2]) }}</span><strong>{{ $workspaceType === 'cages' ? '6' : ($workspaceType === 'breeding' ? '4' : '32') }}</strong></div></article></section>
                <section class="birds-panel panel"><div class="birds-toolbar"><label class="birds-search"><span aria-hidden="true">&#9906;</span><input type="search" placeholder="{{ __($workspaceData['search']) }}" aria-label="{{ __($workspaceData['search']) }}"></label><span class="workspace-toolbar-note">{{ __('program_upcoming') }}</span></div>
                    <div class="birds-table-wrap">
                        @if ($workspaceType === 'cages')
                            <table class="birds-table"><thead><tr><th>{{ __('cages_table_cage') }}</th><th>{{ __('cages_table_location') }}</th><th>{{ __('cages_table_capacity') }}</th><th>{{ __('cages_table_occupancy') }}</th><th>{{ __('cages_table_status') }}</th></tr></thead><tbody><tr><td><div class="bird-identity"><span class="bird-avatar bird-avatar-green">04</span><span><strong>Cage 04</strong><small>CN-0004</small></span></div></td><td>Breeding room</td><td>8 birds</td><td>4 / 8</td><td><span class="bird-status status-active">{{ __('cages_status_active') }}</span></td></tr><tr><td><div class="bird-identity"><span class="bird-avatar bird-avatar-yellow">02</span><span><strong>Cage 02</strong><small>CN-0002</small></span></div></td><td>North wall</td><td>4 birds</td><td>2 / 4</td><td><span class="bird-status status-pair">{{ __('cages_status_active') }}</span></td></tr><tr><td><div class="bird-identity"><span class="bird-avatar bird-avatar-blue">07</span><span><strong>Cage 07</strong><small>CN-0007</small></span></div></td><td>Nursery</td><td>6 birds</td><td>0 / 6</td><td><span class="bird-status status-resting">{{ __('cages_status_available') }}</span></td></tr></tbody></table>
                        @elseif ($workspaceType === 'breeding')
                            <table class="birds-table"><thead><tr><th>{{ __('breeding_table_pair') }}</th><th>{{ __('breeding_table_birds') }}</th><th>{{ __('breeding_table_cage') }}</th><th>{{ __('breeding_table_eggs') }}</th><th>{{ __('breeding_table_next') }}</th></tr></thead><tbody><tr><td><div class="bird-identity"><span class="bird-avatar bird-avatar-coral">L/S</span><span><strong>Luna & Sol</strong><small>PAIR-0016</small></span></div></td><td>Lovebirds</td><td>Cage 02</td><td>4</td><td><span class="bird-status status-pair">{{ __('breeding_status_hatching') }}</span></td></tr><tr><td><div class="bird-identity"><span class="bird-avatar bird-avatar-green">M/B</span><span><strong>Milo & Bibi</strong><small>PAIR-0015</small></span></div></td><td>Budgerigars</td><td>Cage 04</td><td>0</td><td><span class="bird-status status-active">{{ __('breeding_status_ready') }}</span></td></tr><tr><td><div class="bird-identity"><span class="bird-avatar bird-avatar-yellow">C/R</span><span><strong>Coco & Rio</strong><small>PAIR-0014</small></span></div></td><td>Cockatiels</td><td>Cage 08</td><td>3</td><td><span class="bird-status status-resting">{{ __('breeding_status_nesting') }}</span></td></tr></tbody></table>
                        @else
                            <table class="birds-table"><thead><tr><th>{{ __('records_table_activity') }}</th><th>{{ __('records_table_subject') }}</th><th>{{ __('records_table_type') }}</th><th>{{ __('records_table_date') }}</th><th>{{ __('records_table_notes') }}</th></tr></thead><tbody><tr><td><strong>New bird added</strong></td><td>Sky · BRD-00128</td><td><span class="bird-status status-active">{{ __('records_type_flock') }}</span></td><td>Jun 14, 2026</td><td>Joined the flock</td></tr><tr><td><strong>Health check completed</strong></td><td>Luna · BRD-00127</td><td><span class="bird-status status-resting">{{ __('records_type_health') }}</span></td><td>Jun 13, 2026</td><td>Routine check</td></tr><tr><td><strong>Pairing updated</strong></td><td>Luna & Sol</td><td><span class="bird-status status-pair">{{ __('records_type_breeding') }}</span></td><td>Jun 12, 2026</td><td>Pair confirmed</td></tr></tbody></table>
                        @endif
                    </div>
                </section>
            </main>
    </x-dashboard-shell>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('dashboard-sidebar'); const overlay = document.querySelector('.sidebar-overlay'); const languageToggle = document.getElementById('dashboard-language-toggle'); const search = document.querySelector('.birds-search input'); const rows = [...document.querySelectorAll('.birds-table tbody tr')];
            const setSidebar = (open) => { sidebar.classList.toggle('is-open', open); overlay.classList.toggle('is-visible', open); };
            document.querySelector('[data-sidebar-open]').addEventListener('click', () => setSidebar(true)); document.querySelectorAll('[data-sidebar-close]').forEach((button) => button.addEventListener('click', () => setSidebar(false)));
            languageToggle.addEventListener('change', () => { window.location.href = languageToggle.checked ? languageToggle.dataset.arUrl : languageToggle.dataset.enUrl; });
            search.addEventListener('input', () => { const term = search.value.toLowerCase(); rows.forEach((row) => { row.hidden = !row.textContent.toLowerCase().includes(term); }); });
        });
    </script>
</body>
</html>
