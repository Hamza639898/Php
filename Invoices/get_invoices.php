<?php
include '../connection/db.php';

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT i.InvoiceID, CONCAT(p.FirstName, ' ', p.LastName) AS PatientName, i.InvoiceDate, i.TotalAmount, i.AmountPaid, i.PaymentStatus
            FROM invoices i 
            INNER JOIN patients p ON i.PatientID = p.PatientID";
    
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['InvoiceID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PatientName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['InvoiceDate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['TotalAmount']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AmountPaid']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PaymentStatus']) . "</td>";
        echo "<td>
                <button class='btn btn-warning' onclick='editInvoice({$row['InvoiceID']})'>Edit</button>
                <button class='btn btn-danger' onclick='deleteInvoice({$row['InvoiceID']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
}
?>
