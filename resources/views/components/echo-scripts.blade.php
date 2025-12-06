@props(['userId' => null])

@once
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Laravel Echo & Pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.Echo === 'undefined') {
                window.Pusher = Pusher;

                window.Echo = new Echo({
                    broadcaster: 'reverb',
                    key: "{{ config('broadcasting.connections.reverb.key', env('VITE_REVERB_APP_KEY')) }}",
                    wsHost: "{{ config('broadcasting.connections.reverb.options.host', env('VITE_REVERB_HOST')) }}",
                    wsPort: {{ config('broadcasting.connections.reverb.options.port', env('VITE_REVERB_PORT', 8080)) }},
                    wssPort: {{ config('broadcasting.connections.reverb.options.port', env('VITE_REVERB_PORT', 443)) }},
                    forceTLS: {{ config('broadcasting.connections.reverb.options.scheme', env('VITE_REVERB_SCHEME', 'https')) === 'https' ? 'true' : 'false' }},
                    enabledTransports: ['ws', 'wss'],
                });
            }

            @if($userId)
                Echo.private('App.Models.User.{{ $userId }}')
                    .notification((notification) => {
                        console.log('Reverb Notification:', notification);
                        
                        Swal.fire({
                            title: notification.title,
                            text: notification.body,
                            icon: 'info',
                            toast: true,
                            position: 'top-start',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                    });
            @endif
        });
    </script>
@endonce
