@if (session('toast'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const icon = @json(session('toast.icon', 'success'));

            Toast.fire({
                icon: icon,
                title: @json(session('toast.message')),
                background: icon === 'error' ? '#f27474' : '#a5dc86',
                iconColor: icon === 'error' ? '#b94f3c' : '#1f4b39',
            });
        });
    </script>
@endif
