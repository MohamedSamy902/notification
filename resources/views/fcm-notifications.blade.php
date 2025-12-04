{{-- 
    FCM Notifications - Blade Template
    
    استخدم هذا الـ Template في الـ Layout الرئيسي لتطبيقك
    يمكنك تضمينه في أي صفحة تريد استقبال الإشعارات فيها
--}}

{{-- تضمين Sweet Alert 2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- تضمين Firebase SDK --}}
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"></script>

<script>
/**
 * FCM Notifications Handler
 * مدمج مع إعدادات Laravel
 */

// تهيئة Firebase
if (!firebase.apps.length) {
    const firebaseConfig = {
        apiKey: "{{ config('fcm-notifications.firebase.api_key', env('FIREBASE_API_KEY')) }}",
        authDomain: "{{ config('fcm-notifications.firebase.auth_domain', env('FIREBASE_AUTH_DOMAIN')) }}",
        projectId: "{{ config('fcm-notifications.firebase.project_id', env('FIREBASE_PROJECT_ID')) }}",
        storageBucket: "{{ config('fcm-notifications.firebase.storage_bucket', env('FIREBASE_STORAGE_BUCKET')) }}",
        messagingSenderId: "{{ config('fcm-notifications.firebase.messaging_sender_id', env('FIREBASE_MESSAGING_SENDER_ID')) }}",
        appId: "{{ config('fcm-notifications.firebase.app_id', env('FIREBASE_APP_ID')) }}"
    };
    firebase.initializeApp(firebaseConfig);
}

const messaging = firebase.messaging();

// إعدادات العرض من الـ Config
const notificationConfig = {
    displayType: "{{ config('fcm-notifications.display.type', 'both') }}",
    sweetAlert: {
        enabled: {{ config('fcm-notifications.display.sweet_alert.enabled', true) ? 'true' : 'false' }},
        position: "{{ config('fcm-notifications.display.sweet_alert.position', 'top-end') }}",
        timer: {{ config('fcm-notifications.display.sweet_alert.timer', 5000) }},
        toast: {{ config('fcm-notifications.display.sweet_alert.toast', true) ? 'true' : 'false' }},
        showConfirmButton: {{ config('fcm-notifications.display.sweet_alert.show_confirm_button', false) ? 'true' : 'false' }},
        iconType: "{{ config('fcm-notifications.display.sweet_alert.icon_type', 'info') }}",
        showCloseButton: {{ config('fcm-notifications.display.sweet_alert.show_close_button', true) ? 'true' : 'false' }},
        allowOutsideClick: {{ config('fcm-notifications.display.sweet_alert.allow_outside_click', true) ? 'true' : 'false' }}
    },
    system: {
        enabled: {{ config('fcm-notifications.display.system.enabled', true) ? 'true' : 'false' }},
        requireInteraction: {{ config('fcm-notifications.display.system.require_interaction', true) ? 'true' : 'false' }},
        badge: "{{ config('fcm-notifications.display.system.badge', '/favicon.ico') }}"
    }
};

/**
 * طلب إذن الإشعارات والحصول على التوكن
 */
function initFirebaseMessagingRegistration() {
    Notification.requestPermission().then(permission => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            
            return messaging.getToken({ 
                vapidKey: "{{ env('FIREBASE_VAPID_KEY') }}"
            });
        } else {
            console.log('Unable to get permission to notify.');
            return null;
        }
    }).then((token) => {
        if (token) {
            console.log('FCM Token:', token);
            saveTokenToDatabase(token);
        }
    }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
    });
}

/**
 * حفظ التوكن في قاعدة البيانات
 */
function saveTokenToDatabase(token) {
    const tokenDisplayElement = document.getElementById('fcm-token-display');
    if (tokenDisplayElement) {
        tokenDisplayElement.innerText = token;
        tokenDisplayElement.value = token;
    }

    @if(auth()->check())
        fetch('/api/v1/fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ 
                token: token,
                device_type: 'web',
                device_name: navigator.userAgent
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Token saved to DB:', data);
        })
        .catch((error) => {
            console.error('Error saving token:', error);
        });
    @else
        console.log('User not logged in, skipping token save.');
    @endif
}

/**
 * عرض الإشعار باستخدام Sweet Alert
 */
function showSweetAlertNotification(payload) {
    if (!notificationConfig.sweetAlert.enabled) return;

    const iconType = (payload.data && payload.data.type) || notificationConfig.sweetAlert.iconType;

    const swalConfig = {
        title: payload.notification.title,
        text: payload.notification.body,
        icon: iconType,
        position: notificationConfig.sweetAlert.position,
        toast: notificationConfig.sweetAlert.toast,
        timer: notificationConfig.sweetAlert.timer,
        showConfirmButton: notificationConfig.sweetAlert.showConfirmButton,
        showCloseButton: notificationConfig.sweetAlert.showCloseButton,
        allowOutsideClick: notificationConfig.sweetAlert.allowOutsideClick,
        timerProgressBar: true,
    };

    if (payload.notification.image) {
        swalConfig.imageUrl = payload.notification.image;
        swalConfig.imageHeight = 100;
        swalConfig.imageAlt = payload.notification.title;
    }

    Swal.fire(swalConfig).then((result) => {
        if (result.isConfirmed || result.isDismissed) {
            const link = (payload.data && payload.data.link) || '/';
            if (link !== '/') {
                window.open(link, '_blank');
            }
        }
    });

    if (notificationConfig.sweetAlert.toast) {
        setTimeout(() => {
            const swalContainer = document.querySelector('.swal2-container');
            if (swalContainer) {
                swalContainer.style.cursor = 'pointer';
                swalContainer.addEventListener('click', function() {
                    const link = (payload.data && payload.data.link) || '/';
                    if (link !== '/') {
                        window.open(link, '_blank');
                    }
                }, { once: true });
            }
        }, 100);
    }
}

/**
 * عرض إشعار النظام
 */
function showSystemNotification(payload) {
    if (!notificationConfig.system.enabled) return;

    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon || payload.notification.image || '/favicon.ico',
        image: payload.notification.image || null,
        badge: notificationConfig.system.badge,
        requireInteraction: notificationConfig.system.requireInteraction,
        data: payload.data || {}
    };

    if (!("Notification" in window)) {
        console.log("This browser does not support system notifications");
        return;
    }

    if (Notification.permission === "granted") {
        const notification = new Notification(notificationTitle, notificationOptions);
        
        notification.onclick = function(event) {
            event.preventDefault();
            const link = (payload.data && payload.data.link) || '/';
            window.open(link, '_blank');
            notification.close();
        };
    }
}

/**
 * معالجة الإشعارات الواردة
 */
messaging.onMessage(function(payload) {
    console.log('Message received. ', payload);

    switch(notificationConfig.displayType) {
        case 'sweet_alert':
            showSweetAlertNotification(payload);
            break;
        case 'system':
            showSystemNotification(payload);
            break;
        case 'both':
            showSweetAlertNotification(payload);
            showSystemNotification(payload);
            break;
        default:
            console.warn('Invalid display type:', notificationConfig.displayType);
    }
});

// تشغيل التهيئة
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFirebaseMessagingRegistration);
} else {
    initFirebaseMessagingRegistration();
}
</script>
