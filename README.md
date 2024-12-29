# TaskFlow - Système de Gestion de Tâches

## 🚀 Description du Projet

TaskFlow est une application web moderne de gestion de tâches conçue pour aider les équipes et les individus à organiser, suivre et collaborer efficacement sur leurs projets.

## ✨ Fonctionnalités Principales

- **Gestion de Tâches Dynamique**
  - Création et gestion de tâches
  - Tableau Kanban avec colonnes "To Do", "In Progress", "Done"
  - Support de différents types de tâches (Basic, Bug, Feature)

- **Système d'Authentification**
  - Inscription et connexion des utilisateurs
  - Gestion des rôles (Admin, Utilisateur)
  - Système de permissions sécurisé

- **Fonctionnalités Avancées**
  - Assignation de tâches à des membres de l'équipe
  - Validation dynamique des champs
  - Gestion des priorités

## 🛠 Technologies Utilisées

- **Backend**
  - PHP (POO)
  - MySQL
  - PDO pour la gestion des bases de données

- **Frontend**
  - HTML5
  - CSS3 (Tailwind CSS)
  - JavaScript vanilla
  - AJAX pour les interactions dynamiques

- **Sécurité**
  - Sessions PHP
  - Validation côté serveur
  - Protection contre les injections SQL
  - Gestion des rôles et permissions

## 📦 Prérequis

- PHP 7.4+
- MySQL 5.7+
- Apache ou Nginx
- Navigateur web moderne

## 🔧 Installation

1. Clonez le dépôt
```bash
git clone https://github.com/Khawla2004bk/TaskFlow.git
```

2. Configurez la base de données
- Créez un fichier `config/connexion.php`
- Configurez vos paramètres de connexion

3. Importez le schéma SQL
- Utilisez le fichier `database/table.sql`

4. Lancez le serveur
- Utilisez XAMPP, WAMP ou un serveur PHP intégré

## 🔐 Configuration

- Modifiez `config/connexion.php` avec vos paramètres de base de données
- Ajustez les paramètres de sécurité dans `config/config.php`

## 🤝 Contribution

1. Forkez le projet
2. Créez votre branche de fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Commitez vos modifications (`git commit -m 'Add some AmazingFeature'`)
4. Poussez votre branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request