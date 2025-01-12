<?php
include '../connection/db.php';

header('Content-Type: application/json');


$patients_sql = "SELECT PatientID, CONCAT(FirstName, ' ', LastName) AS FullName FROM patients";
$patients_stmt = $pdo->query($patients_sql);
$patients = $patients_stmt->fetchAll(PDO::FETCH_ASSOC);


$dentists_sql = "SELECT DentistID, CONCAT(FirstName, ' ', LastName) AS FullName FROM dentists";
$dentists_stmt = $pdo->query($dentists_sql);
$dentists = $dentists_stmt->fetchAll(PDO::FETCH_ASSOC);


echo json_encode([
    'patients' => $patients,
    'dentists' => $dentists
]);
?>
