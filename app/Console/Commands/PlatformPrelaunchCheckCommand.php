<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PlatformPrelaunchCheckCommand extends Command
{
    protected $signature = 'platform:prelaunch-check';

    protected $description = 'Check production readiness before commercial launch';

    public function handle(): int
    {
        $issues = 0;

        foreach (config('platform.seed_user_emails', []) as $email) {
            if (User::where('email', $email)->exists()) {
                $this->warn("Seed account still exists: {$email}");
                $issues++;
            }
        }

        if (config('app.debug')) {
            $this->warn('APP_DEBUG is enabled.');
            $issues++;
        }

        if (! config('mail.mailers.smtp.username') || ! config('mail.from.address')) {
            $this->warn('MAIL_USERNAME or MAIL_FROM_ADDRESS is not configured.');
            $issues++;
        }

        if (! config('platform.mail_notifications')) {
            $this->warn('NOTIFY_VIA_MAIL is disabled — email notifications are off.');
            $issues++;
        }

        if (! config('payments.demo_mode')) {
            $iban = (string) config('payments.manual.iban', '');

            if ($iban === '' || str_contains($iban, '0000 0000')) {
                $this->warn('PAYMENT_IBAN is missing or still using a placeholder.');
                $issues++;
            }
        }

        $ga4 = (string) config('seo.google_analytics_id', '');

        if ($ga4 === '' || str_contains($ga4, 'XXXX')) {
            $this->warn('GA4_MEASUREMENT_ID is missing or still using a placeholder.');
            $issues++;
        }

        if (config('payments.driver') === 'stripe' && ! config('payments.demo_mode')) {
            $stripeKey = (string) config('payments.stripe.secret_key', '');

            if ($stripeKey === '' || str_contains($stripeKey, 'xxxxx')) {
                $this->warn('PAYMENT_DRIVER=stripe but STRIPE_SECRET_KEY is not configured.');
                $issues++;
            }
        }

        if (config('payments.demo_mode')) {
            $this->line('<comment>INFO</comment> PAYMENT_DEMO_MODE is enabled (test purchases allowed).');
        }

        if ($issues === 0) {
            $this->info('Pre-launch checks passed. Platform looks ready for commercial launch.');

            return self::SUCCESS;
        }

        $this->error("Pre-launch check found {$issues} issue(s).");

        return self::FAILURE;
    }
}
