// ********************* gestion des taches *****************

const addTaskBtn = document.getElementById('addTaskBtn');
const taskModal = document.getElementById('taskModal');
const taskForm = document.getElementById('taskForm');
const cancelBtn = document.getElementById('cancelBtn');
const columns = {
    "to-do": document.querySelector('#todo-column .tasks'),
    "in-progress": document.querySelector('#inprogress-column .tasks'),
    "completed": document.querySelector('#done-column .tasks')
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
        <div id="header">
        <h3 class="task-title">${task.title}</h3>
        <a><img src="images/icon.png" id="icon" alt=""></a>
        </div>
        <p class="task-description">${task.description}</p>
        <div class="task-meta">
            <span class="priority priority-${task.priority}">${task.priority}</span>
            <span>${task.dueDate}</span>
        </div>
        <i class="fas fa-info-circle details-icon" onclick="showTaskDetails(${task.id})"></i>
    `;

    return taskEl;
}

function addTask(task) {
    console.log('Adding task:', task);
    tasks.push(task);
    const taskEl = createTaskElement(task);
    
    const targetColumn = mapStatusToColumn(task.status);
    console.log('Target column for task:', targetColumn);
    targetColumn.appendChild(taskEl);
    
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
    if (column) {
        column.addEventListener('dragover', e => {
            e.preventDefault();
            const dragging = document.querySelector('.dragging');
            if (dragging) {
                const afterElement = getDragAfterElement(column, e.clientY);
                
                if (afterElement) {
                    column.insertBefore(dragging, afterElement);
                } else {
                    column.appendChild(dragging);
                }
            }
        });
    }
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

function showTaskDetails(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (!task) return;

    alert(`
Task Details:
Title: ${task.title}
Description: ${task.description}
Priority: ${task.priority}
Due Date: ${task.dueDate}
    `);
}

function mapStatusToText(statusValue) {
    const statusMap = {
        '1': 'to-do',
        '2': 'in-progress',
        '3': 'completed'
    };
    return statusMap[statusValue] || 'to-do';
}

function mapStatusToColumn(status) {
    const columnMap = {
        'to-do': document.querySelector('#todo-column .tasks'),
        'in-progress': document.querySelector('#inprogress-column .tasks'),
        'completed': document.querySelector('#done-column .tasks')
    };
    console.log('Mapping status:', status);
    console.log('Target column:', columnMap[status]);
    return columnMap[status] || columnMap['to-do'];
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
        description: taskForm.description.value || '',
        priority: taskForm.priority.value,
        type: taskForm.type.value,
        dueDate: taskForm.dueDate.value || null,
        status: mapStatusToText(taskForm.status.value)
    };

    fetch('../api/add_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(task)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            task.id = data.taskId;
            addTask(task);
            hideModal();
        } else {
            console.error('Server error:', data.error);
            alert(`Erreur lors de l'ajout de la tâche : 
                Détails de l'erreur : ${data.error}
                Données envoyées : 
                - Titre : ${task.title}
                - Type : ${task.type}
                - Statut : ${task.status}
                - Priorité : ${task.priority}`);
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert('Une erreur est survenue lors de l\'ajout de la tâche. Veuillez vérifier la console pour plus de détails.');
    });
});

// Add some sample tasks
[
    {
        id: 1,
        title: 'Implement new feature',
        description: 'Add user authentication to the app',
        priority: 'high',
        dueDate: '2023-06-30',
        status: "to-do"
    },
    {
        id: 2,
        title: 'Fix critical bug',
        description: 'Address performance issue in production',
        priority: 'high',
        dueDate: '2023-06-25',
        status: "in-progress"
    },
    {
        id: 3,
        title: 'Design review',
        description: 'Complete UI/UX review for new features',
        priority: 'low',
        dueDate: '2023-06-20',
        status: "completed"
    }
].forEach(task => addTask(task));

// ******************* form invite *******************

const formContainer = document.getElementById('form-container');
const inviter = document.getElementById('inviter');
console.log(formContainer)

formContainer.style.display='none'
inviter.addEventListener('click',function(){
   
    formContainer.style.display='block' 
});

document.getElementById('cancel').addEventListener('click',function(){
   formContainer.style.display='none'

});
