<?php
/**
 * Adminer Loader
 */
if (false) {
    class Adminer {}
}

error_reporting(E_ALL & ~~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', '0');

$htpasswd_file = __DIR__.'/../../storage/app/adminer/.htpasswd';

if (! file_exists($htpasswd_file)) {
    header('HTTP/1.0 500 Internal Server Error');
    exit('🚫 Security error: Passwd file missing. Please contact administrator.');
}

$auth_data = explode(':', trim(file_get_contents($htpasswd_file)), 2);
$user = $_SERVER['PHP_AUTH_USER'] ?? '';
$pass = $_SERVER['PHP_AUTH_PW'] ?? '';

if (! hash_equals($auth_data[0] ?? '', $user) || ! hash_equals($auth_data[1] ?? '', crypt($pass, 'salt'))) {
    header('WWW-Authenticate: Basic realm="Ruby DB Admin Secure Access"');
    header('HTTP/1.0 401 Unauthorized');
    exit('🚫 Unauthorized access. Please provide correct credentials.');
}

function get_env_var($key, $default = null)
{
    $env_file = __DIR__.'/../../.env';
    if (! file_exists($env_file)) {
        return $default;
    }
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        if (trim($name) === $key) {
            return trim($value, '"\' ');
        }
    }

    return $default;
}

function adminer_object()
{
    class AdminerCustom extends Adminer
    {
        public function name()
        {
            return 'Ruby DB Admin';
        }

        public function navigationUser()
        {
            return '';
        }

        public function head()
        {
            $base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
            echo '<link rel="stylesheet" type="text/css" href="' . $base_path . 'adminer.css?v=' . filemtime(__DIR__ . '/adminer.css') . '">';
            return false;
        }

        public function databases($open = false)
        {
            return [get_env_var('DB_DATABASE', 'ruby_laravel')];
        }

        public function database()
        {
            $db = $_GET['db'] ?? '';
            $allowed = get_env_var('DB_DATABASE', 'ruby_laravel');

            return ($db === $allowed) ? $db : $allowed;
        }

        public function selectLimit()
        {
            return 10;
        }

        public function loginForm()
        {
            $host = get_env_var('DB_HOST', 'mysql');
            $port = get_env_var('DB_PORT', '3306');
            $server = ($port && $port !== '3306') ? "$host:$port" : $host;
            ?>
            <input type="hidden" name="auth[driver]" value="server">
            <input type="hidden" name="auth[server]" value="<?php echo htmlspecialchars($server, ENT_QUOTES, 'UTF-8'); ?>">
            <table cellspacing="0">
                <tr><th>Username<td><input name="auth[username]" value="" id="username" autofocus>
                <tr><th>Password<td><input type="password" name="auth[password]" value="">
            </table>
            <p><input type="submit" value="Login">
            <?php
            return true;
        }
    }

    return new AdminerCustom;
}

include './adminer.php.core';
