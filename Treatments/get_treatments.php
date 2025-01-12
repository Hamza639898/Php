<?php
include '../connection/db.php';

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT treatmentID, treatmentName, Description, cost FROM treatments";
    
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['treatmentID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['treatmentName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Description']) . "</td>";
        echo "<td>" . htmlspecialchars($row['cost']) . "</td>";
        echo "<td>
                <button class='btn btn-warning' onclick='editTreatment({$row['treatmentID']})'>Edit</button>
                <button class='btn btn-danger' onclick='deleteTreatment({$row['treatmentID']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
}
?>
