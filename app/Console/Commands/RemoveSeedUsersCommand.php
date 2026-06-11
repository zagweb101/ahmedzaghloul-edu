<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RemoveSeedUsersCommand extends Command
{
    protected $signature = 'platform:remove-seed-users {--force : Skip confirmation}';

    protected $description = 'Remove default seed demo accounts from production';

    public function handle(): int
    {
        $emails = config('platform.seed_user_emails', []);

        if ($emails === []) {
            $this->info('No seed user emails configured.');

            return self::SUCCESS;
        }

        $users = User::query()->whereIn('email', $emails)->get();

        if ($users->isEmpty()) {
            $this->info('No seed accounts found.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm('Delete ' . $users->count() . ' seed account(s)?')) {
            $this->comment('Cancelled.');

            return self::SUCCESS;
        }

        foreach ($users as $user) {
            $user->delete();
            $this->line("Removed {$user->email}");
        }

        $this->info('Seed accounts removed.');

        return self::SUCCESS;
    }
}
