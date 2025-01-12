<?php
include '../connection/db.php';

header('Content-Type: text/html');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT id, username, email, security_question, user_type FROM user";
    
    $stmt = $pdo->query($sql);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['security_question']) . "</td>";
        echo "<td>" . htmlspecialchars($row['user_type']) . "</td>";
        echo "<td>
                <button class='btn btn-warning' onclick='editUser({$row['id']})'>Edit</button>
                <button class='btn btn-danger' onclick='deleteUser({$row['id']})'>Delete</button>
              </td>";
        echo "</tr>";
    }
}
?>
