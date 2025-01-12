<?php
include '../connection/db.php';

header('Content-Type: application/json');


if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing payment ID"]);
    exit;
}

$id = $_GET['id'];

$sql = "SELECT PaymentID, InvoiceID, PaymentDate, PaymentAmount, PaymentMethod 
        FROM payments WHERE PaymentID = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entry) {
    echo json_encode($entry);
} else {
    echo json_encode(["status" => "error", "message" => "Payment not found"]);
}
?>
