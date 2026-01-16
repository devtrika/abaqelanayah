<!-- Firebase Device Registration Component -->
<script>
    // Function to extract and register device token
    function registerFirebaseDevice() {
        // Check if Firebase messaging is available
        if (typeof firebase === 'undefined' || !firebase.messaging) {
            console.error('Firebase messaging is not available');
            return;
        }

        const messaging = firebase.messaging();

        // Request permission for notifications
        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                console.log('Notification permission granted.');
                
                // Get the device token
                messaging.getToken().then((currentToken) => {
                    if (currentToken) {
                        console.log('Device Token:', currentToken);
                        
                        // Send token to server for registration
                        registerDeviceToken(currentToken);
                        
                        // Store token locally for future use
                        localStorage.setItem('fcm_device_token', currentToken);
                    } else {
                        console.log('No registration token available.');
                    }
                }).catch((err) => {
                    console.log('An error occurred while retrieving token. ', err);
                });
                
                // Listen for token refresh
                messaging.onTokenRefresh(() => {
                    messaging.getToken().then((refreshedToken) => {
                        console.log('Token refreshed:', refreshedToken);
                        registerDeviceToken(refreshedToken);
                        localStorage.setItem('fcm_device_token', refreshedToken);
                    }).catch((err) => {
                        console.log('Unable to retrieve refreshed token ', err);
                    });
                });
                
            } else {
                console.log('Unable to get permission to notify.');
            }
        }).catch((err) => {
            console.log('Error getting notification permission:', err);
        });

        // Handle foreground messages
        messaging.onMessage((payload) => {
            console.log('Message received in foreground: ', payload);
            
            // Display notification manually for foreground messages
            if (payload.notification) {
                showNotification(payload.notification.title, payload.notification.body);
            }
        });
    }

    // Function to register device token with the server
    function registerDeviceToken(token) {
        // Get device type
        const deviceType = getDeviceType();
        
        // Prepare data
        const data = {
            device_id: token,
            device_type: deviceType,
            _token: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        };

        // Send to server (adjust URL as needed)
        fetch('/api/register-device', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': data._token
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Device registered successfully:', data);
        })
        .catch(error => {
            console.error('Error registering device:', error);
        });
    }

    // Function to detect device type
    function getDeviceType() {
        const userAgent = navigator.userAgent.toLowerCase();
        
        if (/iphone|ipad|ipod/.test(userAgent)) {
            return 'ios';
        } else if (/android/.test(userAgent)) {
            return 'android';
        } else {
            return 'web';
        }
    }

    // Function to show notification manually
    function showNotification(title, body) {
        if (Notification.permission === 'granted') {
            new Notification(title, {
                body: body,
                icon: '/favicon.ico', // Adjust icon path as needed
                badge: '/favicon.ico'
            });
        }
    }

    // Function to get stored device token
    function getStoredDeviceToken() {
        return localStorage.getItem('fcm_device_token');
    }

    // Function to clear device token (for logout)
    function clearDeviceToken() {
        localStorage.removeItem('fcm_device_token');
        
        // Optionally, unregister from server
        const token = getStoredDeviceToken();
        if (token) {
            fetch('/api/unregister-device', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ device_id: token })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Device unregistered successfully:', data);
            })
            .catch(error => {
                console.error('Error unregistering device:', error);
            });
        }
    }

    // Auto-initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Wait a bit for Firebase to initialize
        setTimeout(() => {
            registerFirebaseDevice();
        }, 1000);
    });

    // Expose functions globally for manual use
    window.firebaseDeviceRegistration = {
        register: registerFirebaseDevice,
        getToken: getStoredDeviceToken,
        clearToken: clearDeviceToken,
        getDeviceType: getDeviceType
    };
</script>

<!-- Usage Instructions -->
<!--
To use this component:

1. Include this component in your Blade template:
   @include('components.firebase-device-registration')

2. Make sure Firebase is initialized before this script runs

3. Create API endpoints for device registration:
   - POST /api/register-device
   - POST /api/unregister-device

4. The script will automatically:
   - Request notification permission
   - Extract device token
   - Register with your server
   - Handle token refresh
   - Store token locally

5. Manual usage:
   - firebaseDeviceRegistration.register() - Force registration
   - firebaseDeviceRegistration.getToken() - Get stored token
   - firebaseDeviceRegistration.clearToken() - Clear token (logout)
   - firebaseDeviceRegistration.getDeviceType() - Get device type
-->