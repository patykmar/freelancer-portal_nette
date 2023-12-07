<?php

namespace App\Factory;

use greeny\MailLibrary\Drivers\ImapDriver;

class ImapDriverFactory
{
    private string $username;
    private string $password;
    private string $mailHost;
    private int $port;
    private bool $useSsl;

    public function __construct(string $username, string $password, string $mailHost, int $port, bool $useSsl)
    {
        $this->username = $username;
        $this->password = $password;
        $this->mailHost = $mailHost;
        $this->port = $port;
        $this->useSsl = $useSsl;
    }

    public function create(): ImapDriver
    {
        return new ImapDriver($this->username, $this->password, $this->mailHost, $this->port, $this->useSsl);
    }
}
