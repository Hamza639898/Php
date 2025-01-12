<?php
include 'connection/db.php';

header('Content-Type: application/json');


$sql = "SELECT PatientID, CONCAT(FirstName, ' ', LastName) AS FullName FROM patients";
$stmt = $pdo->query($sql);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['patients' => $patients]);
?>
