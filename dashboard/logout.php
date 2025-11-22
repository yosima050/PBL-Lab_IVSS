<?php
session_start();

// Kosongkan semua variabel session
$_SESSION = [];

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Hancurkan session di server
session_destroy();

// Redirect ke halaman login (ubah target jika perlu)
header('Location: login.php?logged_out=1');
exit;