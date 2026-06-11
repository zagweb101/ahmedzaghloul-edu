<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RotateAdminPasswordCommand extends Command
{
    protected $signature = 'platform:rotate-admin-password
                            {email : Admin email address}
                            {--password= : New password (omit to be prompted)}';

    protected $description = 'Set a new password for an admin account';

    public function handle(): int
    {
        $email = (string) $this->argument('email');
        $password = (string) ($this->option('password') ?: $this->secret('New password'));

        $validator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', 'min:12']],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $user = User::query()
            ->where('email', $email)
            ->where('is_admin', true)
            ->first();

        if (! $user) {
            $this->error("Admin account not found for [{$email}].");

            return self::FAILURE;
        }

        $user->update(['password' => Hash::make($password)]);

        $this->info("Password updated for {$email}.");

        return self::SUCCESS;
    }
}
