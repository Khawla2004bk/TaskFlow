<?php
include('controllers/showUsers.php') 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: linear-gradient(135deg, #1e3a8a, #4f46e5);
      font-family: 'Inter', sans-serif;
    }
    .table-header {
      background: linear-gradient(135deg, #4f46e5, #9333ea);
      color: #fff;
    }
    .table-row:hover {
      background-color: rgba(79, 70, 229, 0.1);
    }
    .btn:hover {
      transform: scale(1.05);
      transition: all 0.2s ease-in-out;
    }
  </style>
</head>
<body class="min-h-screen flex">
  <!-- Sidebar -->
  <div class="w-64 bg-white shadow-lg min-h-screen p-4">
    <h2 class="text-2xl font-bold text-blue-600 mb-6 text-center">Dashboard</h2>
    <ul class="space-y-4">
      <li class="flex items-center space-x-3">
        <span class="text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </span>
        <a href="#" class="text-gray-700 hover:text-blue-600">Overview</a>
      </li>
      <li class="flex items-center space-x-3">
        <span class="text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </span>
        <a href="#" class="text-gray-700 hover:text-blue-600">Users</a>
      </li>
      <li class="flex items-center space-x-3">
        <span class="text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
        </span>
        <a href="index.php/?page=chart" class="text-gray-700 hover:text-blue-600">Analytics</a>
      </li>
      <li class="flex items-center space-x-3">
        <span class="text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </span>
        <a href="#" class="text-gray-700 hover:text-blue-600">Profile</a>
      </li>
      <li class="flex items-center space-x-3">
        <span class="text-blue-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </span>
        <a href="index.php/?page=home" class="text-gray-700 hover:text-blue-600">Deconnexion</a>
      </li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="flex-1 container mx-auto p-6">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
      <header class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center">
        <h1 class="text-3xl font-bold">Admin Dashboard</h1>
        <p class="mt-1 text-sm">Manage your application users effortlessly</p>
      </header>

      <div class="p-6">
        <table class="min-w-full table-auto border-collapse border border-gray-200 shadow-md">
          <thead class="table-header">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Password</th>
              <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Created At</th>
              <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Updated At</th>
              <th class="px-6 py-3 text-left text-sm font-semibold uppercase tracking-wider">Options</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach($users as $user){  ?>
            <!-- Example Rows -->
            <tr class="table-row">
              <td class="px-6 py-4 text-sm font-medium text-gray-700">1</td>
              <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($user['name']) ?></td>
              <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($user['email']) ?></td>
              <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($user['password']) ?></td>
              <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($user['created_at']) ?></td>
              <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($user['updated_at']) ?></td>
              <td class="px-6 py-4 text-sm text-gray-700 flex space-x-4">
                <button class="btn px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600">Edit</button>
                <button class="btn px-4 py-2 bg-red-500 text-white rounded-lg shadow-md hover:bg-red-600">Delete</button>
              </td>
            </tr>
           
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
