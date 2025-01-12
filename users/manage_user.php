<?php
include '../connection/db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    exit;
}

$operation = $input['operation'];
$id = isset($input['id']) ? $input['id'] : null;

if ($operation === 'insert') {
    $sql = "INSERT INTO user (username, password, email, security_question, security_answer, user_type) 
            VALUES (:username, :password, :email, :security_question, :security_answer, :user_type)";
} elseif ($operation === 'update') {
    $sql = "UPDATE user SET username=:username, password=:password, email=:email, 
            security_question=:security_question, security_answer=:security_answer, 
            user_type=:user_type WHERE id=:id";
} elseif ($operation === 'delete') {
    $sql = "DELETE FROM user WHERE id = :id";
}

$stmt = $pdo->prepare($sql);

if ($operation !== 'delete') {
    $stmt->bindParam(':username', $input['username']);
    $stmt->bindParam(':password', $input['password']);
    $stmt->bindParam(':email', $input['email']);
    $stmt->bindParam(':security_question', $input['security_question']);
    $stmt->bindParam(':security_answer', $input['security_answer']);
    $stmt->bindParam(':user_type', $input['user_type']);
}

if ($operation === 'update' || $operation === 'delete') {
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
}

try {
    $stmt->execute();
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
