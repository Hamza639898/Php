<?php
include '../connection/db.php';

header('Content-Type: application/json');


$sql = "SELECT InvoiceID FROM invoices";
$stmt = $pdo->query($sql);
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);


echo json_encode(['invoices' => $invoices]);
?>
