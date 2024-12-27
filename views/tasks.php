<?php
require_once __DIR__ . '/../controllers/taskController.php';
require_once __DIR__ . '/../config/connexion.php';

// Vérifier la connexion de l'utilisateur
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

$taskController = new TaskController();
$userTasks = $taskController->getUserTasks();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Tâches</title>
</head>
<body>
    <h1>Mes Tâches</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($userTasks['tasks'])): ?>
        <p>Aucune tâche trouvée.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userTasks['tasks'] as $task): ?>
                    <tr>
                        <td><?= htmlspecialchars($task['title']) ?></td>
                        <td><?= htmlspecialchars($task['description']) ?></td>
                        <td><?= $task['priority'] ?></td>
                        <td><?= $task['status'] ?></td>
                        <td><?= $task['type'] ?></td>
                        <td>
                            <form action="index.php?action=update_task_status" method="post">
                                <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                <select name="new_status">
                                    <option value="1">À faire</option>
                                    <option value="2">En cours</option>
                                    <option value="3">Terminé</option>
                                </select>
                                <button type="submit">Mettre à jour</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h2>Créer une nouvelle tâche</h2>
    <form action="index.php?action=create_task" method="post">
        <input type="text" name="title" placeholder="Titre" required>
        <textarea name="description" placeholder="Description"></textarea>
        <select name="priority">
            <option value="1">Basse</option>
            <option value="2">Moyenne</option>
            <option value="3">Haute</option>
        </select>
        <select name="status">
            <option value="1">À faire</option>
            <option value="2">En cours</option>
            <option value="3">Terminé</option>
        </select>
        <select name="type">
            <option value="1">Basic</option>
            <option value="2">Bug</option>
            <option value="3">Feature</option>
        </select>
        <button type="submit">Créer</button>
    </form>
</body>
</html>
