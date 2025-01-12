<?php
include '../connection/db.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing patient treatment id"]);
    exit;
}

$id = $_GET['id'];

$sql = "SELECT PatientTreatmentID, PatientID, TreatmentID, AppointmentID, TreatmentDate, TreatmentNotes 
        FROM patienttreatments 
        WHERE PatientTreatmentID = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entry) {
    echo json_encode($entry);
} else {
    echo json_encode(["status" => "error", "message" => "Patient treatment not found"]);
}
?>
