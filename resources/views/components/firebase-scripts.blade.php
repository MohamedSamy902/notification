@props(['topic' => null])

<!-- Firebase Scripts Component -->
@once
    @if(!defined('FIREBASE_SCRIPTS_LOADED'))
        @php define('FIREBASE_SCRIPTS_LOADED', true); @endphp
        
        <!-- Firebase SDK -->
        <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js"></script>
        
        <!-- Axios (if not already loaded) -->
        <script>
            if (typeof axios === 'undefined') {
                document.write('<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"><\/script>');
            }
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Ensure cookies are sent with requests
                if (typeof axios !== 'undefined') {
                    axios.defaults.withCredentials = true;
                }

                // 1. Configuration from Laravel Config
                const firebaseConfig = {
                    apiKey: "{{ config('advanced-notifications.firebase.frontend.api_key') }}",
                    authDomain: "{{ config('advanced-notifications.firebase.frontend.auth_domain') }}",
                    projectId: "{{ config('advanced-notifications.firebase.frontend.project_id') }}",
                    storageBucket: "{{ config('advanced-notifications.firebase.frontend.storage_bucket') }}",
                    messagingSenderId: "{{ config('advanced-notifications.firebase.frontend.messaging_sender_id') }}",
                    appId: "{{ config('advanced-notifications.firebase.frontend.app_id') }}"
                };

                // Check if config is valid
                if (!firebaseConfig.apiKey) {
                    console.error('AdvancedNotifications: Firebase frontend configuration is missing. Please check your .env file.');
                    return;
                }

                // 2. Initialize Firebase
                firebase.initializeApp(firebaseConfig);
                const messaging = firebase.messaging();

                // 3. Main Function
                async function initNotifications() {
                    try {
                        const permission = await Notification.requestPermission();
                        if (permission === 'granted') {
                            const vapidKey = "{{ config('advanced-notifications.firebase.frontend.vapid_key') }}";
                            if (!vapidKey) {
                                console.error('AdvancedNotifications: VAPID Key is missing.');
                                return;
                            }

                            const token = await messaging.getToken({ vapidKey: vapidKey });
                            
                            if (token) {
                                console.log('FCM Token:', token);
                                await registerToken(token);
                            } else {
                                console.log('No registration token available.');
                            }
                        } else {
                            console.log('Unable to get permission to notify.');
                        }
                    } catch (error) {
                        console.error('An error occurred while retrieving token. ', error);
                    }
                }

                // 4. Register Token API
                async function registerToken(token) {
                    try {
                        // Register Token (Using Web-API endpoint for Session Auth)
                        await axios.post('/web-api/notifications/register-token', {
                            token: token,
                            device_type: 'web'
                        });
                        console.log('Token registered successfully.');

                        // Subscribe to Topic if provided
                        const topicName = "{{ $topic }}";
                        if (topicName) {
                            // Using Web-API endpoint
                            let subscribeUrl = `/web-api/notifications/topics/${topicName}/subscribe`;
                            
                            await axios.post(subscribeUrl, { token: token });
                            console.log(`Subscribed to topic: ${topicName}`);
                        }

                    } catch (error) {
                        console.error('Error registering token or subscribing:', error);
                    }
                }

                // 5. Handle Foreground Messages
                messaging.onMessage((payload) => {
                    console.log('Message received. ', payload);

                    // 1. Dispatch a custom event
                    const event = new CustomEvent('fcm-notification-received', {
                        detail: payload
                    });
                    window.dispatchEvent(event);

                    // 2. Default behavior: Show Native Notification
                    if (Notification.permission === "granted" && payload.notification) {
                        const notificationTitle = payload.notification.title || 'New Notification';
                        const notificationOptions = {
                            body: payload.notification.body,
                            icon: "{{ config('advanced-notifications.firebase.frontend.icon') }}", // Configurable Icon
                            data: payload.data
                        };

                        new Notification(notificationTitle, notificationOptions);
                    }
                });

                // Start
                initNotifications();
            });
        </script>
    @endif
@endonce
