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
    @livewireStyles
    @livewireStyles
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
                <div class="birds-page-heading"><div><span class="dashboard-eyebrow">{{ __('dashboard_workspace') }}</span><h1>{{ __($workspaceType . '_title') }}</h1><p>{{ __($workspaceType . '_subtitle') }}</p></div><button class="dashboard-primary-button" data-workspace-add type="button"><span>+</span>{{ __($workspaceData['add']) }}</button></div>
                @if ($workspaceType === 'cages')
                    <livewire:cages.cage-list />
                @elseif ($workspaceType === 'breeding')
                    <livewire:breeding.pair-list />
                @else
                    <livewire:records.record-list />
                @endif
            </main>
    </x-dashboard-shell>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('dashboard-sidebar'); const overlay = document.querySelector('.sidebar-overlay'); const languageToggle = document.getElementById('dashboard-language-toggle'); const search = document.querySelector('.birds-search input'); const rows = [...document.querySelectorAll('.birds-table tbody tr')];
            const setSidebar = (open) => { sidebar.classList.toggle('is-open', open); overlay.classList.toggle('is-visible', open); };
            document.querySelector('[data-sidebar-open]').addEventListener('click', () => setSidebar(true)); document.querySelectorAll('[data-sidebar-close]').forEach((button) => button.addEventListener('click', () => setSidebar(false)));
            languageToggle.addEventListener('change', () => { window.location.href = languageToggle.checked ? languageToggle.dataset.arUrl : languageToggle.dataset.enUrl; });
            search.addEventListener('input', () => { const term = search.value.toLowerCase(); rows.forEach((row) => { row.hidden = !row.textContent.toLowerCase().includes(term); }); });
            document.querySelector('[data-workspace-add]')?.addEventListener('click', () => {
                const componentElement = document.querySelector('[wire\\:id]');
                const component = componentElement
                    ? window.Livewire.find(componentElement.getAttribute('wire:id'))
                    : null;

                component?.call('openCreateForm');
            });

            document.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('[data-delete-cage]');
                const deletePairButton = event.target.closest('[data-delete-pair]');

                if (!deleteButton && !deletePairButton) {
                    return;
                }

                event.preventDefault();
                event.stopImmediatePropagation();

                const result = await window.Swal.fire({
                    title: deleteButton ? 'Delete this cage?' : 'Delete this breeding pair?',
                    text: deleteButton
                        ? `${deleteButton.dataset.cageName} will be deleted and its birds unassigned.`
                        : `${deletePairButton.dataset.pairName} will be permanently deleted.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#78908a',
                    reverseButtons: true,
                });

                if (!result.isConfirmed) {
                    return;
                }

                const activeDeleteButton = deleteButton ?? deletePairButton;
                const componentElement = activeDeleteButton.closest('[wire\\:id]');
                const component = componentElement
                    ? window.Livewire.find(componentElement.getAttribute('wire:id'))
                    : null;

                if (deleteButton) {
                    component?.call('deleteCage', Number(deleteButton.dataset.deleteCage));
                } else {
                    component?.call('deletePair', Number(deletePairButton.dataset.deletePair));
                }
            }, true);

            window.addEventListener('cage-deleted', () => {
                window.Toast?.fire({
                    icon: 'success',
                    title: 'Cage deleted successfully.',
                    background: '#a5dc86',
                    iconColor: '#1f4b39',
                });
            });

            window.addEventListener('pair-deleted', () => {
                window.Toast?.fire({
                    icon: 'success',
                    title: 'Breeding pair deleted successfully.',
                    background: '#a5dc86',
                    iconColor: '#1f4b39',
                });
            });
        });
    </script>
    @livewireScripts
    @livewireScripts
</body>
</html>
