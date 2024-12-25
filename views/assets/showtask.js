document.addEventListener('DOMContentLoaded', function() {
    // Inviter form functionality
    const inviterBtn = document.getElementById('inviter');
    const formContainer = document.getElementById('form-container');
    const overlay = document.getElementById('overlay');
    const cancelBtn = document.getElementById('cancel');

    // Show form when inviter button is clicked
    inviterBtn.addEventListener('click', function() {
        formContainer.style.display = 'block';
        overlay.classList.add('active');
    });

    // Hide form when cancel button is clicked
    cancelBtn.addEventListener('click', function() {
        formContainer.style.display = 'none';
        overlay.classList.remove('active');
    });

    // Hide form when clicking outside
    overlay.addEventListener('click', function() {
        formContainer.style.display = 'none';
        overlay.classList.remove('active');
    });

    // Handle form submission
    formContainer.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const role = document.getElementById('role').value;

        // Here you can add your API call to handle the invitation
        console.log('Inviting user:', { email, role });

        // Reset form and close modal
        this.reset();
        formContainer.style.display = 'none';
        overlay.classList.remove('active');
    });

    // Drag and Drop functionality
    const columns = document.querySelectorAll('.column');
    let draggedItem = null;
    let draggedItemColumn = null;

    // Add event listeners to all task items
    function addDragListeners(taskElement) {
        taskElement.setAttribute('draggable', true);
        
        taskElement.addEventListener('dragstart', function(e) {
            draggedItem = taskElement;
            draggedItemColumn = taskElement.closest('.column');
            setTimeout(() => {
                taskElement.style.opacity = '0.5';
            }, 0);
        });

        taskElement.addEventListener('dragend', function(e) {
            draggedItem = null;
            draggedItemColumn = null;
            taskElement.style.opacity = '1';
            
            // Remove all drag-over styling
            columns.forEach(column => {
                column.querySelector('.tasks').classList.remove('drag-over');
            });
        });
    }

    // Add event listeners to columns
    columns.forEach(column => {
        const tasksContainer = column.querySelector('.tasks');
        
        tasksContainer.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });

        tasksContainer.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });

        tasksContainer.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (draggedItem) {
                // Get the new status based on the column
                const newStatus = column.id.replace('-column', '');
                
                // Update the task's status visually
                const statusBadge = draggedItem.querySelector('.task-status');
                if (statusBadge) {
                    statusBadge.textContent = newStatus;
                    statusBadge.className = 'task-status ' + newStatus;
                }

                // Move the task to the new column
                this.appendChild(draggedItem);

                // Here you would typically make an API call to update the task status
                const taskId = draggedItem.getAttribute('data-task-id');
                console.log('Task moved:', { taskId, newStatus });
                
                // Update task counters
                updateTaskCounters();
            }
        });
    });

    // Function to update task counters
    function updateTaskCounters() {
        columns.forEach(column => {
            const tasksCount = column.querySelector('.tasks').children.length;
            const headerText = column.querySelector('.column-header');
            const statusText = column.id.replace('-column', '');
            headerText.textContent = `${statusText} (${tasksCount})`;
        });
    }

    // Function to create a new task element
    function createTaskElement(taskData) {
        const task = document.createElement('div');
        task.className = 'task';
        task.setAttribute('data-task-id', taskData.id);
        task.innerHTML = `
            <div class="task-header">
                <h3>${taskData.title}</h3>
                <span class="task-status ${taskData.status}">${taskData.status}</span>
            </div>
            <p class="task-description">${taskData.description}</p>
            <div class="task-footer">
                <span class="task-priority ${taskData.priority}">${taskData.priority}</span>
                <span class="task-type">${taskData.type}</span>
                <span class="task-due-date">${taskData.dueDate}</span>
            </div>
        `;
        
        // Add drag and drop listeners to the new task
        addDragListeners(task);
        return task;
    }

    // Example task creation (you would typically get this data from your API)
    const exampleTask = {
        id: 1,
        title: 'Example Task',
        description: 'This is an example task to demonstrate the drag and drop functionality.',
        status: 'todo',
        priority: 'medium',
        type: 'feature',
        dueDate: '2024-01-01'
    };

    // Add example task to the todo column
    const todoColumn = document.querySelector('#todo-column .tasks');
    todoColumn.appendChild(createTaskElement(exampleTask));

    // Initial update of task counters
    updateTaskCounters();
});
