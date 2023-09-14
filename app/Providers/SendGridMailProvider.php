<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\TransportManager;

class SendGridMailProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->resolving('mail.manager', function ($manager, $app) {
            $manager->extend('sendgrid', function () use ($app) {
                $config = $app['config']->get('mail.sendgrid', []);
    
                return new \Swift_SmtpTransport($config['host'], $config['port']);
            });
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
