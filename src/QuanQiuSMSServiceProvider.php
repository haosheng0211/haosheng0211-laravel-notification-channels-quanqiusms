<?php

namespace NotificationChannels\QuanQiuSMS;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class QuanQiuSMSServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->singleton(QuanQiuSMS::class, function ($app) {
            $username = $app['config']['services.quanqiusms.appkey'];
            $password = $app['config']['services.quanqiusms.secretkey'];

            if (empty($username) || empty($password)) {
                throw new \InvalidArgumentException('Missing QuanQiuSMS config in services');
            }

            return new QuanQiuSMS($username, $password);
        });

        Notification::resolved(function (ChannelManager $service) {
            $service->extend('quanqiusms', function ($app) {
                return new QuanQiuSMSChannel($app->make(QuanQiuSMS::class));
            });
        });
    }

    public function provides(): array
    {
        return [QuanQiuSMS::class];
    }
}
