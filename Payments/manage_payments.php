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
    $sql = "INSERT INTO payments (InvoiceID, PaymentDate, PaymentAmount, PaymentMethod) 
            VALUES (:invoiceID, :paymentDate, :paymentAmount, :paymentMethod)";
} elseif ($operation === 'update') {
    $sql = "UPDATE payments SET InvoiceID=:invoiceID, PaymentDate=:paymentDate, PaymentAmount=:paymentAmount, 
            PaymentMethod=:paymentMethod WHERE PaymentID=:id";
} elseif ($operation === 'delete') {
    $sql = "DELETE FROM payments WHERE PaymentID = :id";
}

$stmt = $pdo->prepare($sql);

if ($operation !== 'delete') {
    $stmt->bindParam(':invoiceID', $input['invoiceID']);
    $stmt->bindParam(':paymentDate', $input['paymentDate']);
    $stmt->bindParam(':paymentAmount', $input['paymentAmount']);
    $stmt->bindParam(':paymentMethod', $input['paymentMethod']);
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
