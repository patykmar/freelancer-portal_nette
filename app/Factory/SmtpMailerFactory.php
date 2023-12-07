<?php

namespace App\Factory;

use Nette\Mail\SmtpMailer;

class SmtpMailerFactory
{
    private array $config;
    private string $emailReceiver;

    /**
     * @param $config []<string>
     * @param string $emailReceiver
     */
    public function __construct(array $config, string $emailReceiver)
    {
        $this->config = $config;
        $this->emailReceiver = $emailReceiver;
    }

    public function create(): SmtpMailer
    {
        return new SmtpMailer($this->config);
    }

    public function getEmailReceiver(): string
    {
        return $this->emailReceiver;
    }
}
