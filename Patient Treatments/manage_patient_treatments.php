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
    $sql = "INSERT INTO patienttreatments (PatientID, TreatmentID, AppointmentID, TreatmentDate, TreatmentNotes) 
            VALUES (:patientID, :treatmentID, :appointmentID, :treatmentDate, :treatmentNotes)";
} elseif ($operation === 'update') {
    $sql = "UPDATE patienttreatments SET PatientID=:patientID, TreatmentID=:treatmentID, AppointmentID=:appointmentID, 
            TreatmentDate=:treatmentDate, TreatmentNotes=:treatmentNotes WHERE PatientTreatmentID=:id";
} elseif ($operation === 'delete') {
    $sql = "DELETE FROM patienttreatments WHERE PatientTreatmentID = :id";
}

$stmt = $pdo->prepare($sql);

if ($operation !== 'delete') {
    $stmt->bindParam(':patientID', $input['patientID']);
    $stmt->bindParam(':treatmentID', $input['treatmentID']);
    $stmt->bindParam(':appointmentID', $input['appointmentID']);
    $stmt->bindParam(':treatmentDate', $input['treatmentDate']);
    $stmt->bindParam(':treatmentNotes', $input['treatmentNotes']);
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
