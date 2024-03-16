<?php

namespace NotificationChannels\QuanQiuSMS;

class QuanQiuSMSMessage
{
    public $content;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public static function create(string $content = ''): self
    {
        return new static($content);
    }

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
