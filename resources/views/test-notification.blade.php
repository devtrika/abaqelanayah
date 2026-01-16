<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Firebase Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.14.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.14.0/firebase-messaging-compat.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .test-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }
        .token-display {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
            max-height: 100px;
            overflow-y: auto;
        }
        .result-box {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            display: none;
        }
        .result-box.success {
            background: #d1e7dd;
            border: 1px solid #badbcc;
            color: #0f5132;
        }
        .result-box.error {
            background: #f8d7da;
            border: 1px solid #f5c2c7;
            color: #842029;
        }
        .badge-token {
            background: #667eea;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 11px;
        }
        .notification-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            display: none;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .result-box.info {
            background: #cfe2ff;
            border: 1px solid #b6d4fe;
            color: #084298;
        }
    </style>
</head>
<body>
    <!-- Notification Indicator -->
    <div id="notificationIndicator" class="notification-indicator">
        <i class="bi bi-bell-fill me-2"></i>
        <span id="notificationText">Notification Received!</span>
    </div>

    <div class="container">
        <div class="test-card">
            <div class="text-center mb-4">
                <i class="bi bi-bell-fill text-primary" style="font-size: 48px;"></i>
                <h2 class="mt-3">Firebase Push Notification Tester</h2>
                <p class="text-muted">Test sending notifications to device tokens</p>
                <div id="firebaseStatus" class="mt-2">
                    <span class="badge bg-secondary">
                        <i class="bi bi-hourglass-split me-1"></i>Initializing Firebase...
                    </span>
                </div>
            </div>

            <!-- Device Tokens List -->
            <div class="mb-4">
                <h5><i class="bi bi-phone me-2"></i>Available Device Tokens</h5>
                <div class="list-group">
                    @forelse($tokens as $token)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <i class="bi bi-person-circle me-1"></i>
                                        {{ $token->tokenable->name ?? 'Unknown User' }}
                                        <span class="badge-token">{{ $token->platform ?? 'Unknown' }}</span>
                                    </h6>
                                    <div class="token-display">{{ $token->token }}</div>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>Last used: {{ $token->last_used_at?->diffForHumans() ?? 'Never' }}
                                    </small>
                                </div>
                                <button class="btn btn-primary btn-sm ms-3 send-notification" data-token="{{ $token->token }}" data-user="{{ $token->tokenable->name ?? 'User' }}">
                                    <i class="bi bi-send me-1"></i>Send Test
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No device tokens found. Please register a device token first.
                            <a href="{{ url('/device-token') }}" class="alert-link">Get Token</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Custom Token Input -->
            <div class="mb-4">
                <h5><i class="bi bi-pencil me-2"></i>Or Test Custom Token</h5>
                <div class="input-group mb-2">
                    <input type="text" id="customToken" class="form-control" placeholder="Paste device token here...">
                    <button class="btn btn-success" id="sendCustom">
                        <i class="bi bi-send me-1"></i>Send to Custom Token
                    </button>
                </div>
                <button class="btn btn-outline-primary btn-sm" id="useMyToken">
                    <i class="bi bi-arrow-down-circle me-1"></i>Use My Browser's Token
                </button>
                <button class="btn btn-outline-success btn-sm ms-2" id="saveMyToken">
                    <i class="bi bi-save me-1"></i>Save My Token to Database
                </button>
                <small class="text-muted d-block mt-1">
                    Click "Use My Browser's Token" to fill the field, or "Save My Token" to add it to the database
                </small>
            </div>

            <!-- Notification Form -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-chat-dots me-2"></i>Notification Content
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title (English)</label>
                            <input type="text" id="titleEn" class="form-control" value="Test Notification" placeholder="English title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Title (Arabic)</label>
                            <input type="text" id="titleAr" class="form-control" value="إشعار تجريبي" placeholder="العنوان بالعربية">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Message (English)</label>
                            <textarea id="messageEn" class="form-control" rows="3" placeholder="English message">This is a test notification from Tasawk Dashboard! 🎉</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Message (Arabic)</label>
                            <textarea id="messageAr" class="form-control" rows="3" placeholder="الرسالة بالعربية">هذا إشعار تجريبي من لوحة تحكم تسوق! 🎉</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Box -->
            <div id="resultBox" class="result-box">
                <div id="resultContent"></div>
            </div>

            <!-- Debug Info -->
            <div class="mt-4">
                <details>
                    <summary class="text-muted" style="cursor: pointer;">
                        <i class="bi bi-info-circle me-1"></i>Debug Information
                    </summary>
                    <div class="mt-3 p-3 bg-light rounded">
                        <p><strong>Firebase Project:</strong> {{ config('firebase.credentials') }}</p>
                        <p><strong>Total Tokens:</strong> {{ $tokens->count() }}</p>
                        <p><strong>Queue Connection:</strong> {{ config('queue.default') }}</p>
                        <p class="mb-0"><strong>Test Endpoint:</strong> <code>POST /test-notification/send</code></p>
                    </div>
                </details>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Initialize Firebase for receiving notifications
        let messaging = null;
        let currentBrowserToken = null;

        async function initializeFirebase() {
            const statusEl = document.getElementById('firebaseStatus');

            try {
                // Load Firebase config
                const response = await fetch('/firebase-web-config.json');
                const config = await response.json();

                // Initialize Firebase
                firebase.initializeApp(config.config);
                messaging = firebase.messaging();

                // Request permission
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    console.log('Notification permission granted');

                    // Get token
                    const token = await messaging.getToken({ vapidKey: config.vapidKey });
                    console.log('FCM Token:', token);

                    // Store the current browser token
                    currentBrowserToken = token;

                    // Update status
                    statusEl.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Firebase Ready - Listening for notifications</span><br><small class="text-muted mt-1">Your token: ' + token.substring(0, 50) + '...</small>';

                    // Handle foreground messages
                    messaging.onMessage((payload) => {
                        console.log('Message received in foreground:', payload);

                        // Show browser notification
                        const title = payload.notification?.title || payload.data?.title_en || 'New Notification';
                        const body = payload.notification?.body || payload.data?.message_en || 'You have a new message';

                        new Notification(title, {
                            body: body,
                            icon: '/storage/tasawk-logo-light.svg',
                            badge: '/storage/tasawk-logo-light.svg',
                            tag: 'tasawk-notification',
                            requireInteraction: true,
                            data: payload.data
                        });

                        // Show notification indicator
                        showNotificationIndicator(`📬 ${title}`);

                        // Also show in result box
                        showResult(`📬 Notification Received!<br><strong>${title}</strong><br>${body}`, 'success');
                    });
                } else {
                    console.warn('Notification permission denied');
                    statusEl.innerHTML = '<span class="badge bg-warning"><i class="bi bi-exclamation-triangle me-1"></i>Notification permission denied</span>';
                    showResult('⚠️ Please enable notification permissions to receive notifications', 'error');
                }
            } catch (error) {
                console.error('Firebase initialization error:', error);
                statusEl.innerHTML = '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Firebase initialization failed</span>';
            }
        }

        // Initialize Firebase when page loads
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then((registration) => {
                    console.log('Service Worker registered:', registration);
                    initializeFirebase();
                })
                .catch((error) => {
                    console.error('Service Worker registration failed:', error);
                });
        } else {
            initializeFirebase();
        }

        // Send notification function
        async function sendNotification(token, userName = 'User') {
            const titleEn = document.getElementById('titleEn').value;
            const titleAr = document.getElementById('titleAr').value;
            const messageEn = document.getElementById('messageEn').value;
            const messageAr = document.getElementById('messageAr').value;

            showResult('Sending notification...', 'info');

            try {
                const response = await fetch('/test-notification/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        token: token,
                        title_en: titleEn,
                        title_ar: titleAr,
                        message_en: messageEn,
                        message_ar: messageAr
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showResult(`✅ Success! Notification sent to ${userName}<br><small>Message ID: ${data.message_id || 'N/A'}</small>`, 'success');
                } else {
                    showResult(`❌ Error: ${data.message || 'Failed to send notification'}`, 'error');
                }
            } catch (error) {
                showResult(`❌ Network Error: ${error.message}`, 'error');
            }
        }

        function showResult(message, type) {
            const resultBox = document.getElementById('resultBox');
            const resultContent = document.getElementById('resultContent');

            resultBox.className = 'result-box ' + type;
            resultContent.innerHTML = message;
            resultBox.style.display = 'block';
        }

        function showNotificationIndicator(message) {
            const indicator = document.getElementById('notificationIndicator');
            const text = document.getElementById('notificationText');

            text.textContent = message;
            indicator.style.display = 'block';

            setTimeout(() => {
                indicator.style.display = 'none';
            }, 5000);
        }

        // Event listeners
        document.querySelectorAll('.send-notification').forEach(button => {
            button.addEventListener('click', function() {
                const token = this.dataset.token;
                const userName = this.dataset.user;
                sendNotification(token, userName);
            });
        });

        document.getElementById('sendCustom').addEventListener('click', function() {
            const token = document.getElementById('customToken').value.trim();
            if (!token) {
                showResult('❌ Please enter a device token', 'error');
                return;
            }
            sendNotification(token, 'Custom Token');
        });

        document.getElementById('useMyToken').addEventListener('click', function() {
            if (currentBrowserToken) {
                document.getElementById('customToken').value = currentBrowserToken;
                showResult('✅ Your browser token has been filled in! Click "Send to Custom Token" to test.', 'success');
            } else {
                showResult('⚠️ Browser token not ready yet. Please wait for Firebase to initialize.', 'error');
            }
        });

        document.getElementById('saveMyToken').addEventListener('click', async function() {
            if (!currentBrowserToken) {
                showResult('⚠️ Browser token not ready yet. Please wait for Firebase to initialize.', 'error');
                return;
            }

            showResult('Saving token to database...', 'info');

            try {
                const response = await fetch('/api/device-tokens', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        token: currentBrowserToken,
                        platform: 'web'
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    showResult('✅ Token saved successfully! Refresh the page to see it in the list.', 'success');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showResult(`❌ Failed to save token: ${data.message || 'Unknown error'}`, 'error');
                }
            } catch (error) {
                showResult(`❌ Network Error: ${error.message}`, 'error');
            }
        });
    </script>
</body>
</html>

