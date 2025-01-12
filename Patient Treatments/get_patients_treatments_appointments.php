<?php
include '../connection/db.php';

header('Content-Type: application/json');


$patients_sql = "SELECT PatientID, CONCAT(FirstName, ' ', LastName) AS FullName FROM patients";
$patients_stmt = $pdo->query($patients_sql);
$patients = $patients_stmt->fetchAll(PDO::FETCH_ASSOC);


$treatments_sql = "SELECT TreatmentID, treatmentName FROM treatments";
$treatments_stmt = $pdo->query($treatments_sql);
$treatments = $treatments_stmt->fetchAll(PDO::FETCH_ASSOC);

$appointments_sql = "SELECT AppointmentID FROM appointments";
$appointments_stmt = $pdo->query($appointments_sql);
$appointments = $appointments_stmt->fetchAll(PDO::FETCH_ASSOC);


echo json_encode([
    'patients' => $patients,
    'treatments' => $treatments,
    'appointments' => $appointments
]);
?>
