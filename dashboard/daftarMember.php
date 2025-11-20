<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Lab IVSS - Daftar Member</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

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
                                    <input type="text" name="name" class="form-control form-control-user" id="name" placeholder="Nama Lengkap" required>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="nim" class="form-control form-control-user" id="nim" placeholder="NIM" required>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="text" name="jurusan" class="form-control form-control-user" id="jurusan" placeholder="Jurusan">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <input type="text" name="prodi" class="form-control form-control-user" id="prodi" placeholder="Prodi" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="text" name="dosenPembimbing" class="form-control form-control-user" id="dosenPembimbing" placeholder="Dosen Pembimbing" required>
                                </div>

                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-user" id="email" placeholder="Email Mahasiswa" required>
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