<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/** php artisan portfolio:install [--fresh]  → app key + tables + demo data + storage link, in one go. */
class PortfolioInstall extends Command
{
    protected $signature = 'portfolio:install {--fresh : Drop every table first (DANGER: erases data)}';
    protected $description = 'One-shot setup: app key, migrations, demo data (seeders) and the public storage link';

    public function handle(): int
    {
        if (!config('app.key')) $this->call('key:generate', ['--force' => true]);
        $this->call($this->option('fresh') ? 'migrate:fresh' : 'migrate', ['--force' => true]);
        $this->call('db:seed', ['--force' => true]);
        if (!is_link(public_path('storage'))) $this->call('storage:link');

        $this->newLine();
        $this->info('✔ Portfolio backend is ready.');
        $this->line('  Admin login : <comment>' . env('ADMIN_EMAIL', 'admin@example.com') . '</comment> / <comment>' . env('ADMIN_PASSWORD', 'password123') . '</comment>');
        $this->line('  Start server: <comment>php artisan serve</comment>  →  http://localhost:8000/admin');
        return self::SUCCESS;
    }
}
