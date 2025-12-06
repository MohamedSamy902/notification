@props(['userId' => null])

@once
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Laravel Echo & Pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Setup Pusher
            if (typeof window.Pusher === 'undefined') {
                window.Pusher = Pusher;
            }

            // 2. Setup Echo Instance
            // Check if Echo is not defined OR it is defined but looks like the Class (no .private method)
            if (typeof window.Echo === 'undefined' || typeof window.Echo.private !== 'function') {
                
                // If window.Echo exists (as a Class from CDN), use it. Otherwise assume it's not loaded yet (which would be an error, but let's try).
                const EchoClass = window.Echo;
                
                if (typeof EchoClass === 'function') {
                    window.Echo = new EchoClass({
                        broadcaster: 'reverb',
                        key: "{{ config('broadcasting.connections.reverb.key', env('VITE_REVERB_APP_KEY')) }}",
                        wsHost: "{{ config('broadcasting.connections.reverb.options.host', env('VITE_REVERB_HOST')) }}",
                        wsPort: {{ config('broadcasting.connections.reverb.options.port', env('VITE_REVERB_PORT', 8080)) }},
                        wssPort: {{ config('broadcasting.connections.reverb.options.port', env('VITE_REVERB_PORT', 443)) }},
                        forceTLS: {{ config('broadcasting.connections.reverb.options.scheme', env('VITE_REVERB_SCHEME', 'https')) === 'https' ? 'true' : 'false' }},
                        enabledTransports: ['ws', 'wss'],
                    });
                    console.log('AdvancedNotifications: Echo initialized with Reverb.');
                } else {
                    console.error('AdvancedNotifications: Echo Class not found. Make sure the CDN script is loaded.');
                }
            }

            // 3. Subscribe to Private Channel
            @if($userId)
                if (window.Echo && typeof window.Echo.private === 'function') {
                    window.Echo.private('App.Models.User.{{ $userId }}')
                        .notification((notification) => {
                        .notification((notification) => {
                            // 1. Track Delivered
                            if (notification.id) {
                                axios.post(`/api/notifications/${notification.id}/delivered`).catch(e => console.error(e));
                            }

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
                                    
                                    // Add click listener to toast
                                    toast.addEventListener('click', () => {
                                        if (notification.id) {
                                            axios.post(`/api/notifications/${notification.id}/clicked`).then(() => {
                                                if (notification.action_url) {
                                                    window.location.href = notification.action_url;
                                                }
                                            });
                                        } else if (notification.action_url) {
                                            window.location.href = notification.action_url;
                                        }
                                    });
                                }
                            });
                        });
                }
            @endif
        });
    </script>
@endonce
