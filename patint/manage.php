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
    $sql = "INSERT INTO patients (FirstName, LastName, DateOfBirth, Gender, ContactNumber, Email, Address, MedicalHistory) 
            VALUES (:firstName, :lastName, :dateOfBirth, :gender, :contactNumber, :email, :address, :medicalHistory)";
} elseif ($operation === 'update') {
    $sql = "UPDATE patients SET FirstName=:firstName, LastName=:lastName, DateOfBirth=:dateOfBirth, Gender=:gender, 
            ContactNumber=:contactNumber, Email=:email, Address=:address, MedicalHistory=:medicalHistory 
            WHERE patientID=:id";
} elseif ($operation === 'delete') {
    $sql = "DELETE FROM patients WHERE patientID = :id";
}

$stmt = $pdo->prepare($sql);

if ($operation !== 'delete') {
    $stmt->bindParam(':firstName', $input['firstName']);
    $stmt->bindParam(':lastName', $input['lastName']);
    $stmt->bindParam(':dateOfBirth', $input['dateOfBirth']);
    $stmt->bindParam(':gender', $input['gender']);
    $stmt->bindParam(':contactNumber', $input['contactNumber']);
    $stmt->bindParam(':email', $input['email']);
    $stmt->bindParam(':address', $input['address']);
    $stmt->bindParam(':medicalHistory', $input['medicalHistory']);
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
