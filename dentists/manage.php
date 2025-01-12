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
    $sql = "INSERT INTO dentists (FirstName, LastName, Specialty, ContactNumber, Email) 
            VALUES (:firstName, :lastName, :specialty, :contactNumber, :email)";
} elseif ($operation === 'update') {
    $sql = "UPDATE dentists SET FirstName=:firstName, LastName=:lastName, Specialty=:specialty, 
            ContactNumber=:contactNumber, Email=:email 
            WHERE dentistID=:id";
} elseif ($operation === 'delete') {
    $sql = "DELETE FROM dentists WHERE dentistID = :id";
}

$stmt = $pdo->prepare($sql);

if ($operation !== 'delete') {
    $stmt->bindParam(':firstName', $input['firstName']);
    $stmt->bindParam(':lastName', $input['lastName']);
    $stmt->bindParam(':specialty', $input['specialty']);
    $stmt->bindParam(':contactNumber', $input['contactNumber']);
    $stmt->bindParam(':email', $input['email']);
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
