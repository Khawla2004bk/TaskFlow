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

function showModal(modal) {
    modal.classList.add('active');
}

function hideModal(modal) {
    modal.classList.remove('active');
    modal.reset();
}

function createTaskElement(task) {
    const taskEl = document.createElement('div');
    taskEl.className = 'task-card';
    taskEl.draggable = true;
    taskEl.dataset.id = task.id;
    
    // Mapping du texte de priorité
    const priorityTextMap = {
        '1': 'Basse',
        '2': 'Moyenne', 
        '3': 'Haute',
    };
    
    taskEl.innerHTML = `
        <div id="header">
        <h3 class="task-title">${task.title}</h3>
        <a><img src="images/icon.png" id="icon" alt="" onclick="showTaskDetails(${task.id})"></a>
        </div>
        <p class="task-description">${task.description}</p>
        <div class="task-meta">
            <span class="priority priority-${task.priority}">${priorityTextMap[task.priority] || 'Basse'}</span>
            <span>${task.dueDate}</span>
        </div>
    `;

    return taskEl;
}

function addTask(task) {
    tasks.push(task);
    const taskEl = createTaskElement(task);
    
    const targetColumn = mapStatusToColumn(task.status);
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

        column.addEventListener('drop', e => {
            e.preventDefault();
            const draggingTask = document.querySelector('.dragging');
            if (!draggingTask) return;

            // Déterminer le nouveau statut en fonction de la colonne
            let newStatus;
            if (column.closest('#todo-column')) {
                newStatus = 1; // to-do
            } else if (column.closest('#inprogress-column')) {
                newStatus = 2; // in-progress
            } else if (column.closest('#done-column')) {
                newStatus = 3; // completed
            }

            const taskId = draggingTask.dataset.id;

            // Envoyer une requête pour mettre à jour le statut
            fetch('../api/update_task_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `task_id=${taskId}&new_status=${newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                // if (!data.success) {
                    // Optionnel : Annuler le déplacement visuel si la mise à jour échoue
                // }
            })
            .catch(error => {});

            draggingTask.classList.remove('dragging');
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
    const detailsModal = document.getElementById('detailsModal');
    detailsModal.classList.add('active');
    
    const task = tasks.find(t => t.id === taskId);
    if (!task) {
        console.error('Tâche non trouvée pour l\'ID:', taskId);
        return;
    }

    // Créer un conteneur modal avec l'ID de la tâche
    detailsModal.innerHTML = `
        <div class="modal-backdrop absolute inset-0 flex items-center justify-center">
            <div class="modal-content bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4" data-task-id="${taskId}">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">${task.title}</h2>
                        </div>
                        <button 
                            onclick="hideModal(detailsModal)"
                            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-4 mt-4">
                        <span class="priority-${task.priority} text-white text-sm px-3 py-1 rounded-full">${mapPriorityToText(task.priority)}</span>
                        <span class="text-gray-400 text-sm">Due: ${task.dueDate}</span>
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
                </div>
            </div>
        </div>
    `;

    console.log('showTaskDetails - Task Details:', {
        taskId: taskId,
        task: task
    });
}

function mapStatusToText(statusValue) {
    const statusMap = {
        '1': 'to-do',
        '2': 'in-progress',
        '3': 'completed'
    };
    return statusMap[statusValue] || 'to-do';
}

function mapPriorityToText(priority) {
    const priorityMap = {
        '1': 'Basse',
        '2': 'Moyenne',
        '3': 'Haute',
        '4': 'Urgente'
    };
    return priorityMap[priority] || 'Basse';
}

function mapStatusToColumn(status) {
    const columnMap = {
        'to-do': document.querySelector('#todo-column .tasks'),
        'in-progress': document.querySelector('#inprogress-column .tasks'),
        'completed': document.querySelector('#done-column .tasks')
    };
    return columnMap[status] || columnMap['to-do'];
}

// Fonction pour charger les tâches existantes
function loadTasks() {
    fetch('../api/get_tasks.php')
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Vider d'abord toutes les colonnes
            Object.values(columns).forEach(column => {
                column.innerHTML = '';
            });

            // Réinitialiser le tableau de tâches
            tasks = [];

            // Ajouter chaque tâche
            data.tasks.forEach(task => {
                addTask({
                    id: task.id,
                    title: task.title,
                    description: task.description || '',
                    priority: task.priority, // Garder le numéro de priorité
                    status: mapStatusToText(task.status),
                    dueDate: task.due_date
                });
            });
        } else {
            console.error('Erreur lors du chargement des tâches:', data.message);
            alert('Impossible de charger les tâches : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur de chargement des tâches:', error);
        alert('Erreur de chargement des tâches');
    });
}

// Charger les tâches au chargement de la page
document.addEventListener('DOMContentLoaded', loadTasks);

addTaskBtn.addEventListener('click', () => showModal(taskModal));
cancelBtn.addEventListener('click', () => hideModal(taskModal));
taskModal.addEventListener('click', e => {
    if (e.target === taskModal) hideModal(taskModal);
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
            hideModal(taskModal);
        } else {
            console.error('Erreur lors de l\'ajout de la tâche:', data.message);
            alert('Impossible d\'ajouter la tâche : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur d\'ajout de la tâche:', error);
        alert('Erreur d\'ajout de la tâche');
    });
});

// ******************* form invite *******************

const formContainer = document.getElementById('form-container');
const inviter = document.getElementById('inviter');

formContainer.style.display='none'
inviter.addEventListener('click',function(){
   
    formContainer.style.display='block' 
});

document.getElementById('cancel').addEventListener('click',function(){
   formContainer.style.display='none'

});

function assignTask() {
    const assignEmailInput = document.getElementById('assignEmail');
    const email = assignEmailInput.value.trim();
    const detailsModal = document.getElementById('detailsModal');
    const modalContent = detailsModal.querySelector('.modal-content');
    const taskId = modalContent ? parseInt(modalContent.dataset.taskId, 10) : null;

    console.log('Debug assignTask:', {
        email: email,
        taskId: taskId,
        modalContent: modalContent,
        detailsModal: detailsModal
    });

    if (!email || !taskId) {
        console.error('Email ou taskId invalide:', {
            email: email,
            taskId: taskId
        });
        alert('Veuillez saisir une adresse email valide et sélectionner une tâche');
        return;
    }

    fetch('api/debug_assign_task.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}&task_id=${taskId}`
    })
    .then(response => {
        // Vérifier le statut de la réponse
        if (!response.ok) {
            // Tenter de lire le contenu de la réponse pour plus de détails
            return response.text().then(text => {
                console.error('Réponse d\'erreur:', text);
                throw new Error(`Erreur HTTP: ${response.status} - ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Réponse du serveur:', data);
        
        // Vérifier explicitement la propriété success
        if (data.success === true) {
            alert('Tâche assignée avec succès');
            assignEmailInput.value = '';
        } else {
            // Afficher un message d'erreur détaillé
            const errorMessage = data.message || 'Erreur lors de l\'assignation de la tâche';
            console.error('Erreur d\'assignation:', errorMessage);
            alert(errorMessage);
        }
    })
    .catch(error => {
        console.error('Erreur de requête:', error);
        
        // Message d'erreur plus informatif
        let errorMessage = 'Une erreur est survenue lors de l\'assignation';
        if (error instanceof TypeError) {
            errorMessage += ' (problème de réseau ou de connexion)';
        } else if (error.message) {
            errorMessage += `: ${error.message}`;
        }
        
        alert(errorMessage);
    });
}
