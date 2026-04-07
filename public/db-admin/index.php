<?php
/**
 * Adminer Secure Loader
 *
 * Triple Protection:
 * Layer 1: Apache .htaccess (IP whitelist + Basic Auth via .htpasswd-server)
 * Layer 2: PHP Basic Auth (self-service credentials via .htpasswd-app)
 * Layer 3: Adminer MySQL login (database-level credentials)
 */
if (false) {
    class Adminer {}
}

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', '0');

$htpasswd_file = __DIR__.'/../../storage/app/adminer/.htpasswd-app';
$htpasswd_dir = dirname($htpasswd_file);

// ============================================================
// SELF-SERVICE: First-run setup or password change
// ============================================================
$action = $_GET['action'] ?? '';

if ($action === 'setup' || ! file_exists($htpasswd_file)) {
    handlePasswordSetup($htpasswd_file, $htpasswd_dir, ! file_exists($htpasswd_file));
    exit;
}

// ============================================================
// Layer 2: PHP Basic Auth — application-level password
// ============================================================
$auth_data = explode(':', trim(file_get_contents($htpasswd_file)), 2);
$user = $_SERVER['PHP_AUTH_USER'] ?? '';
$pass = $_SERVER['PHP_AUTH_PW'] ?? '';

$valid_user = hash_equals($auth_data[0] ?? '', $user);
$valid_pass = password_verify($pass, $auth_data[1] ?? '');

if (! $valid_user || ! $valid_pass) {
    header('WWW-Authenticate: Basic realm="Ruby DB Admin Application Access"');
    header('HTTP/1.0 401 Unauthorized');
    exit('🚫 Unauthorized access.');
}

// ============================================================
// Password change (requires current auth first)
// ============================================================
if ($action === 'change-password') {
    handlePasswordChange($htpasswd_file, $user);
    exit;
}

// ============================================================
// Environment helper
// ============================================================
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
            return trim($value, '"\'');
        }
    }
    return $default;
}

// ============================================================
// Adminer customisation
// ============================================================
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
            $base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\').'/';
            echo '<link rel="stylesheet" type="text/css" href="'.$base_path.'adminer.css?v='.filemtime(__DIR__.'/adminer.css').'">';
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
            <p style="margin-top:1em;font-size:12px;color:#888;">
                <a href="?action=change-password" style="color:#666;">🔑 Change Application Password</a>
                &nbsp;|&nbsp;
                <em>To change your DB password: <code>ALTER USER USER() IDENTIFIED BY 'new_password';</code></em>
            </p>
            <?php
            return true;
        }
    }
    return new AdminerCustom;
}

include './adminer.php.core';

// ============================================================
// Self-service password handlers
// ============================================================

/**
 * Handle first-run setup or full credential reset.
 */
function handlePasswordSetup(string $file, string $dir, bool $isFirstRun): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['_token'] ?? '';
        $sessionToken = $_SESSION['adminer_setup_token'] ?? '';

        if (! $sessionToken || ! hash_equals($sessionToken, $token)) {
            $error = 'Invalid security token. Please try again.';
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['password_confirm'] ?? '';

            if (strlen($username) < 3) {
                $error = 'Username must be at least 3 characters.';
            } elseif (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters.';
            } elseif ($password !== $confirm) {
                $error = 'Passwords do not match.';
            } else {
                if (! is_dir($dir)) {
                    mkdir($dir, 0750, true);
                }
                $hash = password_hash($password, PASSWORD_BCRYPT);
                file_put_contents($file, $username.':'.$hash);
                chmod($file, 0640);
                $success = 'Credentials set successfully! You can now access the database tool.';
                unset($_SESSION['adminer_setup_token']);
            }
        }
    }

    // Generate fresh CSRF token for the form
    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['adminer_setup_token'] = $csrfToken;

    renderForm(
        $isFirstRun ? 'First-Time Setup' : 'Reset Credentials',
        $isFirstRun
            ? 'Welcome! Please create your application-level credentials to secure this interface.'
            : 'Set new application-level credentials below.',
        $csrfToken,
        $error,
        $success,
        $success ? '.' : null
    );
}

/**
 * Handle password change for an authenticated user.
 */
function handlePasswordChange(string $file, string $currentUser): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['_token'] ?? '';
        $sessionToken = $_SESSION['adminer_change_token'] ?? '';

        if (! $sessionToken || ! hash_equals($sessionToken, $token)) {
            $error = 'Invalid security token. Please try again.';
        } else {
            $newUser = trim($_POST['username'] ?? '');
            $newPass = $_POST['password'] ?? '';
            $confirm = $_POST['password_confirm'] ?? '';

            if (strlen($newUser) < 3) {
                $error = 'Username must be at least 3 characters.';
            } elseif (strlen($newPass) < 8) {
                $error = 'Password must be at least 8 characters.';
            } elseif ($newPass !== $confirm) {
                $error = 'Passwords do not match.';
            } else {
                $hash = password_hash($newPass, PASSWORD_BCRYPT);
                file_put_contents($file, $newUser.':'.$hash);
                chmod($file, 0640);
                $success = 'Credentials updated! Your browser will ask for the new credentials on the next request.';
                unset($_SESSION['adminer_change_token']);
            }
        }
    }

    $csrfToken = bin2hex(random_bytes(32));
    $_SESSION['adminer_change_token'] = $csrfToken;

    renderForm(
        'Change Application Password',
        "Currently authenticated as: <strong>{$currentUser}</strong>",
        $csrfToken,
        $error,
        $success,
        $success ? '.' : null
    );
}

/**
 * Render the credential form HTML.
 */
function renderForm(
    string $title,
    string $subtitle,
    string $csrfToken,
    string $error,
    string $success,
    ?string $redirectUrl
): void {
    header('Content-Type: text/html; charset=UTF-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 <?php echo htmlspecialchars($title); ?> — Ruby DB Admin</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#1a1a2e;color:#e0e0e0;min-height:100vh;display:flex;align-items:center;justify-content:center}
        .card{background:#16213e;border:1px solid #0f3460;border-radius:12px;padding:2.5rem;width:100%;max-width:420px;box-shadow:0 8px 32px rgba(0,0,0,.4)}
        h1{font-size:1.4rem;margin-bottom:.3rem;color:#fff}
        .sub{font-size:.85rem;color:#8899a6;margin-bottom:1.5rem;line-height:1.5}
        label{display:block;font-size:.8rem;font-weight:600;color:#8899a6;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.05em}
        input[type=text],input[type=password]{width:100%;padding:.7rem .9rem;background:#0f3460;border:1px solid #1a4a8a;border-radius:6px;color:#fff;font-size:.95rem;margin-bottom:1rem}
        input:focus{outline:none;border-color:#e94560;box-shadow:0 0 0 2px rgba(233,69,96,.2)}
        button{width:100%;padding:.75rem;background:#e94560;color:#fff;border:none;border-radius:6px;font-size:.95rem;font-weight:600;cursor:pointer;transition:background .2s}
        button:hover{background:#c73e54}
        .err{background:rgba(233,69,96,.15);border:1px solid #e94560;color:#ff6b81;padding:.7rem;border-radius:6px;margin-bottom:1rem;font-size:.85rem}
        .ok{background:rgba(46,213,115,.15);border:1px solid #2ed573;color:#7bed9f;padding:.7rem;border-radius:6px;margin-bottom:1rem;font-size:.85rem}
        .back{display:inline-block;margin-top:1rem;color:#8899a6;font-size:.8rem;text-decoration:none}
        .back:hover{color:#e94560}
    </style>
</head>
<body>
    <div class="card">
        <h1>🔐 <?php echo htmlspecialchars($title); ?></h1>
        <p class="sub"><?php echo $subtitle; ?></p>

        <?php if ($error): ?>
            <div class="err"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="ok"><?php echo htmlspecialchars($success); ?></div>
            <?php if ($redirectUrl): ?>
                <a href="<?php echo htmlspecialchars($redirectUrl); ?>" class="back">← Continue to Database Tool</a>
            <?php endif; ?>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="_token" value="<?php echo $csrfToken; ?>">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required minlength="3" autofocus
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required minlength="8">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="8">
                <button type="submit">Set Credentials</button>
            </form>
            <a href="." class="back">← Back to Database Tool</a>
        <?php endif; ?>
    </div>
</body>
</html>
    <?php
}
