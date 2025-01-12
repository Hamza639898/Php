<?php
include '../connection/db.php';

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT PaymentID, InvoiceID, PaymentDate, PaymentAmount, PaymentMethod FROM payments";
    
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['PaymentID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['InvoiceID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PaymentDate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PaymentAmount']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PaymentMethod']) . "</td>";
        echo "<td>
                <button class='btn btn-warning' onclick='editPayment({$row['PaymentID']})'>Edit</button>
                <button class='btn btn-danger' onclick='deletePayment({$row['PaymentID']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
}
?>
