<?php
include 'connection/db.php';  // تأكد من أن الاتصال بقاعدة البيانات صحيح

header('Content-Type: application/json');

// التحقق من وجود التواريخ في الطلب
if (isset($_GET['startDate']) && isset($_GET['endDate'])) {
    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];

    // استعلام SQL لجلب المرضى المسجلين بين التواريخ المحددة
    $sql = "SELECT PatientID, FirstName, LastName, RegistrationDate, ContactNumber
            FROM patients
            WHERE RegistrationDate BETWEEN :startDate AND :endDate";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();

        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // إذا تم العثور على بيانات، إرجاعها بصيغة JSON
        if ($patients) {
            echo json_encode($patients);
        } else {
            // إذا لم يتم العثور على مرضى ضمن النطاق الزمني المحدد
            echo json_encode([]);
        }
    } catch (PDOException $e) {
        // إرجاع رسالة خطأ في حال حدوث مشكلة في الاستعلام
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    // إذا لم يتم إرسال التواريخ
    echo json_encode(['error' => 'Invalid request: missing startDate or endDate']);
}
?>
