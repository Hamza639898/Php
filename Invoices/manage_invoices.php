<?php
include '../connection/db.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    exit;
}

$operation = $input['operation'];
$id = isset($input['id']) ? $input['id'] : null;

if ($operation === 'insert') {
    $sql = "INSERT INTO invoices (PatientID, InvoiceDate, TotalAmount, AmountPaid, PaymentStatus) 
            VALUES (:patientID, :invoiceDate, :totalAmount, :amountPaid, :paymentStatus)";
} elseif ($operation === 'update') {
    $sql = "UPDATE invoices SET PatientID=:patientID, InvoiceDate=:invoiceDate, TotalAmount=:totalAmount, 
            AmountPaid=:amountPaid, PaymentStatus=:paymentStatus WHERE InvoiceID=:id";
} elseif ($operation === 'delete') {
    $sql = "DELETE FROM invoices WHERE InvoiceID = :id";
}

$stmt = $pdo->prepare($sql);

if ($operation !== 'delete') {
    $stmt->bindParam(':patientID', $input['patientID']);
    $stmt->bindParam(':invoiceDate', $input['invoiceDate']);
    $stmt->bindParam(':totalAmount', $input['totalAmount']);
    $stmt->bindParam(':amountPaid', $input['amountPaid']);
    $stmt->bindParam(':paymentStatus', $input['paymentStatus']);
}

if ($operation === 'update' || $operation === 'delete') {
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
}

try {
    $stmt->execute();
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
