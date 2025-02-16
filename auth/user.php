<?php
require_once __DIR__ . '/../db/database.php';


$headers = getallheaders();


$authorizationHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

if (!$authorizationHeader) {
    echo json_encode(['error' => 'Unauthorized - No Authorization header found']);
    exit;
}


$token = str_replace('Bearer ', '', $authorizationHeader);


try {

    $stmt = $db->prepare("SELECT * FROM users WHERE api_token = ?");
    $stmt->bindValue(1, $token);
    $stmt->execute();


    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // return the user info
        echo json_encode([
            'name' => $user['name'],
            'email' => $user['email'],
            'message' => 'User successfully authenticated'
        ]);
    } else {

        echo json_encode(['error' => 'Invalid token']);
    }
} catch (PDOException $e) {

    echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
}
