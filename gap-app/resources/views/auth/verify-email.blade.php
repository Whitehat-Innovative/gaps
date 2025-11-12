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
</body>
</html>
