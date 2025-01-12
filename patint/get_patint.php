<?php
include '../connection/db.php';

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT patientID, FirstName, LastName, DateOfBirth, Gender, ContactNumber, Email, Address, MedicalHistory FROM patients";
    
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['patientID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['FirstName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['LastName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DateOfBirth']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Gender']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ContactNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Address']) . "</td>";
        echo "<td>" . htmlspecialchars($row['MedicalHistory']) . "</td>";
        echo "<td>
                <button class='btn btn-warning' onclick='editPatient({$row['patientID']})'>Edit</button>
                <button class='btn btn-danger' onclick='deletePatient({$row['patientID']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
}
?>
