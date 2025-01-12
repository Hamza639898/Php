<?php
// sessionka inuu bilawday in lahubiyo
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // in labilaawo siisanka
}

// in lahubiyo in login lasoo sameeyay iyo inkale
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


$user_id = $_SESSION['user_id'];

include 'connection/db.php';

$query = "SELECT username, user_type FROM user WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $username = $user['username'];
    $usertype = $user['user_type']; // 
} else {
  
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Dental Clinic Management System</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ["assets/css/fonts.min.css"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Custom CSS for Header -->
    <style>
        .clinic-header {
            background: linear-gradient(135deg, #4c669f, #3b5998, #192f6a); 
            padding: 15px 20px;
            color: white;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            width: 100%;
            text-align: center;
            position: relative;
        }

        .clinic-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 0;
            color: #f1c40f; 
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.4);
        }

        .clinic-header .sub-title {
            font-size: 1.1rem;
            color: #ecf0f1; 
            margin-top: 5px;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.4);
        }

        .user-info {
            position: absolute;
            top: 10px;
            right: 15px;
            text-align: right;
            color: #ffffff;
        }

        .user-info .username {
            font-size: 1.1rem;
            font-weight: 600;
            color: #f39c12; 
            display: flex;
            align-items: center;
        }

        .user-info .usertype {
            font-size: 0.95rem;
            font-weight: 400;
            color: #bdc3c7; 
            display: flex;
            align-items: center;
        }

        .user-info i {
            margin-right: 8px;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="clinic-header">
        <h1>Dental Clinic Management System</h1>
        <div class="sub-title">Your trusted partner in dental care</div>
        <div class="user-info">
            <img src="users.png" alt="User Profile">
            <div class="username">
                <i class="fas fa-user"></i> <?php echo $username; ?>
            </div>
            <div class="usertype">
                <i class="fas fa-user-tag"></i> <?php echo ucfirst($usertype); ?>
            </div>
            <form action="logout.php" method="post" style="display: inline-block; margin-left: 15px;">
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>
