document.addEventListener('DOMContentLoaded', () => {
    // Particle.js configuration
    particlesJS('particles-js', {
        particles: {
            number: { value: 80, density: { enable: true, value_area: 800 } },
            color: { value: "#ffffff" },
            shape: { type: "circle", stroke: { width: 0, color: "#000000" }, polygon: { nb_sides: 5 } },
            opacity: { value: 0.5, random: false, anim: { enable: false, speed: 1, opacity_min: 0.1, sync: false } },
            size: { value: 3, random: true, anim: { enable: false, speed: 40, size_min: 0.1, sync: false } },
            line_linked: { enable: true, distance: 150, color: "#ffffff", opacity: 0.4, width: 1 },
            move: { enable: true, speed: 6, direction: "none", random: false, straight: false, out_mode: "out", bounce: false, attract: { enable: false, rotateX: 600, rotateY: 1200 } }
        },
        interactivity: {
            detect_on: "canvas",
            events: { onhover: { enable: true, mode: "repulse" }, onclick: { enable: true, mode: "push" }, resize: true },
            modes: { grab: { distance: 400, line_linked: { opacity: 1 } }, bubble: { distance: 400, size: 40, duration: 2, opacity: 8, speed: 3 }, repulse: { distance: 200, duration: 0.4 }, push: { particles_nb: 4 }, remove: { particles_nb: 2 } }
        },
        retina_detect: true
    });

    // GSAP animations
    gsap.from("header", { opacity: 0, y: -50, duration: 1, ease: "power3.out" });
    gsap.from("h1", { opacity: 0, y: 50, duration: 1, delay: 0.5, ease: "power3.out" });
    gsap.from("main p", { opacity: 0, y: 50, duration: 1, delay: 0.7, ease: "power3.out" });
    gsap.from("main a", { opacity: 0, y: 50, duration: 1, delay: 0.9, ease: "power3.out", stagger: 0.2 });
    gsap.from(".glassmorphism", { opacity: 0, y: 50, duration: 1, delay: 1.1, ease: "power3.out", stagger: 0.2 });

    // Neon border effect on hover
    document.querySelectorAll('.glassmorphism').forEach(el => {
        el.addEventListener('mouseenter', () => {
            el.classList.add('neon-border');
        });
        el.addEventListener('mouseleave', () => {
            el.classList.remove('neon-border');
        });
    });

    // Parallax effect on scroll
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelector('main');
        parallax.style.transform = `translateY(${scrolled * 0.3}px)`;
    });
});

// ************ log in and sign up page **********

document.getElementById('switchToSignup').addEventListener('click', function(e) {
    e.preventDefault();
    const form = document.getElementById('loginForm');
    const title = document.querySelector('h2');
    const switchLink = document.getElementById('switchToSignup');

    if (title.textContent.includes('Welcome')) {
        title.textContent = 'Create Your Account';
        form.innerHTML = `
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="name" name="name" required class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign Up
                </button>
            </div>
        `;
        switchLink.textContent = 'Sign in';
    } else {
        title.textContent = 'Welcome to TaskMaster Pro';
        form.innerHTML = `
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign In
                </button>
            </div>
        `;
        switchLink.textContent = 'Sign up';
    }
});


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

