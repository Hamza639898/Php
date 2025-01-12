<?php
session_start(); 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    include 'connection/db.php';

    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']); 

    
    $query = "SELECT * FROM user WHERE username = :username AND password = :password";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password); 
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user_id'] = $user['id']; 

       
        if ($remember) {
            setcookie('username', $username, time() + (86400 * 30), "/"); 
            setcookie('password', $password, time() + (86400 * 30), "/"); 
        }

        header('Location: index.php'); 
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}


$saved_username = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
$saved_password = isset($_COOKIE['password']) ? $_COOKIE['password'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .login-container {
            width: 300px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .forgot-password {
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center">Login</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?php echo htmlspecialchars($saved_username); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" 
                       value="<?php echo htmlspecialchars($saved_password); ?>" required>
            </div>
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" 
                       <?php echo $saved_username ? 'checked' : ''; ?>>
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <div class="forgot-password">
            <a href="forget_password.php">Forget My Password?</a>
        </div>
    </div>
</body>
</html>
