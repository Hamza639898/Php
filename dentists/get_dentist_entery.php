<?php
include '../connection/db.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing dentist id"]);
    exit;
}

$id = $_GET['id'];

$sql = "SELECT dentistID, FirstName, LastName, Specialty, ContactNumber, Email 
        FROM dentists 
        WHERE dentistID = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entry) {
    echo json_encode($entry);
} else {
    echo json_encode(["status" => "error", "message" => "Dentist not found"]);
}
?>
