
importScripts("https://www.gstatic.com/firebasejs/10.14.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.14.0/firebase-messaging-compat.js");
// For an optimal experience using Cloud Messaging, also add the Firebase SDK for Analytics.

// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
// Keep this config in sync with public/firebase-web-config.json
var firebaseConfig = {
 apiKey: "AIzaSyBTqszgCPShswQmoP4NpqoyY0II-CYCfqw",
  authDomain: "lia-project-228c5.firebaseapp.com",
  projectId: "lia-project-228c5",
  storageBucket: "lia-project-228c5.firebasestorage.app",
  messagingSenderId: "80014472128",
  appId: "1:80014472128:web:646f95f38739819cf84e25",
  measurementId: "G-XBPKYDCP86"
};

firebase.initializeApp(firebaseConfig)

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();

// Note: VAPID key should be configured in the main app, not in the service worker
// The service worker will automatically use the VAPID key set in the main application

messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    // Extract notification data
    const notificationTitle = payload.notification?.title || payload.data?.title || 'New Notification';
    const notificationBody = payload.notification?.body || payload.data?.body || 'You have a new message';

    const notificationOptions = {
        body: notificationBody,
        icon: '/firebase-logo.png', // Add your app icon here
        badge: '/firebase-logo.png',
        tag: 'notification-tag',
        requireInteraction: false,
        data: payload.data || {},
        actions: [
            {
                action: 'open',
                title: 'Open App'
            },
            {
                action: 'close',
                title: 'Close'
            }
        ]
    };

    return self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click events
self.addEventListener('notificationclick', function(event) {
    console.log('[firebase-messaging-sw.js] Notification click received.');

    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    // Handle notification click - open the app
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // If app is already open, focus it
            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    return client.focus();
                }
            }
            // If app is not open, open it
            if (clients.openWindow) {
                return clients.openWindow('/');
            }
        })
    );
});
