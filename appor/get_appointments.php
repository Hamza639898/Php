<?php
include '../connection/db.php';

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT appointments.AppointmentID, CONCAT(patients.FirstName, ' ', patients.LastName) AS PatientName, 
            CONCAT(dentists.FirstName, ' ', dentists.LastName) AS DentistName, appointments.AppointmentDate, 
            appointments.AppointmentTime, appointments.Reason, appointments.Status 
            FROM appointments 
            INNER JOIN patients ON appointments.PatientID = patients.PatientID 
            INNER JOIN dentists ON appointments.DentistID = dentists.DentistID";
    
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['AppointmentID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PatientName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DentistName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AppointmentDate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AppointmentTime']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Reason']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Status']) . "</td>";
        echo "<td>
                <button class='btn btn-warning' onclick='editEntry({$row['AppointmentID']})'>Edit</button>
                <button class='btn btn-danger' onclick='deleteEntry({$row['AppointmentID']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
}
?>
