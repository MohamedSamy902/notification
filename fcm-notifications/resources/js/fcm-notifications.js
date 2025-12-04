/**
 * FCM Notifications - Frontend Handler
 * 
 * هذا الملف يحتوي على كود JavaScript لمعالجة الإشعارات في المتصفح
 * يدعم Sweet Alert وإشعارات النظام مع إمكانية التخصيص الكامل
 */

// تهيئة Firebase
if (!firebase.apps.length) {
    const firebaseConfig = {
        apiKey: "YOUR_API_KEY",
        authDomain: "YOUR_AUTH_DOMAIN",
        projectId: "YOUR_PROJECT_ID",
        storageBucket: "YOUR_STORAGE_BUCKET",
        messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
        appId: "YOUR_APP_ID"
    };
    firebase.initializeApp(firebaseConfig);
}

const messaging = firebase.messaging();

/**
 * إعدادات العرض - يتم جلبها من الـ Backend
 * يمكنك تمرير هذه الإعدادات من Blade Template
 */
const notificationConfig = {
    displayType: 'both',  // 'system', 'sweet_alert', 'both'
    sweetAlert: {
        enabled: true,
        position: 'top-end',
        timer: 5000,
        toast: true,
        showConfirmButton: false,
        iconType: 'info',
        showCloseButton: true,
        allowOutsideClick: true
    },
    system: {
        enabled: true,
        requireInteraction: true,
        badge: '/favicon.ico'
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
                vapidKey: 'YOUR_VAPID_KEY'
            });
        } else {
            console.log('Unable to get permission to notify.');
            return null;
        }
    }).then((token) => {
        if (token) {
            console.log('FCM Token:', token);
            saveTokenToDatabase(token);
        } else {
            console.log('No registration token available.');
        }
    }).catch((err) => {
        console.log('An error occurred while retrieving token. ', err);
    });
}

/**
 * حفظ التوكن في قاعدة البيانات
 */
function saveTokenToDatabase(token) {
    // عرض التوكن في الصفحة إذا كان العنصر موجوداً
    const tokenDisplayElement = document.getElementById('fcm-token-display');
    if (tokenDisplayElement) {
        tokenDisplayElement.innerText = token;
        tokenDisplayElement.value = token;
    }

    // إرسال التوكن للسيرفر
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
}

/**
 * عرض الإشعار باستخدام Sweet Alert
 */
function showSweetAlertNotification(payload) {
    if (!notificationConfig.sweetAlert.enabled) return;

    // تحديد نوع الأيقونة بناءً على البيانات أو استخدام الافتراضي
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

    // إضافة الصورة إذا كانت موجودة
    if (payload.notification.image) {
        swalConfig.imageUrl = payload.notification.image;
        swalConfig.imageHeight = 100;
        swalConfig.imageAlt = payload.notification.title;
    }

    // عرض الإشعار
    Swal.fire(swalConfig).then((result) => {
        // إذا ضغط المستخدم على الإشعار
        if (result.isConfirmed || result.isDismissed) {
            const link = (payload.data && payload.data.link) || '/';
            if (link !== '/') {
                window.open(link, '_blank');
            }
        }
    });

    // إضافة حدث النقر على Toast
    if (notificationConfig.sweetAlert.toast) {
        const swalContainer = document.querySelector('.swal2-container');
        if (swalContainer) {
            swalContainer.addEventListener('click', function() {
                const link = (payload.data && payload.data.link) || '/';
                if (link !== '/') {
                    window.open(link, '_blank');
                }
            }, { once: true });
        }
    }
}

/**
 * عرض إشعار النظام (System Notification)
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

    // التحقق من دعم المتصفح للإشعارات
    if (!("Notification" in window)) {
        console.log("This browser does not support system notifications");
        return;
    }

    // التحقق من الإذن
    if (Notification.permission === "granted") {
        const notification = new Notification(notificationTitle, notificationOptions);
        
        // معالجة النقر على الإشعار
        notification.onclick = function(event) {
            event.preventDefault();
            const link = (payload.data && payload.data.link) || '/';
            window.open(link, '_blank');
            notification.close();
        };
    }
}

/**
 * معالجة الإشعارات الواردة (Foreground)
 */
messaging.onMessage(function(payload) {
    console.log('Message received. ', payload);

    // عرض الإشعار حسب الإعدادات
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

// تشغيل التهيئة عند تحميل الصفحة
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFirebaseMessagingRegistration);
} else {
    initFirebaseMessagingRegistration();
}
