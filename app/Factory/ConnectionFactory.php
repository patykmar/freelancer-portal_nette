<?php

namespace App\Factory;

use greeny\MailLibrary\Connection;

class ConnectionFactory
{
    private ImapDriverFactory $imapDrive;

    public function __construct(ImapDriverFactory $imapDriverFactory)
    {
        $this->imapDrive = $imapDriverFactory;
    }

    public function create(): Connection
    {
        return new Connection($this->imapDrive->create());
    }

}
