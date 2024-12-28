// Gestion de la déconnexion
document.addEventListener('DOMContentLoaded', () => {
    const logoutButton = document.getElementById('logout') || document.getElementById('logoutBtn');
    
    if (logoutButton) {
        logoutButton.addEventListener('click', () => {
            fetch('../api/logout.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Rediriger vers la page de connexion
                        window.location.href = data.redirect;
                    } else {
                        console.error('Erreur de déconnexion');
                    }
                })
                .catch(error => {
                    console.error('Erreur de requête de déconnexion:', error);
                });
        });
    }
});
