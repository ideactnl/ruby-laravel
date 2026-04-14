<?php

/**
 * Global Adminer Object override.
 * Must be in the global namespace to be detected by Adminer core.
 */
if (! function_exists('adminer_object')) {
    function adminer_object()
    {
        class AdminerCustom extends \Adminer
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
                $cssPath = '/adminer.css';
                $styleFile = public_path('adminer.css');
                if (file_exists($styleFile)) {
                    $cssPath .= '?v='.filemtime($styleFile);
                }

                echo '<link rel="stylesheet" type="text/css" href="'.$cssPath.'">';

                echo '<script'.(function_exists('get_nonce') ? ' nonce="'.get_nonce().'"' : '').'>window.verifyVersion = function() {};</script>';

                return false;
            }

            public function databases($open = false)
            {
                return [config('database.connections.mysql.database', 'ruby_laravel')];
            }

            public function database()
            {
                $db = $_GET['db'] ?? '';
                $allowed = config('database.connections.mysql.database', 'ruby_laravel');

                return ($db === $allowed) ? $db : $allowed;
            }

            public function selectLimit()
            {
                return 10;
            }

            public function query($query)
            {
                $logFile = storage_path('logs/adminer_audit.log');
                $timestamp = date('Y-m-d H:i:s');
                $ip = request()->ip() ?? '0.0.0.0';
                $user = request()->user()->email ?? 'unknown';

                $cleanQuery = str_replace(["\n", "\r"], ' ', trim($query));
                $logLine = sprintf('[%s] [IP:%s] [AuthUser:%s] QUERY: %s%s',
                    $timestamp, $ip, $user, $cleanQuery, PHP_EOL);

                @file_put_contents($logFile, $logLine, FILE_APPEND);

                return true;
            }

            public function loginForm()
            {
                $host = config('database.connections.mysql.host', 'mysql');
                $port = config('database.connections.mysql.port', '3306');
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
}
