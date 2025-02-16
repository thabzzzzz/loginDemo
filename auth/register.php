<?php
require_once __DIR__ . '/../db/database.php';
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input["name"], $input["email"], $input["password"])) {
    echo json_encode(["error" => "Missing fields"]);
    exit();
}

$hashedPassword = password_hash($input["password"], PASSWORD_BCRYPT);
try {

    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bindValue(1, $input["name"]);
    $stmt->bindValue(2, $input["email"]);
    $stmt->bindValue(3, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["message" => "User registered successfully"]);
    }
} catch (PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(["error" => "Email already exists"]);
    } else {
        echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
    }
}
