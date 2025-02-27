<?php  
require_once __DIR__ . '/../koneksi.php';



class AuthHelper {  
    private const SESSION_TIMEOUT = 3600;  

    private static function startSessionOnce() {  
        if (session_status() == PHP_SESSION_NONE) {  
            session_start();  
        }  
    }  

    private static function validateSession() {  
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {  
            self::logout();  
            return false;  
        }  

        if (isset($_SESSION['last_activity']) &&   
            (time() - $_SESSION['last_activity']) > self::SESSION_TIMEOUT) {  
            self::logout();  
            return false;  
        }  

        $_SESSION['last_activity'] = time();  
        return true;  
    }  

    public static function logout() {  
        self::startSessionOnce();  
        $_SESSION = [];  
        session_destroy();  
        header("Location: /moju/login.php");  
        exit;  
    }  

    public static function isLoggedIn() {  
        self::startSessionOnce();  
        return self::validateSession() &&   
               isset($_SESSION['user_id']) &&   
               isset($_SESSION['role']);  
    }  

    public static function getCurrentUser() {  
        self::startSessionOnce();  
        return self::isLoggedIn() ? $_SESSION : null;  
    }  

    public static function requireLogin() {  
        self::startSessionOnce();  
        if (!self::isLoggedIn()) {  
            header("Location: /moju/login.php");  
            exit;  
        }  
    }  

    public static function requireRole($allowedRoles = []) {  
        self::requireLogin();  
        
        $currentRole = $_SESSION['role'] ?? '';  
        
        // Perbaikan: Gunakan in_array untuk pencocokan role  
        if (!empty($allowedRoles) && !in_array($currentRole, $allowedRoles)) {  
            error_log("Unauthorized access attempt by user {$_SESSION['user_id']} with role $currentRole");  
            self::redirectAfterLogin();  
            exit;  
        }  
    }  

    public static function redirectAfterLogin() {  
        self::startSessionOnce();  
        
        $redirects = [  
            'admin' => '/Admin/pages/dashboard.php',  
            'admin artikel' => '/Admin/pages/artikel.php',  
            'user' => '/home.php'  
        ];  

        $role = $_SESSION['role'] ?? 'user';  
        $redirect = $redirects[$role] ?? '/home.php';  
        
        header("Location: $redirect");  
        exit;  
    }  
}