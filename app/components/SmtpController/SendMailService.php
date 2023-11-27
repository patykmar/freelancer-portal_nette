<?php

namespace App\Components;

use App\Factory\SmtpMailerFactory;
use Nette\Application\UI\Control;
use Nette\ComponentModel\IContainer;
use Nette\Latte\Engine;
use Nette\Mail\Message;
use Nette\NotImplementedException;
use Nette\Utils\ArrayHash;

class SendMailService extends Control
{
    private const REGISTER_HELPER_LOADER = '\Nette\Templating\Helpers::loader';
    private $smtpMailer;
    private $latteEngine;
    private $emailAddressReceiver;

    public function __construct(
        SmtpMailerFactory $smtpMailerFactory,
        Engine            $engine,
        IContainer        $parent = null,
        string            $name = null
    )
    {
        parent::__construct($parent, $name);
        $this->smtpMailer = $smtpMailerFactory->create();
        $this->emailAddressReceiver = $smtpMailerFactory->getEmailReceiver();
        $this->latteEngine = $engine;
    }

    /**
     * Odesle zakaznikovi email s uzivatelskym jmenem a heslem
     * @param ArrayHash $value obsahuje pole kde je vyplnen komu poslat email, idecko a heslo noveho klienta
     */
    public function novaOsoba(ArrayHash $value)
    {
        /*
         * Vytvorim sablonu a naplnim ji daty
         */
        $template = $this->createTemplate()
            ->setFile(__DIR__ . '/novaOsoba.latte')
            ->registerFilter($this->latteEngine)
            ->registerHelperLoader(self::REGISTER_HELPER_LOADER);
        $template->items = $value;
        /*
         * Vytvorim emailovou zpravu, kterou pak odeslu
         */
        $mail = new Message;
        $mail->setFrom($this->emailAddressReceiver);
        $mail->addTo($value->email)
            ->setSubject('Portal.patyk.cz nový uživatelský účet')
            ->setHtmlBody($template);

        $this->smtpMailer->send($mail);
//  $mailer = new \Nette\Mail\SendmailMailer;
//  $mailer->send($mail);
    }


    /**
     * Odesle zakaznikovi email s novym heslem
     */
//    public function vygenerujNoveHeslo(DibiRow $value)
//    {
    /*
     * Vytvorim sablonu a naplnim ji daty
     */
//        $template = $this->createTemplate()
//            ->setFile(__DIR__ . '/noveHeslo.latte')
//            ->registerFilter($this->latteEngine)
//            ->registerHelperLoader(self::REGISTER_HELPER_LOADER);
//        $template->items = $value;
//
//        // Vytvorim emailovou zpravu, kterou pak odeslu
//        $mail = new Message;
//        $mail->setFrom($this->emailAddress);
//        $mail->addTo($value->email)
//            ->setSubject('Portal.patyk.cz nove heslo')
//            ->setHtmlBody($template);
//        $this->smtpMailer->send($mail);
    #$mailer = new \Nette\Mail\SendmailMailer;
    #$mailer->send($mail);
//    }

    /**
     * Odesle zakaznikovi email s pozadavkem na zpetnou vazbu.
     */
    public function odesliPozadavekNaZpetnoutVazbu(ArrayHash $value)
    {
        throw new NotImplementedException("Method odesliPozadavekNaZpetnoutVazbu is not implemented");
//        // Vytvorim sablonu a naplnim ji daty
//        $template = $this->createTemplate()
//            ->setFile(__DIR__ . '/pozadavekNaZpetnouVazbu.latte')
//            ->registerFilter($this->latteEngine)
//            ->registerHelperLoader(self::REGISTER_HELPER_LOADER);
//        $template->items = $value;
//        // Vytvorim emailovou zpravu, kterou pak odeslu
//        $mail = new Message();
//        $mail->setFrom($this->emailAddressReceiver);
//        $mail->addTo($value['email'])
//            ->setSubject('Portal.patyk.cz - Tiket ' . $value['idTxt'] . ' byl vyřešen')
//            ->setHtmlBody($template);
//        // echo $template;
//        $this->smtpMailer->send($mail);
//
//        unset($template, $mail);
//        // $mailer = new \Nette\Mail\SendmailMailer;
//        // $mailer->send($mail);
    }
}