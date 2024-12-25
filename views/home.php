<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexTask - Advanced Todo Application</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .neon-border {
            box-shadow: 0 0 5px #4fd1c5, 0 0 10px #4fd1c5, 0 0 15px #4fd1c5, 0 0 20px #4fd1c5;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 via-purple-900 to-teal-800 min-h-screen font-['Roboto']">
    <div id="particles-js" class="absolute inset-0"></div>
    <header class="relative z-10 py-6 px-4 sm:px-6 lg:px-8">
        <nav class="flex justify-between items-center">
            <div class="text-4xl font-['Orbitron'] font-bold text-white">
                Task<span class="text-teal-400">Flow</span>
            </div>
            <div class="space-x-4">
                <a href="#" class="text-white hover:text-teal-400 transition-colors duration-300">Home</a>
                <a href="#" class="text-white hover:text-teal-400 transition-colors duration-300">Features</a>
                <a href="#" class="text-white hover:text-teal-400 transition-colors duration-300">Pricing</a>
                <a href="?page=login_signup" class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-2 px-4 rounded-full transition-colors duration-300" id="signup">Sign Up</a>
            </div>
        </nav>
    </header>
    <main class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <h1 class="text-6xl sm:text-7xl md:text-8xl font-['Orbitron'] font-bold text-white mb-8 leading-tight">
            Revolutionize Your <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-blue-500">Productivity</span>
        </h1>
        <p class="text-xl sm:text-2xl text-gray-300 mb-12 max-w-3xl mx-auto">
            Experience the future of task management with NexTask. Seamlessly organize, prioritize, and conquer your goals like never before.
        </p>
        <div class="flex justify-center space-x-6">
            <a href="#" class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-3 px-8 rounded-full text-lg transition-colors duration-300 transform hover:scale-105">
                Get Started
            </a>
            <a href="#" class="bg-transparent border-2 border-white text-white font-bold py-3 px-8 rounded-full text-lg hover:bg-white hover:text-purple-900 transition-colors duration-300 transform hover:scale-105">
                Learn More
            </a>
        </div>
    </main>
    <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="glassmorphism p-8 text-center">
                <div class="text-5xl mb-4 text-teal-400 floating">🚀</div>
                <h3 class="text-2xl font-bold text-white mb-4">Boost Productivity</h3>
                <p class="text-gray-300">Streamline your workflow and accomplish more in less time.</p>
            </div>
            <div class="glassmorphism p-8 text-center">
                <div class="text-5xl mb-4 text-teal-400 floating">🔗</div>
                <h3 class="text-2xl font-bold text-white mb-4">Seamless Integration</h3>
                <p class="text-gray-300">Connect with your favorite tools for a unified experience.</p>
            </div>
            <div class="glassmorphism p-8 text-center">
                <div class="text-5xl mb-4 text-teal-400 floating">📊</div>
                <h3 class="text-2xl font-bold text-white mb-4">Insightful Analytics</h3>
                <p class="text-gray-300">Gain valuable insights into your productivity patterns.</p>
            </div>
        </div>
    </div>
    <footer class="relative z-10 text-center py-8 text-gray-400">
        <p>&copy; 2023 NexTask. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script src="assets/script.js"></script>
</body>
</html>
