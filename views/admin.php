<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskMaster Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
  <style>
        .form-container {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            z-index: 1000;
            
    
        }

        .form-container.active {
            display: block;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-container .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-container label {
            font-weight: 500;
            color: #333;
            font-size: 0.9rem;
        }

        .form-container input,
        .form-container select {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-container input:focus,
        .form-container select:focus {
            border-color: #4a90e2;
            outline: none;
        }

        .form-container .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .form-container button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-container .cancel {
            background-color: #f1f1f1;
            color: #333;
        }

        .form-container .add {
            background-color: #4a90e2;
            color: white;
        }

        .form-container .cancel:hover {
            background-color: #e1e1e1;
        }

        .form-container .add:hover {
            background-color: #357abd;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(244, 4, 4, 0.5);
            z-index: 999;
           

        }

        .overlay.active {
            display: block;
        }

        /* Board and Column Styles */
        .board {
            display: flex;
            gap: 2rem;
            padding: 2rem;
            overflow-x: auto;
        }

        .column {
            flex: 1;
            min-width: 300px;
            background: #f5f5f5;
            border-radius: 8px;
            padding: 1rem;
        }

        .column-header {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .tasks {
            min-height: 200px;
            transition: background-color 0.3s;
        }

        .tasks.drag-over {
            background-color: #e8f0fe;
            border-radius: 4px;
        }

        /* Task Card Styles */
        .task {
            background: white;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            cursor: grab;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .task:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .task:active {
            cursor: grabbing;
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 0.5rem;
        }

        .task-header h3 {
            margin: 0;
            font-size: 1rem;
            color: #333;
        }

        .task-description {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
        }

        .task-footer {
            display: flex;
            gap: 0.5rem;
            font-size: 0.8rem;
        }

        .task-status,
        .task-priority,
        .task-type,
        .task-due-date {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
        }

        .task-status {
            background: #e0e0e0;
        }

        .task-status.todo {
            background: #fff3cd;
            color: #856404;
        }

        .task-status.inprogress {
            background: #cce5ff;
            color: #004085;
        }

        .task-status.done {
            background: #d4edda;
            color: #155724;
        }

        .task-priority.high {
            background: #f8d7da;
            color: #721c24;
        }

        .task-priority.medium {
            background: #fff3cd;
            color: #856404;
        }

        .task-priority.low {
            background: #d4edda;
            color: #155724;
        }

        .task-type {
            background: #e2e3e5;
            color: #383d41;
        }

        .task-due-date {
            color: #6c757d;
        }
        #plusicon{
            background-color: royalblue ;
            border-radius: 100%;
            padding: 2px;
            width: 30px;
            color: white;
            text-align: center;
            font-size: 19px;  
            width: 36px;
            height: 38px; 
        }
        #email-plus{
            display: flex;
            flex-direction: row;
            gap: 2px;
            
        }
                
        #icon{
            font-size: 20%;
            width: 15px ;
            height: 15px;
            cursor:pointer  
                
        }

        #header{
            display: flex;
            flex-direction: row;
            gap: 60px;
            
        } 
       


    </style> 
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="#" class="logo">TaskFlow</a>
            <button id="addTaskBtn" class="add-task-btn">Add New Task</button>
            <button id="inviter" class="inviter">Inviter</button>
            <button id="logoutBtn" class="btn btn-danger">Logout</button>
        </div>
    </header>

    <div class="container">
        <h1 class="title">Your Tasks</h1>
        
        <div class="board">
            <div class="column" id="todo-column">
                <h2 class="column-header">To Do</h2>
                <div class="tasks"></div>
            </div>
            
            <div class="column" id="inprogress-column">
                <h2 class="column-header">In Progress</h2>
                <div class="tasks"></div>
            </div>
            
            <div class="column" id="done-column">
                <h2 class="column-header">Done</h2>
                <div class="tasks"></div>
            </div>
        </div>
    </div>

    <div id="taskModal" class="modal">
        <div class="modal-content">
            <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Add New Task</h2>
            <form id="taskForm" action="index.php?action=create_task" method="POST">
                <div class="form-group">
                    <label for="title" class="form-label">Titre</label>
                    <input type="text" id="title" name="title" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-textarea"></textarea>
                </div>
                <div class="form-group">
                    <label for="priority" class="form-label">Priority</label>
                    <select id="priority" name="priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="taskStatus">Status</label>
                    <select id="taskStatus" name="status">
                        <option value="1">To do</option>
                        <option value="2">In progress</option>
                        <option value="3">Done</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type" class="form-label">Type</label>
                    <select id="type" name="type" class="form-select">
                        <option value="1">Basic</option>
                        <option value="2">Bug</option>
                        <option value="3">Feature</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="dueDate" class="form-label">Due Date</label>
                    <input type="date" id="dueDate" name="dueDate" class="form-input">
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel btn btn-secondary" id="cancelBtn" onclick="closeTaskForm()">Cancel</button>
                    <button type="submit" class="add btn btn-primary">Add Task</button>
                </div>
             </form>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>
    <div class="form-container" id="form-container">
        <h2 style="margin-bottom: 1.5rem; color: #333;">Invite Team Member</h2>
        <form action="#" method="post">
            <div class="form-group">
                <label for="email">Email Address</label>
                 <div id="email-plus">
                <input type="email" id="email" name="email" placeholder="Enter team member's email" required>
                <div id="plusicon"><b>+</b></div>
                </div>
            </div>
            <div class="form-group">
                <label for="role">Permissions</label>
                <select id="role" name="role" required>
                    <option value="">Select permission level</option>
                    <option value="modify">Modify</option>
                    <option value="read">Read Only</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="button" class="cancel" id="cancel">Cancel</button>
                <button type="submit" class="add">Send Invite</button>
            </div>
        </form>
    </div>


    <!-- *************************details ***************** -->
    <div id="taskModal" class="modal fixed inset-0 z-50 hidden">
        <div class="modal-backdrop absolute inset-0 flex items-center justify-center">
            <div class="modal-content bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4">
                <!-- Header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Implement new feature</h2>
                            <p class="text-gray-500 mt-1">Add user authentication to the app</p>
                        </div>
                        <button 
                            onclick="closeModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-4 mt-4">
                        <span class="priority-high text-white text-sm px-3 py-1 rounded-full">high</span>
                        <span class="text-gray-400 text-sm">Due: 2023-06-30</span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-6">
                    <!-- Assignment Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Assign to
                        </label>
                        <div class="relative">
                            <input 
                                type="email" 
                                id="assignEmail"
                                placeholder="Enter email address"
                                class="custom-input w-full px-4 py-2 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none"
                            >
                            <button 
                                onclick="assignTask()"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors duration-200"
                            >
                                Assign
                            </button>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Description</h3>
                            <p class="text-gray-600">
                                Implement user authentication system including login, registration, and password recovery features. 
                                Ensure secure password handling and JWT token implementation.
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 mb-2">Subtasks</h3>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-gray-600">
                                    <input type="checkbox" class="rounded text-indigo-600 focus:ring-indigo-500">
                                    Set up authentication routes
                                </label>
                                <label class="flex items-center gap-2 text-gray-600">
                                    <input type="checkbox" class="rounded text-indigo-600 focus:ring-indigo-500">
                                    Implement password hashing
                                </label>
                                <label class="flex items-center gap-2 text-gray-600">
                                    <input type="checkbox" class="rounded text-indigo-600 focus:ring-indigo-500">
                                    Create JWT middleware
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                    <button 
                        onclick="closeModal()"
                        class="px-4 py-2 border border-gray-200 text-gray-600 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                    >
                        Cancel
                    </button>
                    <button 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200"
                    >
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script src="assets/showtask.js" ></script>
    <script src="assets/details.js" ></script>

   
</body>
</html>