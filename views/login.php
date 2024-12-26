<?php
include_once __DIR__ . "/../controllers/logincontr.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskMaster Pro</title>
    <link rel="stylesheet" href="assets/loginstyle.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- <style>
      
    </style> -->
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div id="particles-js" class="absolute inset-0"></div>
    <div class="glass w-full max-w-4xl flex rounded-xl overflow-hidden">
        <div class="w-1/2 bg-white p-12 flex flex-col justify-center">
            <h2 class="text-4xl font-bold mb-6 text-gray-800">Welcome to TaskMaster Pro</h2>
            <p class="text-gray-600 mb-8">Elevate your productivity to new heights.</p>
            <?php if ($error): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form id="loginForm" class="space-y-6" action="index.php?action=logincontr" method="post">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign In
                    </button>
                </div>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Don't have an account?
                <a href="index.php?page=signup" id="switchToSignup" class="font-medium text-indigo-600 hover:text-indigo-500">Sign up</a>
            </p>
        </div>
        <div class="w-1/2 p-12 flex flex-col justify-center items-center text-white">
            <div class="animate-float">
                <svg class="w-48 h-48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.5 5.5L5 7L7.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M3.5 11.5L5 13L7.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M3.5 17.5L5 19L7.5 16.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M11 6H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M11 12H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M11 18H20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            testing
            <h3 class="text-2xl font-bold mt-8 mb-4">Organize. Prioritize. Succeed.</h3>
            <p class="text-center">Experience task management like never before with our cutting-edge platform.</p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="assets/script.js"></script>
</body>

</html>