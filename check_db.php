<?php
require_once 'config/connexion.php';

try {
    $pdo = DatabaseConfig::getConnection();
    $stmt = $pdo->query("DESCRIBE tasks");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Table Structure:\n";
    foreach ($columns as $column) {
        echo "Column: " . $column['Field'] . 
             ", Type: " . $column['Type'] . 
             ", Null: " . $column['Null'] . 
             ", Key: " . $column['Key'] . 
             ", Default: " . ($column['Default'] ?? 'NULL') . 
             ", Extra: " . $column['Extra'] . "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
