<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskMaster Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <style>
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
       

    </style> -->
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="#" class="logo">TaskMaster Pro</a>
            <button id="addTaskBtn" class="add-task-btn">Add New Task</button>
            <button id="inviter" class="inviter">Inviter</button>
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
            <form id="taskForm">
                <div class="form-group">
                    <label class="form-label" for="title">Title</label>
                    <input type="text" id="title" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" class="form-textarea"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="priority">Priority</label>
                    <select id="priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="type">Type</label>
                    <select id="type" class="form-select">
                        <option value="basic">Basic</option>
                        <option value="bug">Bug</option>
                        <option value="feature">Feature</option>
                    </select>
                </div>

                
                <div class="form-group">
                    <label class="form-label" for="dueDate">Due Date</label>
                    <input type="date" id="dueDate" class="form-input">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Task</button>
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

    <script src="assets/showtask.js" ></script>

   
</body>
</html>