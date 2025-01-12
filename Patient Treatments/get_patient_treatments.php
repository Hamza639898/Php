<?php
include '../connection/db.php';

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT pt.PatientTreatmentID, CONCAT(p.FirstName, ' ', p.LastName) AS PatientName, t.treatmentName, 
            pt.AppointmentID, pt.TreatmentDate, pt.TreatmentNotes 
            FROM patienttreatments pt 
            INNER JOIN patients p ON pt.PatientID = p.PatientID 
            INNER JOIN treatments t ON pt.TreatmentID = t.TreatmentID";
    
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['PatientTreatmentID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PatientName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['treatmentName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AppointmentID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['TreatmentDate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['TreatmentNotes']) . "</td>";
        echo "<td>
                <button class='btn btn-warning' onclick='editPatientTreatment({$row['PatientTreatmentID']})'>Edit</button>
                <button class='btn btn-danger' onclick='deletePatientTreatment({$row['PatientTreatmentID']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
}
?>
