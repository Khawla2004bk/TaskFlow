<?php
try{
 $conn = new PDO("mysql:host=localhost; dbname=taskflow", "root", "");
 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql="SELECT users.name, users.email, users.password, users.created_at , users.updated_at FROM Users";


$stmt = $conn->query($sql);

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// print_r($users);

}catch (PDOException $e) {
    // Handle connection or query errors
    echo "Error: " . $e->getMessage();
}