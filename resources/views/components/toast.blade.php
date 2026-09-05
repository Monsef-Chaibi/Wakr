@if (session('toast'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Toast.fire({
                icon: @json(session('toast.icon', 'success')),
                title: @json(session('toast.message')),
            });
        });
    </script>
@endif
