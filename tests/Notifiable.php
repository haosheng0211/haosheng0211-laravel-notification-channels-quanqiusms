<?php

namespace NotificationChannels\QuanQiuSMS\Test;

class Notifiable
{
    use \Illuminate\Notifications\Notifiable;

    public function routeNotificationForQuanQiuSMS(): string
    {
        return '+8860900123456';
    }
}
