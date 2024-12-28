<?php
// Afficher toutes les erreurs pour le débogage
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start();

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        ob_end_clean(); 
        http_response_code(400);
        echo json_encode([
            'error' => 'Invalid JSON data', 
            'raw_input' => $json
        ]);
        exit;
    }

    $requiredFields = ['title', 'description', 'priority', 'type', 'status'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            ob_end_clean(); // Clear any output
            http_response_code(400);
            echo json_encode([
                'error' => "Missing required field: $field", 
                'received_data' => $data
            ]);
            exit;
        }
    }

    $typeMap = [
        'bug' => 1,
        'feature' => 2,
        'basic' => 3
    ];

    $statusMap = [
        'to-do' => 1,
        'in-progress' => 2,
        'completed' => 3
    ];

    $priorityMap = [
        'low' => 1,
        'medium' => 2,
        'high' => 3
    ];

    $typeId = $typeMap[$data['type']] ?? 3; 
    $statusId = $statusMap[$data['status']] ?? 1;
    $priorityId = $priorityMap[$data['priority']] ?? 2; 

    $dueDate = null;
    if (!empty($data['dueDate']) && $data['dueDate'] !== 'null') {
        $tempDate = date_create($data['dueDate']);
        if ($tempDate) {
            $dueDate = date_format($tempDate, 'Y-m-d');
        }
    }

    error_log('Received task data: ' . print_r($data, true));

    // Récupérer l'ID de l'utilisateur connecté
    session_start();
    $createdBy = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        $pdo = DatabaseConfig::getConnection();

        // Modifier la requête pour inclure created_by
        $stmt = $pdo->prepare("INSERT INTO tasks (title, description, type, status, priority, due_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        // Bind parameters
        $stmt->bindValue(1, $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(2, $data['description'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(3, $typeId, PDO::PARAM_INT);
        $stmt->bindValue(4, $statusId, PDO::PARAM_INT);
        $stmt->bindValue(5, $priorityId, PDO::PARAM_INT);
        $stmt->bindValue(6, $dueDate, PDO::PARAM_STR);
        $stmt->bindValue(7, $createdBy, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $taskId = $pdo->lastInsertId();
            echo json_encode([
                'success' => true, 
                'taskId' => $taskId,
                'message' => 'Task added successfully',
                'details' => [
                    'title' => $data['title'],
                    'type' => $typeId,
                    'status' => $statusId,
                    'priority' => $priorityId,
                    'created_by' => $createdBy
                ]
            ]);
        } else {
            // Gestion des erreurs comme précédemment
            $errorInfo = $stmt->errorInfo();
            error_log('Database Error: ' . print_r($errorInfo, true));
            error_log('SQL Error Details: ' . json_encode([
                'title' => $data['title'],
                'description' => $data['description'] ?? '',
                'type' => $typeId,
                'status' => $statusId,
                'priority' => $priorityId,
                'dueDate' => $dueDate
            ]));

            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'error' => $errorInfo[2],
                'error_code' => $errorInfo[0],
                'driver_error_code' => $errorInfo[1],
                'input_data' => $data
            ]);
        }
    } catch (PDOException $e) {
        error_log('PDO Exception: ' . $e->getMessage());
        error_log('Exception Trace: ' . $e->getTraceAsString());

        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'input_data' => $data
        ]);
    }
} else {
    ob_end_clean();

    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
}

exit();
?>
