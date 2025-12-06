@props(['firebaseConfig'])

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-app.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging.js";

    const firebaseConfig = @json($firebaseConfig);

    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app);

    function requestPermission() {
        console.log('Requesting permission...');
        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                console.log('Notification permission granted.');
                // Get Token
                getToken(messaging, { vapidKey: '{{ config('advanced-notifications.firebase.vapid_key') }}' }).then((currentToken) => {
                    if (currentToken) {
                        console.log('Token:', currentToken);
                        // Send token to server
                        registerToken(currentToken);
                    } else {
                        console.log('No registration token available. Request permission to generate one.');
                    }
                }).catch((err) => {
                    console.log('An error occurred while retrieving token. ', err);
                });
            } else {
                console.log('Unable to get permission to notify.');
            }
        });
    }

    function registerToken(token) {
        fetch('/api/notifications/register-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Authorization': 'Bearer ' + '{{ auth()->user()?->createToken("fcm")->plainTextToken }}' // Assuming Sanctum or similar, or use session auth if web
            },
            body: JSON.stringify({ token: token, device_type: 'web' })
        }).then(response => {
            if (response.ok) {
                console.log('Token registered on server.');
            } else {
                console.error('Failed to register token on server.');
            }
        });
    }

    // Handle incoming messages. Called when:
    // - a message is received while the app has focus
    // - the user clicks on an app notification created by a service worker
    //   `messaging.onBackgroundMessage` handler.
    onMessage(messaging, (payload) => {
        console.log('Message received. ', payload);
        // Customize notification here
        const notificationTitle = payload.notification.title;
        const notificationOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon
        };

        new Notification(notificationTitle, notificationOptions);
    });

    // Auto-request permission on load (optional, better to trigger on user action)
    requestPermission();

</script>
