<?php
require_once __DIR__ . '/../db/database.php';
header("Content-Type: application/json");

if (!isset($_SERVER["HTTP_AUTHORIZATION"])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$token = $_SERVER["HTTP_AUTHORIZATION"];
$stmt = $db->prepare("UPDATE users SET api_token = NULL WHERE api_token = ?");
$stmt->bindValue(1, $token);

if ($stmt->execute()) {
    echo json_encode(["message" => "Logged out successfully"]);
} else {
    echo json_encode(["error" => "Invalid token"]);
}
