<?php
include '../connection/db.php';

header('Content-Type: application/json');

// الحصول على تفاصيل المستخدم بناءً على ID
if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing user ID"]);
    exit;
}

$id = $_GET['id'];

$sql = "SELECT id, username, password, email, security_question, security_answer, user_type 
        FROM user WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entry) {
    echo json_encode($entry);
} else {
    echo json_encode(["status" => "error", "message" => "User not found"]);
}
?>
