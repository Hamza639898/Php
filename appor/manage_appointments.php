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
    $sql = "INSERT INTO appointments (PatientID, DentistID, AppointmentDate, AppointmentTime, Reason, Status) 
            VALUES (:patientID, :dentistID, :appointmentDate, :appointmentTime, :reason, :status)";
} elseif ($operation === 'update') {
    $sql = "UPDATE appointments SET PatientID=:patientID, DentistID=:dentistID, AppointmentDate=:appointmentDate, 
            AppointmentTime=:appointmentTime, Reason=:reason, Status=:status WHERE AppointmentID=:id";
} elseif ($operation === 'delete') {
    $sql = "DELETE FROM appointments WHERE AppointmentID = :id";
}

$stmt = $pdo->prepare($sql);

if ($operation !== 'delete') {
    $stmt->bindParam(':patientID', $input['patientID']);
    $stmt->bindParam(':dentistID', $input['dentistID']);
    $stmt->bindParam(':appointmentDate', $input['appointmentDate']);
    $stmt->bindParam(':appointmentTime', $input['appointmentTime']);
    $stmt->bindParam(':reason', $input['reason']);
    $stmt->bindParam(':status', $input['status']);
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
