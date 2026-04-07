<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AdminerPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adminer:password
                            {layer : Which layer to set — "server" (Apache) or "app" (PHP)}
                            {--show : Show current username without changing anything}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set or rotate credentials for the Adminer security layers';

    /**
     * The base directory for password files.
     */
    protected string $basePath;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->basePath = storage_path('app/adminer');
        $layer = $this->argument('layer');

        if (! in_array($layer, ['server', 'app'])) {
            $this->error('Layer must be "server" (Apache Basic Auth) or "app" (PHP Basic Auth).');

            return self::FAILURE;
        }

        $file = $this->getPasswordFile($layer);

        if ($this->option('show')) {
            return $this->showCurrentUser($file, $layer);
        }

        return $this->setPassword($file, $layer);
    }

    /**
     * Get the password file path for a given layer.
     */
    protected function getPasswordFile(string $layer): string
    {
        return $this->basePath.'/.htpasswd-'.$layer;
    }

    /**
     * Show the current username for a layer.
     */
    protected function showCurrentUser(string $file, string $layer): int
    {
        if (! File::exists($file)) {
            $this->warn("No credentials set for the [{$layer}] layer yet.");
            $this->info("Run: sail artisan adminer:password {$layer}");

            return self::SUCCESS;
        }

        $contents = trim(File::get($file));
        $username = explode(':', $contents, 2)[0] ?? '(unknown)';

        $this->info("Layer [{$layer}] — Current username: {$username}");

        return self::SUCCESS;
    }

    /**
     * Interactively set a new username and password for a layer.
     */
    protected function setPassword(string $file, string $layer): int
    {
        $layerLabel = $layer === 'server' ? 'Apache Basic Auth' : 'PHP Basic Auth';

        $this->newLine();
        $this->info("🔐 Setting credentials for: {$layerLabel}");
        $this->line("   File: {$file}");
        $this->newLine();

        $username = $this->ask('Enter username');
        if (empty($username)) {
            $this->error('Username cannot be empty.');

            return self::FAILURE;
        }

        $password = $this->secret('Enter password');
        if (empty($password)) {
            $this->error('Password cannot be empty.');

            return self::FAILURE;
        }

        $confirm = $this->secret('Confirm password');
        if ($password !== $confirm) {
            $this->error('Passwords do not match.');

            return self::FAILURE;
        }

        // Ensure directory exists
        File::ensureDirectoryExists($this->basePath, 0750);

        if ($layer === 'server') {
            // Apache uses its own password hashing (APR1-MD5 via htpasswd command)
            return $this->setApachePassword($file, $username, $password);
        }

        // PHP layer uses bcrypt via password_hash()
        return $this->setPhpPassword($file, $username, $password);
    }

    /**
     * Generate an Apache-compatible .htpasswd entry.
     */
    protected function setApachePassword(string $file, string $username, string $password): int
    {
        // Try using the htpasswd utility first (most reliable)
        $escapedFile = escapeshellarg($file);
        $escapedUser = escapeshellarg($username);
        $escapedPass = escapeshellarg($password);

        // -c = create, -b = batch mode (password on command line), -B = bcrypt
        $result = null;
        $output = [];
        exec("htpasswd -cbB {$escapedFile} {$escapedUser} {$escapedPass} 2>&1", $output, $result);

        if ($result === 0) {
            chmod($file, 0640);
            $this->newLine();
            $this->info('✅ Apache Basic Auth credentials set successfully.');
            $this->line("   Username: {$username}");
            $this->line("   File:     {$file}");

            return self::SUCCESS;
        }

        // Fallback: generate APR1 hash manually using PHP
        $hash = $this->apr1Md5($password);
        File::put($file, "{$username}:{$hash}");
        chmod($file, 0640);

        $this->newLine();
        $this->info('✅ Apache Basic Auth credentials set successfully (APR1 fallback).');
        $this->line("   Username: {$username}");
        $this->line("   File:     {$file}");

        return self::SUCCESS;
    }

    /**
     * Generate a PHP bcrypt .htpasswd entry.
     */
    protected function setPhpPassword(string $file, string $username, string $password): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        File::put($file, "{$username}:{$hash}");
        chmod($file, 0640);

        $this->newLine();
        $this->info('✅ PHP Basic Auth credentials set successfully.');
        $this->line("   Username: {$username}");
        $this->line("   File:     {$file}");

        return self::SUCCESS;
    }

    /**
     * Generate an APR1-MD5 hash (Apache's default format).
     *
     * Used as a fallback when the `htpasswd` binary is not available.
     */
    protected function apr1Md5(string $password): string
    {
        $salt = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
        $text = $password.'$apr1$'.$salt;
        $bin = pack('H32', md5($password.$salt.$password));

        for ($i = strlen($password); $i > 0; $i -= 16) {
            $text .= substr($bin, 0, min(16, $i));
        }

        for ($i = strlen($password); $i > 0; $i >>= 1) {
            $text .= ($i & 1) ? chr(0) : $password[0];
        }

        $bin = pack('H32', md5($text));

        for ($i = 0; $i < 1000; $i++) {
            $new = ($i & 1) ? $password : $bin;
            if ($i % 3) {
                $new .= $salt;
            }
            if ($i % 7) {
                $new .= $password;
            }
            $new .= ($i & 1) ? $bin : $password;
            $bin = pack('H32', md5($new));
        }

        $tmp = '';
        for ($i = 0; $i < 5; $i++) {
            $k = $i + 6;
            $j = $i + 12;
            if ($j === 16) {
                $j = 5;
            }
            $tmp = $bin[$i].$bin[$k].$bin[$j].$tmp;
        }

        $tmp = chr(0).chr(0).$bin[11].$tmp;

        $alphabet = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $result = '';
        for ($i = 0; $i < 18; $i += 3) {
            $value = (ord($tmp[$i]) << 16) | (ord($tmp[$i + 1]) << 8) | ord($tmp[$i + 2]);
            for ($j = 0; $j < 4; $j++) {
                $result .= $alphabet[$value & 0x3F];
                $value >>= 6;
            }
        }

        return '$apr1$'.$salt.'$'.$result;
    }
}
