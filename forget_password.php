<?php
include 'connection/db.php'; // تضمين ملف الاتصال بقاعدة البيانات

$step = 1; // البدء بالخطوة الأولى وهي إدخال اسم المستخدم

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && !isset($_POST['security_answer'])) {
        // الخطوة الأولى: التحقق من اسم المستخدم وعرض سؤال الأمان
        $username = $_POST['username'];

        $query = "SELECT security_question FROM user WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            // عرض سؤال الأمان
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $security_question = $user['security_question'];
            $step = 2; // الانتقال إلى الخطوة الثانية
        } else {
            $error = "Invalid username.";
        }
    } elseif (isset($_POST['username']) && isset($_POST['security_answer'])) {
        // الخطوة الثانية: التحقق من جواب الأمان
        $username = $_POST['username'];
        $security_answer = $_POST['security_answer'];

        $query = "SELECT * FROM user WHERE username = :username AND security_answer = :security_answer";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':security_answer', $security_answer);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            // عرض كلمة المرور المخزنة
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $stored_password = $user['password']; // كلمة المرور المخزنة (التي يجب أن تكون مشفرة)

            $success = "Your password is: " . htmlspecialchars($stored_password);
            $step = 3; // الانتقال إلى الخطوة الثالثة
        } else {
            $error = "Invalid security answer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
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
            width: 400px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center">Forget Password</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <!-- زر للعودة إلى صفحة تسجيل الدخول -->
            <form action="login.php">
                <button type="submit" class="btn btn-primary btn-block mt-3">Go to Login</button>
            </form>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <!-- الخطوة الأولى: إدخال اسم المستخدم -->
            <form action="forget_password.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Next</button>
            </form>

        <?php elseif ($step == 2): ?>
            <!-- الخطوة الثانية: عرض سؤال الأمان والإجابة -->
            <form action="forget_password.php" method="post">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                <div class="form-group">
                    <label for="security_question">Security Question</label>
                    <input type="text" class="form-control" id="security_question" value="<?php echo htmlspecialchars($security_question); ?>" disabled>
                </div>
                <div class="form-group">
                    <label for="security_answer">Answer</label>
                    <input type="text" class="form-control" id="security_answer" name="security_answer" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
