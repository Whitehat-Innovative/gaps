<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <div class="text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Email Verified!</h1>

            @if(isset($invalid) && $invalid)
                <p class="text-red-600 mb-6">
                    The verification link is invalid. Please request a new verification email.
                </p>
            @elseif(isset($verified) && $verified)
                <p class="text-green-600 mb-6">
                    Your email address has already been verified. You can now log in to your account from the app.
                </p>
            @elseif(isset($verify) && $verify)
                <p class="text-gray-600 mb-6">
                    Your email address has been successfully verified. You can now log in to your account from the app.
                </p>
            @endif

            <!-- Additional Info -->
            <p class="text-xs text-gray-500 mt-6">
                If you did not verify this email, please <a href="mailto:support@example.com" class="text-indigo-600 hover:text-indigo-700 underline">contact support</a>.
            </p>
        </div>
    </div>

<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-analytics.js";
  import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-messaging.js";

  const firebaseConfig = {
    apiKey: "AIzaSyBanbPA685TE3KDjCC9I09lMPKV7fh2mZ4",
    authDomain: "fitbase-3a4eb.firebaseapp.com",
    projectId: "fitbase-3a4eb",
    storageBucket: "fitbase-3a4eb.firebasestorage.app",
    messagingSenderId: "343450984538",
    appId: "1:343450984538:web:d11f4f4e1a75198d7b7863",
    measurementId: "G-4M67Z1XQ45"
  };

  const app = initializeApp(firebaseConfig);
  const messaging = getMessaging(app);

  // Request browser permission
  Notification.requestPermission().then(async (permission) => {
    if (permission === "granted") {

      const token = await getToken(messaging, {
        vapidKey:
          "BEIxKW_xviKq8WShT2OvlBQ0--MvHQ-0IBLmcblRQWB9-Kg2IgOI8omopEh2OLAtwU8M1dci2v7L8DXhJRvS6qY",
        serviceWorkerRegistration: await navigator.serviceWorker.register(
          "/firebase-messaging-sw.js"
        ),
      });

      console.log("FCM Token:", token);
      alert("FCM Token:\n\n" + token);
    } else {
      alert("Permission denied.");
    }
  });
</script>





</body>



</html>
