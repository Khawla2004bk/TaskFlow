<?php

class Role {
    // Constantes pour les rôles
    public const ADMIN = 1;
    public const USER = 2;

    // Définition des permissions par rôle
    private const PERMISSIONS = [
        self::ADMIN => [
            'create_user',
            'edit_user',
            'delete_user',
            'view_all_users',
            'manage_system',
            'access_admin_panel'
        ],
        self::USER => [
            'edit_own_profile',
            'view_own_data',
            'create_task',
            'edit_own_task',
            'delete_own_task'
        ],
    ];

    /**
     * Vérifie si un rôle a une permission spécifique
     * 
     * @param int $role Rôle à vérifier
     * @param string $permission Permission à vérifier
     * @return bool
     */
    public static function hasPermission(int $role, string $permission): bool {
        // Si le rôle n'existe pas, retourne false
        if (!isset(self::PERMISSIONS[$role])) {
            return false;
        }

        // Vérifie si la permission est dans la liste des permissions du rôle
        return in_array($permission, self::PERMISSIONS[$role]);
    }

    /**
     * Récupère le libellé du rôle
     * 
     * @param int $role Code du rôle
     * @return string Libellé du rôle
     */
    public static function getRoleLabel(int $role): string {
        return match($role) {
            self::ADMIN => 'Administrateur',
            self::USER => 'Utilisateur',
            default => 'Rôle inconnu'
        };
    }

    /**
     * Liste tous les rôles disponibles
     * 
     * @return array Liste des rôles
     */
    public static function getAllRoles(): array {
        return [
            self::ADMIN => self::getRoleLabel(self::ADMIN),
            self::USER => self::getRoleLabel(self::USER),
        ];
    }
}
