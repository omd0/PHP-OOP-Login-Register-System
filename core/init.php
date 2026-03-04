<?php
/**
 * Bootstrap (init): runs at the start of every page.
 * - Loads config, autoloads classes, starts session, then optionally restores login from "Remember me" cookie.
 */

session_start();

// Global config: DB credentials and session/cookie names (use env vars in production)
$GLOBALS['config'] = array(
    'mysql' => array(
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'db' => getenv('DB_NAME') ?: 'php_oop',
        'charset' => 'utf8mb4'
    ),
    'remember' => array(
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800   // 7 days in seconds
    ),
    'sessions' => array(
        'session_name' => 'user',  // session key holding current user id
        'token_name' => 'token'     // CSRF token key
    )
);

// Autoload: require classes/ClassName.php when you use "new ClassName"
// Skip PHP built-ins (e.g. PDO) so we only load project classes from classes/
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});

require_once 'functions/sanitize.php';

// "Remember me": if no session but cookie exists, log in using stored hash
if (Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('sessions/session_name'))) {
    $hash = Cookie::get(Config::get('remember/cookie_name'));
    $hashCheck = DB::getInstance()->get('users_session', array('hash', '=', $hash));

    if ($hashCheck->count()) {
        $user = new User($hashCheck->first()->user_id);
        $user->login();   // Session::put(session_name, user_id)
    }
}