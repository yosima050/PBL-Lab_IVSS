<?php
session_start();
// Ensure db.php exists in the same folder
require_once __DIR__ . '/db.php';

// 1. Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

// 2. Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // We use 'email' as the identifier, matching the input field name below
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Username/Email and Password are required!";
    } else {
        try {
            // Query to find user by email
            // Note: If you want to allow login by Username OR Email, change the WHERE clause
            $sql = "SELECT u.id_users, u.nama_users, u.email_users, u.password, r.nama_role 
                    FROM users u 
                    JOIN role r ON u.id_role = r.id_role 
                    WHERE u.email_users = :email";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                // Verify Password (using plain text comparison as per your previous data)
                // If using hash: if (password_verify($password, $user['password'])) {
                if ($password === $user['password']) {
                    
                    // Set Session Variables
                    $_SESSION['user_id'] = $user['id_users'];
                    $_SESSION['nama_users'] = $user['nama_users'];
                    
                    // Map Database Role to Dashboard Logic
                    $db_role = strtolower($user['nama_role']);
                    
                    if (strpos($db_role, 'sistem') !== false) {
                        $_SESSION['role'] = 'admin_sistem';
                    } elseif (strpos($db_role, 'berita') !== false) {
                        $_SESSION['role'] = 'admin_berita';
                    } elseif (strpos($db_role, 'ketua') !== false) {
                        $_SESSION['role'] = 'ketua_lab';
                    } else {
                        $_SESSION['role'] = 'guest';
                    }

                    // Redirect to Dashboard
                    header("Location: dashboard.php");
                    exit;

                } else {
                    $error = "Password Incorrect!";
                }
            } else {
                $error = "Email not registered!";
            }
        } catch (PDOException $e) {
            $error = "System Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Lab IVSS - Login</title>

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container-fluid p-0">
        <div class="row no-gutters">
            <div class="col-md-6 d-none d-md-block bg-login-image"></div>
            <div class="col-md-6 login-right">
                <div class="card o-hidden border-0 shadow-lg my-5 login-card">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Selamat Datang Kembali!</h1>
                                <p class="text-muted">Masukkan Username dan Password</p>
                            </div>

                            <?php if(!empty($error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <?= htmlspecialchars($error) ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <form class="user" method="POST" action="">
                                <div class="form-group">
                                    <input type="text" name="email" class="form-control form-control-user"
                                        id="exampleInputUsername" aria-describedby="usernameHelp"
                                        placeholder="Username / Email" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-control-user"
                                        id="exampleInputPassword" placeholder="Password" required>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck">
                                        <label class="custom-control-label" for="customCheck">Remember
                                            Me</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Log In
                                </button>
                            </form>
                            
                            <hr>
                            <div class="text-center">
                                <a class="small" href="register.html">No account yet? Sign Up</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>