<?php
include '../connection/db.php';

header('Content-Type: application/json');


if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing patient id"]);
    exit;
}

$id = $_GET['id'];


$sql = "SELECT patientID, FirstName, LastName, DateOfBirth, Gender, ContactNumber, Email, Address, MedicalHistory 
        FROM patients 
        WHERE patientID = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entry) {
    echo json_encode($entry);
} else {
    echo json_encode(["status" => "error", "message" => "Patient not found"]);
}
?>
