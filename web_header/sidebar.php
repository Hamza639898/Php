<?php
//hubinta in login lasameeyay
if (session_status() == PHP_SESSION_NONE) {
    session_start();  
}

if (!isset($_SESSION['user_id'])) {
  // ucelint login page haduu login samyan
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

// hubinta in user uu soogalay 
if ($user) {
    $username = $user['username'];
    $usertype = $user['user_type']; 
} else {

    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<div class="sidebar" style="background-color: #1e1e2f; color: #fff;">
    <div class="sidebar-logo">
        <div class="logo-header" style="background-color: #1e1e2f;">
            <a href="index.php" class="logo">
                <img src="128.png" alt="navbar brand" class="navbar-brand" height="40" />
            </a>
            <p>Dental Clinic System</p>
        </div>
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <!-- Home -->
                <li class="nav-item">
                    <a href="index.php">
                        <i class="fas fa-home" style="color: #ffcc00;"></i>
                        <p style="color: #ffffff; font-size: 1.1rem;">Home</p>
                    </a>
                </li>

                <!-- Admin Section (Visible to Admin only) -->
                <?php if ($usertype == 'admin') : ?>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#admin" aria-expanded="false" class="collapsed">
                            <i class="fas fa-user-cog" style="color: #ff6666;"></i>
                            <p style="color: #ffffff; font-size: 1.1rem;">Admin</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="admin">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="general_user.php">
                                        <span class="sub-item" style="color: #ffcc00;">User</span>
                                    </a>
                                </li>
                               
                                <li>
                                    <a href="general_dentists.php">
                                        <span class="sub-item" style="color: #ffcc00;">doctor</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                <!-- Registration Section (Visible to User, Admin only) -->
                <?php if ($usertype == 'admin' || $usertype == 'user') : ?>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#dashboard" aria-expanded="false" class="collapsed">
                            <i class="fas fa-edit" style="color: #66ff66;"></i>
                            <p style="color: #ffffff; font-size: 1.1rem;">Registration</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="dashboard">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="general_paitent.php">
                                        <span class="sub-item" style="color: #66ff66;">Registration paitent</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="generel_apportiment.php">
                                        <span class="sub-item" style="color: #66ff66;">Appointments</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                <!-- Treatments Section (Visible to User, Admin only) -->
                <?php if ($usertype == 'admin' || $usertype == 'user') : ?>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#otherPages" aria-expanded="false" class="collapsed">
                            <i class="fas fa-briefcase-medical" style="color: #ff9999;"></i>
                            <p style="color: #ffffff; font-size: 1.1rem;">Treatments</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="otherPages">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="general_Treatments.php">
                                        <span class="sub-item" style="color: #ff9999;">Treatments</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="general_Patient Treatments.php">
                                        <span class="sub-item" style="color: #ff9999;">Patient Treatments</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                <!-- Finance Section (Visible to Finance and Admin) -->
                <?php if ($usertype == 'admin' || $usertype == 'finance') : ?>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#other" aria-expanded="false" class="collapsed">
                            <i class="fas fa-dollar-sign" style="color: #ffcc33;"></i>
                            <p style="color: #ffffff; font-size: 1.1rem;">Finance</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="other">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="general_Invoices.php">
                                        <span class="sub-item" style="color: #ffcc33;">Invoices</span>
                                    </a>
                                </li>
                               
                                <li>
                                    <a href="general_Payments.php">
                                        <span class="sub-item" style="color: #ffcc33;">Payments</span>
                                    </a>
                                </li>
                                
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                <!-- Reports Section (Visible to User, Admin only) -->
                <?php if ($usertype == 'admin' || $usertype == 'user') : ?>
                    <li class="nav-item">
                        <a data-bs-toggle="collapse" href="#report" aria-expanded="false" class="collapsed">
                            <i class="fas fa-chart-line" style="color: #66ccff;"></i>
                            <p style="color: #ffffff; font-size: 1.1rem;">Reports</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="report">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="patient_reports.php">
                                        <span class="sub-item" style="color: #66ccff;">Patient Reports</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="invoice_payment_dashboard.php">
                                        <span class="sub-item" style="color: #66ccff;">Invoice & Payment Reports</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="general_Payments.php">
                                        <span class="sub-item" style="color: #66ccff;">Payments</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="general_Expenses.php">
                                        <span class="sub-item" style="color: #66ccff;">Expenses</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
