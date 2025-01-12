<?php
include '../connection/db.php';

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT dentistID, FirstName, LastName, Specialty, ContactNumber, Email FROM dentists";
    
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['dentistID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['FirstName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['LastName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Specialty']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ContactNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
        echo "<td>
                <button class='btn btn-warning' onclick='editDentist({$row['dentistID']})'>Edit</button>
                <button class='btn btn-danger' onclick='deleteDentist({$row['dentistID']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
}
?>
