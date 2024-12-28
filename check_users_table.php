<?php
require_once 'config/connexion.php';

try {
    $pdo = DatabaseConfig::getConnection();
    $stmt = $pdo->query("DESCRIBE Users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Colonnes de la table Users :\n";
    foreach ($columns as $column) {
        echo "Nom: " . $column['Field'] . 
             ", Type: " . $column['Type'] . 
             ", Null: " . $column['Null'] . 
             ", Key: " . $column['Key'] . 
             ", Default: " . ($column['Default'] ?? 'NULL') . 
             ", Extra: " . $column['Extra'] . "\n";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
