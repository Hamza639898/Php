<?php
include '../connection/db.php';

header('Content-Type: application/json');

// الحصول على الجدول والـ id
if (!isset($_GET['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing treatment id"]);
    exit;
}

$id = $_GET['id'];

// استعلام للحصول على بيانات العلاج
$sql = "SELECT treatmentID, treatmentName, Description, cost 
        FROM treatments 
        WHERE treatmentID = :id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$entry = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entry) {
    echo json_encode($entry);
} else {
    echo json_encode(["status" => "error", "message" => "Treatment not found"]);
}
?>
