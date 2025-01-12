<?php
session_start();

// Session timeout duration (5 minutes)
$session_timeout = 5 * 60;

// Check last activity time
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    // Destroy session if it has expired
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include './web_header/header.php';
include './web_header/sidebar.php';
include 'connection/db.php';

try {
    // Fetch counts from the database
    $patients_query = "SELECT COUNT(*) AS total_patients FROM patients";
    $stmt = $pdo->prepare($patients_query);
    $stmt->execute();
    $patients_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_patients'];

    $appointments_query = "SELECT COUNT(*) AS total_appointments FROM appointments";
    $stmt = $pdo->prepare($appointments_query);
    $stmt->execute();
    $appointments_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_appointments'];

    $doctors_query = "SELECT COUNT(*) AS total_doctors FROM dentists";
    $stmt = $pdo->prepare($doctors_query);
    $stmt->execute();
    $doctors_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_doctors'];

    $staff_query = "SELECT COUNT(*) AS total_staff FROM staff";
    $stmt = $pdo->prepare($staff_query);
    $stmt->execute();
    $staff_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_staff'];

    $invoices_query = "SELECT COUNT(*) AS total_invoices FROM invoices";
    $stmt = $pdo->prepare($invoices_query);
    $stmt->execute();
    $invoices_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_invoices'];

    $users_query = "SELECT COUNT(*) AS total_users FROM user";
    $stmt = $pdo->prepare($users_query);
    $stmt->execute();
    $users_count = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

$current_user = "Hamza Doe";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .main-panel {
            flex-grow: 1;
            margin-left: 270px;
            padding: 30px;
            background-color: #fff;
            padding-top: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 0px;
        }

        .header h1 {
            font-size: 2rem;
            margin: 0;
        }

        .header .user-info {
            display: flex;
            align-items: center;
        }

        .header .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .card {
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            margin-bottom: 20px;
            transition: all 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
        }

        .card-body {
            padding: 25px;
        }

        .card i {
            font-size: 3.5rem;
            margin-bottom: 15px;
            color: #007bff;
        }

        h5 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .display-6 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #007bff;
        }

        .card:hover i {
            color: #ffcc00;
            transition: color 0.3s ease-in-out;
        }

        footer {
            width: 100%;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="main-panel">
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img src="crowd.png" alt="">
                            <h5>Registered Patients</h5>
                            <p class="card-text display-6"><?php echo $patients_count; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img src="crowd.png" alt="">
                            <h5>Appointments</h5>
                            <p class="card-text display-6"><?php echo $appointments_count; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                           <img src="doctors.png" alt="">
                            <h5>Doctors</h5>
                            <p class="card-text display-6"><?php echo $doctors_count; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="fas fa-user-nurse fa-3x"></i>
                            <h5>Staff</h5>
                            <p class="card-text display-6"><?php echo $staff_count; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img src="bills_1.png" alt="">
                            <h5>Bills & Invoices</h5>
                            <p class="card-text display-6"><?php echo $invoices_count; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <img src="users.png" alt="">
                            <h5>Users</h5>
                            <p class="card-text display-6"><?php echo $users_count; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <?php include './web_header/footer.php'; ?>
        </div>
    </div>
</body>
</html>
