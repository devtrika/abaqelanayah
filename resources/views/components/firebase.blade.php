<!-- FireBase -->
<!-- The core Firebase JS SDK is always required and must be listed first -->
{{-- <script src="https://www.gstatic.com/firebasejs/7.6.1/firebase.js"></script> --}}
<script src="https://www.gstatic.com/firebasejs/7.6.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.6.1/firebase-messaging.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.6.1/firebase-analytics.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.6.1/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.6.1/firebase-firestore.js"></script>


<script>
    (async function(){
        try{
            var res = await fetch('/firebase-web-config.json', { cache: 'no-store' });
            var raw = await res.json();
            var cfg = raw.config ? raw.config : raw;
            firebase.initializeApp(cfg);
            var messaging = firebase.messaging();
            window.fcmMessageing = messaging;
            if ('serviceWorker' in navigator) {
                await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            }
            var permission = await Notification.requestPermission();
            if (permission !== 'granted') return;
            var vapidKey = raw.vapidKey || cfg.vapidKey;
            var token = await messaging.getToken({ vapidKey: vapidKey });
            if (token) {
                localStorage.setItem('device_id', token);
                var deviceIdInput = document.getElementById('device_id');
                if (deviceIdInput) deviceIdInput.value = token;
            }
            messaging.onMessage(function(payload){
                var title = (payload.notification && payload.notification.title) || (payload.data && (payload.data.title || payload.data.title_ar || payload.data.title_en)) || 'إشعار جديد';
                var body  = (payload.notification && payload.notification.body) || (payload.data && (payload.data.body || payload.data.body_ar || payload.data.message_ar || payload.data.message_en)) || 'لديك رسالة جديدة';
                if (Notification.permission === 'granted'){
                    var n = new Notification(title,{ body: body, icon: '/favicon.ico', tag: 'fcm-notification-'+Date.now(), requireInteraction: false, silent: false });
                    n.onclick = function(){ window.focus(); n.close(); if (payload.data && payload.data.url) { window.location.href = payload.data.url; } };
                }
                alert('لديك رسالة جديدة : ' + body);
            });
        }catch(e){
            console.warn('Firebase init failed', e);
        }
    })();
</script>
