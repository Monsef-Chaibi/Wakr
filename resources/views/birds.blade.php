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
    @livewireStyles
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
                    <button id="openModal" class="dashboard-primary-button" type="button"><span>+</span>{{ __('birds_add') }}</button>
                </div>

                <livewire:birds.bird-list />
            </main>

            <div id="modalOverlay"
               class="hidden fixed inset-0 z-1000 items-center justify-center overflow-y-auto bg-slate-950/65 p-4 backdrop-blur-sm">
            <div role="dialog" aria-modal="true" aria-labelledby="modal-title" tabindex="-1"
                class="relative my-auto w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-900/5 outline-none">
                <div class="sticky top-0 z-10 flex items-start justify-between gap-4 border-b border-emerald-100 bg-linear-to-br from-emerald-50 to-white px-6 py-5">
                    <div class="flex items-center gap-3">
                        <span class="grid size-11 place-items-center rounded-xl bg-emerald-600 text-xl font-bold text-white shadow-sm">+</span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-700">Bird management</p>
                            <h3 id="modal-title" class="mt-1 text-xl font-semibold text-slate-900">Add a bird</h3>
                            <p class="mt-1 text-sm text-slate-500">Create a profile and link it to a cage or parent birds.</p>
                        </div>
                    </div>

                    <button type="button" id="closeModal" aria-label="Close modal"
                        class="grid size-9 place-items-center rounded-full text-2xl leading-none text-slate-400 transition hover:bg-white hover:text-slate-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg"
                        class="size-3 cursor-pointer fill-slate-500 hover:fill-red-600"
                        aria-hidden="true" viewBox="0 0 329.269 329">
                        <path
                            d="M194.8 164.77 323.013 36.555c8.343-8.34 8.343-21.825 0-30.164-8.34-8.34-21.825-8.34-30.164 0L164.633 134.605 36.422 6.391c-8.344-8.34-21.824-8.34-30.164 0-8.344 8.34-8.344 21.824 0 30.164l128.21 128.215L6.259 292.984c-8.344 8.34-8.344 21.825 0 30.164a21.27 21.27 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25l128.21-128.214 128.216 128.214a21.27 21.27 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25 8.343-8.34 8.343-21.824 0-30.164zm0 0" />
                        </svg>
                    </button>
                </div>

                <div class="max-h-[calc(100vh-11rem)] overflow-y-auto px-6 py-6">
                <livewire:birds.create-bird />
            </div>
            </div>
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


        const openBtn = document.getElementById("openModal");
        const closeBtn = document.getElementById("closeModal");
        const cancelBtn = document.getElementById("cancelBtn");
        const overlay = document.getElementById("modalOverlay");
        const dialog = overlay.querySelector("[role='dialog']");

        // Open modal and lock body scroll
        openBtn.onclick = () => {
            overlay.classList.remove("hidden");
            overlay.classList.add("flex");
            document.body.style.overflow = "hidden";
            dialog.focus();
        };

        // Close modal and restore focus/scroll
        function closeModal() {
            overlay.classList.add("hidden");
            overlay.classList.remove("flex");
            document.body.style.overflow = "";
            openBtn.focus();
        }

        closeBtn.onclick = cancelBtn.onclick = closeModal;
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                closeModal();
            }
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !overlay.classList.contains('hidden')) {
                closeModal();
            }
        });

        window.addEventListener('bird-created', () => {
            closeModal();
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'success',
                    title: 'Bird added successfully.',
                    background: '#a5dc86',
                    iconColor: '#1f4b39',
                });
            }
        });

        document.addEventListener('click', async (event) => {
            const deleteButton = event.target.closest('[data-delete-bird]');

            if (!deleteButton) {
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            const result = await window.Swal.fire({
                title: 'Delete this bird?',
                text: `${deleteButton.dataset.birdName} will be permanently deleted.`,
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

            const componentElement = deleteButton.closest('[wire\\:id]');
            const component = componentElement
                ? window.Livewire.find(componentElement.getAttribute('wire:id'))
                : null;

            if (component) {
                component.call('deleteBird', Number(deleteButton.dataset.deleteBird));
            }
        }, true);

        window.addEventListener('bird-deleted', () => {
            if (window.Toast) {
                window.Toast.fire({
                    icon: 'success',
                    title: 'Bird deleted successfully.',
                    background: '#a5dc86',
                    iconColor: '#1f4b39',
                });
            }
        });

        // Close when clicking outside the dialog
        overlay.onclick = (e) => {
            if (e.target === overlay) closeModal();
        };

        // Keyboard accessibility
        document.addEventListener("keydown", (e) => {
            if (overlay.classList.contains("hidden")) return;

            // Close on ESC
            if (e.key === "Escape") closeModal();

            // Focus trapping logic
            if (e.key === "Tab") {
                const focusable = dialog.querySelectorAll("button, [href], input, select, textarea, [tabindex]:not([tabindex='-1'])");
                const first = focusable[0];
                const last = focusable[focusable.length - 1];

                if (e.shiftKey && document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                } else if (!e.shiftKey && document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        });
</script>
@livewireScripts

</body>

</html>
