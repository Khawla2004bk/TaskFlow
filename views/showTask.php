<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskMaster Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="assets/style.css">
    
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="#" class="logo">TaskMaster Pro</a>
            <button id="addTaskBtn" class="add-task-btn">Add New Task</button>
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
                    <label class="form-label" for="status">Status</label>
                    <select id="status" class="form-select">
                        <option value="todo">Todo</option>
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
    <script src="../assets/script.js" ></script>

    <!-- <script>
        const addTaskBtn = document.getElementById('addTaskBtn');
        const taskModal = document.getElementById('taskModal');
        const taskForm = document.getElementById('taskForm');
        const cancelBtn = document.getElementById('cancelBtn');
        const columns = {
            todo: document.querySelector('#todo-column .tasks'),
            inprogress: document.querySelector('#inprogress-column .tasks'),
            done: document.querySelector('#done-column .tasks')
        };

        let tasks = [];

        function showModal() {
            taskModal.classList.add('active');
        }

        function hideModal() {
            taskModal.classList.remove('active');
            taskForm.reset();
        }

        function createTaskElement(task) {
            const taskEl = document.createElement('div');
            taskEl.className = 'task-card';
            taskEl.draggable = true;
            taskEl.dataset.id = task.id;
            
            taskEl.innerHTML = `
                <h3 class="task-title">${task.title}</h3>
                <p class="task-description">${task.description}</p>
                <div class="task-meta">
                    <span class="priority priority-${task.priority}">${task.priority}</span>
                    <span>${task.dueDate}</span>
                </div>
            `;

            return taskEl;
        }

        function addTask(task) {
            tasks.push(task);
            const taskEl = createTaskElement(task);
            columns.todo.appendChild(taskEl);
            setupDragAndDrop(taskEl);
        }

        function setupDragAndDrop(element) {
            element.addEventListener('dragstart', () => {
                element.classList.add('dragging');
            });

            element.addEventListener('dragend', () => {
                element.classList.remove('dragging');
            });
        }

        Object.values(columns).forEach(column => {
            column.addEventListener('dragover', e => {
                e.preventDefault();
                const dragging = document.querySelector('.dragging');
                const afterElement = getDragAfterElement(column, e.clientY);
                
                if (afterElement) {
                    column.insertBefore(dragging, afterElement);
                } else {
                    column.appendChild(dragging);
                }
            });
        });

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.task-card:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        addTaskBtn.addEventListener('click', showModal);
        cancelBtn.addEventListener('click', hideModal);
        taskModal.addEventListener('click', e => {
            if (e.target === taskModal) hideModal();
        });

        taskForm.addEventListener('submit', e => {
            e.preventDefault();
            
            const task = {
                id: Date.now(),
                title: taskForm.title.value,
                description: taskForm.description.value,
                priority: taskForm.priority.value,
                type: taskForm.type.value,
                status: taskForm.status.value,
                dueDate: taskForm.dueDate.value,
            };

            addTask(task);
            hideModal();
        });

        // Add some sample tasks
        [
            {
                id: 1,
                title: 'Implement new feature',
                description: 'Add user authentication to the app',
                priority: 'high',
                dueDate: '2023-06-30',
                status: 'todo'
            },
            {
                id: 2,
                title: 'Fix critical bug',
                description: 'Address performance issue in production',
                priority: 'high',
                dueDate: '2023-06-25',
                status: 'inprogress'
            },
            {
                id: 3,
                title: 'Design review',
                description: 'Complete UI/UX review for new features',
                priority: 'low',
                dueDate: '2023-06-20',
                status: 'done'
            }
        ].forEach(task => addTask(task));
    </script> -->
</body>
</html>