<?php

namespace NotificationChannels\QuanQiuSMS;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use NotificationChannels\QuanQiuSMS\Exceptions\CouldNotSendNotification;

class QuanQiuSMSChannel
{
    protected $client;

    public function __construct(QuanQiuSMS $client)
    {
        $this->client = $client;
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('quanqiusms')) {
            throw CouldNotSendNotification::missingTo();
        }

        $phone = $this->parsePhoneNumber($to);

        $to = $this->formatGlobalPhoneNumber($phone);

        if (! method_exists($notification, 'toQuanQiuSMS')) {
            throw new \InvalidArgumentException('Notification does not have a toQuanQiuSMS method');
        }

        $message = $notification->toQuanQiuSMS($notifiable);

        if (is_string($message)) {
            $message = new QuanQiuSMSMessage($message);
        }

        try {
            $response = $this->client->sendSMS($to, $message->content);

            $response = json_decode($response, true);

            if ((int) $response['code'] != 0) {
                throw CouldNotSendNotification::serviceRespondedWithAnError($response['result']);
            }
        } catch (GuzzleException $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($exception);
        }
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function parsePhoneNumber(string $to): PhoneNumber
    {
        try {
            return PhoneNumberUtil::getInstance()->parse($to);
        } catch (NumberParseException $exception) {
            throw CouldNotSendNotification::invalidPhoneNumber();
        }
    }

    public function formatGlobalPhoneNumber(PhoneNumber $phone): string
    {
        $number = PhoneNumberUtil::getInstance()->format($phone, PhoneNumberFormat::E164);

        return preg_replace('/\D/', '', $number);
    }
}
