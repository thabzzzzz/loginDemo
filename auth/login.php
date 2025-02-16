<?php
require_once __DIR__ . '/../db/database.php';
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);


error_log("Login Request: " . print_r($input, true));

if (!isset($input["email"], $input["password"])) {
    echo json_encode(["error" => "Missing fields"]);
    exit();
}

try {
    // prepare statement
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bindValue(1, $input["email"]);
    $stmt->execute();


    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($input["password"], $user["password"])) {

        $token = bin2hex(random_bytes(40));


        $update = $db->prepare("UPDATE users SET api_token = ? WHERE email = ?");
        $update->bindValue(1, $token);
        $update->bindValue(2, $input["email"]);
        $update->execute();


        error_log("Generated Token: " . $token);


        echo json_encode(["token" => $token]);
    } else {

        echo json_encode(["error" => "Invalid credentials"]);
    }
} catch (PDOException $e) {

    error_log("Database Error: " . $e->getMessage());
    echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
}
