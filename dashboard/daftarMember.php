<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lab IVSS - Daftar Member</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

<?php
// Tampilkan alert jika ada error (satu alert berisi semua pesan)
if (!empty($_SESSION['register_errors'])) {
    $msg = implode("\\n", array_map('addslashes', $_SESSION['register_errors']));
    // siapkan old values untuk repopulate
    $old = $_SESSION['old'] ?? [];
    // hapus session setelah dipakai
    unset($_SESSION['register_errors'], $_SESSION['old']);
    echo "<script>document.addEventListener('DOMContentLoaded', function(){ alert(\"{$msg}\"); });</script>";
} else {
    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['old']);
}
?>

    <div class="container-fluid p-0">
        <div class="row no-gutters">
            <div class="col-md-6 d-none d-md-block bg-register-image"></div>
            <div class="col-md-6 login-right">
                <div class="card o-hidden border-0 shadow-lg my-5 login-card">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Daftar Member</h1>
                            </div>

                            <!-- Form: gunakan grid Bootstrap dengan form-row / form-group yang benar -->
                            <form class="user" method="post" action="daftarMember_process.php">

                                <div class="form-group">
                                    <input type="text" name="name" class="form-control form-control-user" id="name" placeholder="Nama Lengkap" required
                                           value="<?php echo isset($old['name']) ? htmlspecialchars($old['name']) : ''; ?>">
                                </div>

                                <div class="form-group">
                                    <input type="text" name="nim" class="form-control form-control-user" id="nim" placeholder="NIM" required
                                           value="<?php echo isset($old['nim']) ? htmlspecialchars($old['nim']) : ''; ?>">
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="text" name="jurusan" class="form-control form-control-user" id="jurusan" placeholder="Jurusan"
                                               value="<?php echo isset($old['jurusan']) ? htmlspecialchars($old['jurusan']) : ''; ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="prodi" class="form-control form-control-user" id="prodi" placeholder="Prodi" required
                                               value="<?php echo isset($old['prodi']) ? htmlspecialchars($old['prodi']) : ''; ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="dosenPembimbing" class="form-control form-control-user" id="dosenPembimbing" placeholder="Dosen Pembimbing" required
                                           value="<?php echo isset($old['dosenPembimbing']) ? htmlspecialchars($old['dosenPembimbing']) : ''; ?>">
                                </div>

                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-user" id="email" placeholder="Email Mahasiswa" required
                                           value="<?php echo isset($old['email']) ? htmlspecialchars($old['email']) : ''; ?>">
                                </div>

                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-control-user" id="password" placeholder="Password" required>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="showPass">
                                        <label class="custom-control-label" for="showPass">Tampilkan Password</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Daftar Member
                                </button>
                            </form>

                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var chk = document.getElementById('showPass');
            var pwd = document.getElementById('password');
            if (chk && pwd) {
                chk.addEventListener('change', function () {
                    pwd.type = this.checked ? 'text' : 'password';
                });
            }
        });
    </script>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>