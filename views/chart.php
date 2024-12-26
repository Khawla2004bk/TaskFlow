<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f9ff;
        }
        .dashboard-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-2px);
        }
        .sidebar-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .sidebar-icon:hover {
            background: rgba(0, 217, 214, 0.1);
            color: #00d9d6;
        }
        .progress-ring {
            transform: rotate(-90deg);
        }
        .calendar-day {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .calendar-day:hover:not(.inactive) {
            background: rgba(0, 217, 214, 0.1);
            color: #00d9d6;
        }
        .calendar-day.active {
            background: #00d9d6;
            color: white;
        }
        .calendar-day.inactive {
            color: #9ca3af;
        }
        .timeline-node {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #00d9d6;
            position: relative;
        }
        .timeline-node::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            background: #00d9d6;
            top: 50%;
            left: 100%;
            transform: translateY(-50%);
        }
        .timeline-node:last-child::after {
            display: none;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-16 bg-white h-screen fixed left-0 top-0 flex flex-col items-center py-6 gap-6">
            <div class="sidebar-icon text-cyan-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <div class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />
                </svg>
            </div>
            <div class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
            <div class="sidebar-icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>

        <!-- Main Content -->
        <div class="ml-16 p-8 w-full">
            <!-- Search Bar -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
                <div class="relative">
                    <input type="text" placeholder="Search..." class="pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Main Chart -->
                <div class="dashboard-card p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">Performance Overview</h3>
                    <canvas id="mainChart" height="200"></canvas>
                </div>

                <!-- Stats Cards -->
                <div class="dashboard-card p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Statistics</h3>
                        <select class="text-sm text-gray-500 border-none focus:ring-0">
                            <option>This Week</option>
                            <option>This Month</option>
                            <option>This Year</option>
                        </select>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Users</span>
                            <span class="text-2xl font-semibold">1,205</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Active Sessions</span>
                            <span class="text-2xl font-semibold">840</span>
                        </div>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="dashboard-card p-6">
                    <h3 class="text-lg font-semibold mb-4">January</h3>
                    <div class="grid grid-cols-7 gap-2">
                        <div class="calendar-day font-medium text-sm text-gray-400">Su</div>
                        <div class="calendar-day font-medium text-sm text-gray-400">Mo</div>
                        <div class="calendar-day font-medium text-sm text-gray-400">Tu</div>
                        <div class="calendar-day font-medium text-sm text-gray-400">We</div>
                        <div class="calendar-day font-medium text-sm text-gray-400">Th</div>
                        <div class="calendar-day font-medium text-sm text-gray-400">Fr</div>
                        <div class="calendar-day font-medium text-sm text-gray-400">Sa</div>
                    </div>
                    <div id="calendar-days" class="grid grid-cols-7 gap-2 mt-2"></div>
                </div>

                <!-- Progress Chart -->
                <div class="dashboard-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Project Progress</h3>
                    <div class="flex justify-center">
                        <div class="relative">
                            <svg class="progress-ring" width="120" height="120">
                                <circle class="progress-ring-circle" stroke="#e2e8f0" stroke-width="8" fill="transparent" r="52" cx="60" cy="60"/>
                                <circle class="progress-ring-circle" stroke="#00d9d6" stroke-width="8" fill="transparent" r="52" cx="60" cy="60"/>
                            </svg>
                            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-2xl font-semibold">75%</div>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="dashboard-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Monthly Revenue</h3>
                    <canvas id="barChart" height="200"></canvas>
                </div>

                <!-- Timeline -->
                <div class="dashboard-card p-6">
                    <h3 class="text-lg font-semibold mb-4">Project Timeline</h3>
                    <div class="flex justify-between items-center mt-8">
                        <div class="timeline-node"></div>
                        <div class="timeline-node"></div>
                        <div class="timeline-node"></div>
                        <div class="timeline-node"></div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm text-gray-600">
                        <span>Planning</span>
                        <span>Design</span>
                        <span>Development</span>
                        <span>Launch</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Main Chart
            const mainCtx = document.getElementById('mainChart').getContext('2d');
            new Chart(mainCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Performance',
                        data: [65, 59, 80, 81, 56, 85],
                        borderColor: '#00d9d6',
                        tension: 0.4,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

           
            const barCtx = document.getElementById('barChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue',
                        data: [12, -19, 3, 5, -2, 3],
                        backgroundColor: function(context) {
                            return context.raw >= 0 ? '#00d9d6' : '#ff6b6b';
                        }
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

           
            const calendarDays = document.getElementById('calendar-days');
            const daysInMonth = 31;
            const firstDay = 1; 

            
            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day inactive';
                calendarDays.appendChild(emptyDay);
            }

   
            for (let i = 1; i <= daysInMonth; i++) {
                const day = document.createElement('div');
                day.className = 'calendar-day';
                day.textContent = i;
                if (i === 14) day.classList.add('active');
                calendarDays.appendChild(day);
            }

            const circle = document.querySelector('.progress-ring-circle:last-child');
            const radius = circle.r.baseVal.value;
            const circumference = radius * 2 * Math.PI;

            circle.style.strokeDasharray = `${circumference} ${circumference}`;
            circle.style.strokeDashoffset = circumference;

            function setProgress(percent) {
                const offset = circumference - (percent / 100 * circumference);
                circle.style.strokeDashoffset = offset;
            }

            setProgress(75);
        });
    </script>
</body>
</html>