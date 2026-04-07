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
                            {username : The server-level username for Basic Auth} 
                            {password? : The server-level password (optional, will be prompted if missing)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate or update server-level Basic Auth credentials for Adminer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $username = $this->argument('username');
        $password = $this->argument('password');

        if (! $password) {
            $password = $this->secret('Enter server-level password');
            $confirmation = $this->secret('Confirm server-level password');

            if ($password !== $confirmation) {
                $this->error('Passwords do not match.');

                return 1;
            }
        }

        $directory = storage_path('app/adminer');
        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $htpasswdFile = $directory.'/.htpasswd-server';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $entry = "{$username}:{$hashedPassword}";

        File::put($htpasswdFile, $entry.PHP_EOL);

        $this->info("Server-level credentials for '{$username}' updated successfully.");
        $this->warn("Storage: {$htpasswdFile}");

        return 0;
    }
}
