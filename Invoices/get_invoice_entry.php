<?php
include '../connection/db.php';

header('Content-Type: application/json');


if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing invoice ID"]);
    exit;
}

$id = $_GET['id'];

$sql = "SELECT InvoiceID, PatientID, InvoiceDate, TotalAmount, AmountPaid, PaymentStatus 
        FROM invoices WHERE InvoiceID = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entry) {
    echo json_encode($entry);
} else {
    echo json_encode(["status" => "error", "message" => "Invoice not found"]);
}
?>
