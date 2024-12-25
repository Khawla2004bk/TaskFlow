// ********************* gestion des taches *****************

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

// ******************* form invite *******************

const formContainer = document.getElementById('form-container');
const inviter = document.getElementById('inviter');


formContainer.style.display='none'
inviter.addEventListener('click',function(){
   
    formContainer.style.display='block' 
    console.log('okkkk')
});

document.getElementById('cancel').addEventListener('click',function(){
   formContainer.style.display='none'

});