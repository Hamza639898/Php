<?php
include '../connection/db.php';

header('Content-Type: application/json');


if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing appointment id"]);
    exit;
}

$id = $_GET['id'];

$sql = "SELECT AppointmentID, PatientID, DentistID, AppointmentDate, AppointmentTime, Reason, Status 
        FROM appointments 
        WHERE AppointmentID = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entry) {
    echo json_encode($entry);
} else {
    echo json_encode(["status" => "error", "message" => "Appointment not found"]);
}
?>
