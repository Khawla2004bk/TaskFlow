<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskMaster Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/showtask.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="#" class="logo">TaskFlow</a>
            <button id="addTaskBtn" class="add-task-btn">Add New Task</button>
            <button id="inviter" class="inviter">Inviter</button>
            <button id="logout" class="logout btn btn-danger">Logout</button>
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
    <div id="detailsModal" class="modal fixed inset-0 z-50"></div>


    <script src="assets/showtask.js" ></script>
    <script src="assets/logout.js" ></script>

   
</body>
</html>