
// Get all task cards and add click event listeners
document.querySelectorAll('.task').forEach(task => {
    task.addEventListener('click', (e) => {
        // Prevent drag start when clicking for details
        e.preventDefault();
        showTaskDetails(task);
    });
});

function showTaskDetails(taskElement) {
    const modal = document.getElementById('taskDetailsModal');
    const backdrop = modal.querySelector('.modal-backdrop');
    const title = taskElement.querySelector('h3').textContent;
    const description = taskElement.querySelector('.task-description').textContent;
    const priority = taskElement.querySelector('.task-priority').textContent;
    const dueDate = taskElement.querySelector('.task-due-date').textContent;

    // Update modal content
    document.getElementById('detailsTitle').textContent = title;
    document.getElementById('detailsDescription').textContent = description;
    document.getElementById('detailsPriority').textContent = priority;
    document.getElementById('detailsPriority').className = `task-priority ${priority.toLowerCase()}`;
    document.getElementById('detailsDueDate').textContent = dueDate;

    // Show modal with animation
    backdrop.classList.add('active');
}

function closeDetailsModal() {
    const modal = document.getElementById('taskDetailsModal');
    const backdrop = modal.querySelector('.modal-backdrop');
    backdrop.classList.remove('active');
}

function assignTask() {
    const emailInput = document.getElementById('assignEmail');
    const messageDiv = document.getElementById('assignmentMessage');
    const email = emailInput.value.trim();
    
    if (!email) {
        messageDiv.textContent = 'Please enter an email address';
        messageDiv.className = 'assignment-message error';
        return;
    }

    // Simulate API call
    const assignButton = emailInput.nextElementSibling;
    assignButton.disabled = true;
    assignButton.textContent = 'Assigning...';

    setTimeout(() => {
        assignButton.disabled = false;
        assignButton.textContent = 'Assign';
        emailInput.value = '';
        
        messageDiv.textContent = `Task assigned to ${email}`;
        messageDiv.className = 'assignment-message success';
        
        setTimeout(() => {
            messageDiv.textContent = '';
        }, 3000);
    }, 1000);
}

// Close modal when clicking outside
document.getElementById('taskDetailsModal').addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-backdrop')) {
        closeDetailsModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeDetailsModal();
    }
});
