<?php
include_once __DIR__ . '/../config/session.php';
include_once __DIR__ . '/../config/connexion.php';
include_once __DIR__ . '/../config/helper.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskMaster Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/showtask.css">

  
</head>
    <header class="header">
        <div class="header-content">
            <a href="#" class="logo">TaskFlow</a>
            <!-- <button id="addTaskBtn" class="add-task-btn">Add New Task</button> -->
            <button id="logout" class="logout">log out</button>
        </div>
    </header>

    <div class="container">
        <h1 class="title">Your assigned Tasks</h1>
        
        <div class="board">
            <div class="column" id="todo-column">
                <h2 class="column-header">To Do</h2>
                <div class="tasks">
                
                </div>
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

    <!-- <div id="taskModal" class="modal">
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
                    <label class="form-label" for="status">Status</label>
                    <select id="status" class="form-select">
                        <option value="todo">To Do</option>
                        <option value="doing">Doing</option>
                        <option value="done">Done</option>
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
     -->

   
    
    <script src="assets/showtask.js" ></script>
    <script src="assets/logout.js" ></script>

   
</body>
</html>