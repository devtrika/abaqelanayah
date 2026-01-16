<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Device Token Tester</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; padding: 24px; background: #0f172a; color: #e5e7eb; }
        .card { max-width: 800px; margin: 0 auto; background: #111827; border: 1px solid #1f2937; border-radius: 12px; padding: 20px; }
        h1 { font-size: 22px; margin: 0 0 16px 0; }
        .row { display: flex; gap: 12px; align-items: center; margin-bottom: 14px; }
        .btn { padding: 8px 14px; border-radius: 8px; border: 1px solid #374151; background: #1f2937; color: #e5e7eb; cursor: pointer; }
        .btn:hover { background: #374151; }
        .status { font-size: 13px; color: #93c5fd; }
        .error { color: #fca5a5; }
        textarea { width: 100%; min-height: 140px; border-radius: 8px; border: 1px solid #374151; background: #0b1220; color: #e5e7eb; padding: 10px; font-size: 13px; }
        .small { font-size: 12px; color: #9ca3af; }
        code { background: #0b1220; padding: 2px 6px; border-radius: 6px; }
    </style>
    <!-- Firebase v10 compat to match service worker -->
    <script src="https://www.gstatic.com/firebasejs/10.14.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.14.0/firebase-messaging-compat.js"></script>
</head>
<body>
    <div class="card">
        <h1>Web Push Device Token</h1>
        <div id="status" class="status"></div>
        <div class="row">
            <button id="btn-permission" class="btn">Request Notifications Permission</button>
            <button id="btn-get-token" class="btn">Get Device Token</button>
            <button id="btn-copy" class="btn">Copy Token</button>
        </div>
        <textarea id="token" placeholder="Token will appear here..." readonly></textarea>
        <p class="small">Requires HTTPS (or <code>localhost</code>) and a modern desktop browser. Ensure <code>public/firebase-web-config.json</code> contains a valid <code>vapidKey</code>.</p>
    </div>

    <script>
        const statusEl = document.getElementById('status');
        const tokenEl = document.getElementById('token');
        const btnPermission = document.getElementById('btn-permission');
        const btnGetToken = document.getElementById('btn-get-token');
        const btnCopy = document.getElementById('btn-copy');

        function setStatus(msg, isError = false) {
            statusEl.textContent = msg;
            statusEl.className = isError ? 'status error' : 'status';
        }

        function isSecureOrigin() {
            const isHttps = location.protocol === 'https:';
            const isLocalhost = location.hostname === 'localhost' || location.hostname === '127.0.0.1';
            return isHttps || isLocalhost;
        }

        async function loadConfig() {
            const res = await fetch('/firebase-web-config.json', { cache: 'no-store' });
            if (!res.ok) throw new Error('Failed to load firebase-web-config.json');
            const raw = await res.json();
            // Support both { apiKey, ... , vapidKey } and { config: { ... }, vapidKey }
            const cfg = raw.config ? { ...raw.config, vapidKey: raw.vapidKey } : raw;
            const missing = [];
            if (!cfg.apiKey) missing.push('apiKey');
            if (!cfg.messagingSenderId) missing.push('messagingSenderId');
            if (!cfg.appId) missing.push('appId');
            if (missing.length) {
                throw new Error('Invalid Firebase config: missing ' + missing.join(', '));
            }
            if (!cfg.vapidKey) {
                setStatus('Missing vapidKey in firebase-web-config.json. Set your Web Push key.', true);
            }
            return cfg;
        }

        async function ensureSupported() {
            if (!isSecureOrigin()) {
                throw new Error('Insecure origin detected. Use HTTPS or localhost for service workers.');
            }
            if (!('Notification' in window)) {
                throw new Error('Notifications not supported by this browser');
            }
            if (!('serviceWorker' in navigator)) {
                throw new Error('Service workers not supported by this browser');
            }
            if (firebase.messaging.isSupported) {
                const supported = await firebase.messaging.isSupported();
                if (!supported) throw new Error('Firebase Messaging not supported in this browser');
            }
        }

        async function registerServiceWorker() {
            const reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            return reg;
        }

        btnPermission.addEventListener('click', async () => {
            try {
                await ensureSupported();
                const perm = await Notification.requestPermission();
                setStatus(perm === 'granted' ? 'Notifications permission granted' : 'Notifications permission denied');
            } catch (e) {
                setStatus(e.message || String(e), true);
            }
        });

        btnGetToken.addEventListener('click', async () => {
            try {
                setStatus('Loading config...');
                await ensureSupported();
                const cfg = await loadConfig();

                setStatus('Initializing Firebase...');
                firebase.initializeApp(cfg);

                setStatus('Registering service worker...');
                const registration = await registerServiceWorker();

                setStatus('Requesting token...');
                const messaging = firebase.messaging();
                const tok = await messaging.getToken({
                    vapidKey: cfg.vapidKey,
                    serviceWorkerRegistration: registration,
                });

                if (!tok) throw new Error('No token returned. Check vapidKey and permissions.');
                tokenEl.value = tok;
                localStorage.setItem('device_id', tok);
                setStatus('Token generated successfully');
            } catch (e) {
                console.error(e);
                let msg = e && e.message ? e.message : String(e);
                // Provide targeted guidance for common FCM web errors
                if (msg.includes('messaging/token-subscribe-failed')) {
                    msg = 'FCM subscription failed: Ensure vapidKey matches the Firebase project, disable ad-blockers, and use HTTPS or localhost.';
                }
                if (msg.includes('missing required authentication credential')) {
                    msg += ' Tip: Verify API key and sender ID belong to the same Firebase project, and that service worker config matches the page config.';
                }
                setStatus(msg, true);
            }
        });

        btnCopy.addEventListener('click', async () => {
            try {
                if (!tokenEl.value) throw new Error('No token to copy');
                await navigator.clipboard.writeText(tokenEl.value);
                setStatus('Token copied to clipboard');
            } catch (e) {
                setStatus(e.message || String(e), true);
            }
        });

        // Initial status
        if (!isSecureOrigin()) {
            setStatus('Insecure origin. Use HTTPS or localhost (required for SW).', true);
        } else {
            setStatus('Ready. Request permission then generate token.');
        }
    </script>
</body>
</html>
