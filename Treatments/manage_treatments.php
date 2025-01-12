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
    $sql = "INSERT INTO treatments (treatmentName, Description, cost) 
            VALUES (:treatmentName, :description, :cost)";
} elseif ($operation === 'update') {
    $sql = "UPDATE treatments SET treatmentName=:treatmentName, Description=:description, cost=:cost 
            WHERE treatmentID=:id";
} elseif ($operation === 'delete') {
    $sql = "DELETE FROM treatments WHERE treatmentID = :id";
}

$stmt = $pdo->prepare($sql);

if ($operation !== 'delete') {
    $stmt->bindParam(':treatmentName', $input['treatmentName']);
    $stmt->bindParam(':description', $input['description']);
    $stmt->bindParam(':cost', $input['cost']);
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
