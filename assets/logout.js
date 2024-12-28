// Gestion de la déconnexion
document.getElementById('logout')?.addEventListener('click', () => {
    fetch('../api/logout.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Rediriger vers la page de connexion
                window.location.href = data.redirect;
            } else {
                // Optionnel : Gérer l'erreur
            }
        })
        .catch(error => {});

});
