<?php

/**
 * Description of ActionsPresenter
 *
 * @author Martin Patyk
 */

namespace App\CronModule\Presenters;

use App\Factory\ConnectionFactory;
use App\Model\IncidentModel;
use App\Model\OdCiModel;
use greeny\MailLibrary\ConnectionException;
use greeny\MailLibrary\ContactList;
use greeny\MailLibrary\DriverException;
use greeny\MailLibrary\InvalidFilterValueException;
use greeny\MailLibrary\Mail;
use Nette\Application\AbortException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Nette\Utils\Strings;

class ActionsPresenter extends CronBasePresenter
{
    private OdCiModel $ciModel;
    private ConnectionFactory $connectionFactory;
    private IncidentModel $incidentModel;

    public function __construct(OdCiModel $ciModel, ConnectionFactory $connectionFactory, IncidentModel $incidentModel)
    {
        parent::__construct();
        $this->ciModel = $ciModel;
        $this->connectionFactory = $connectionFactory;
        $this->incidentModel = $incidentModel;
    }

    /**
     * Funkce, ktera z emailu vytvori tiket v IS portal.patyk.cz
     * @throws AbortException|DriverException
     */
    public function actionWebalerts()
    {
        try {
            $connection = $this->connectionFactory->create();
            $inbox = $connection->getMailbox('INBOX');

            //vyberu jen neprectene maily
            $mails = $inbox->getMails()
                ->limit(10)
                ->where(Mail::SEEN, false);


            /** @var Mail $mail */
            foreach ($mails->fetchAll() as $mail) {
                //vrati celou hlavicku mailu
                #dump($item->getHeaders());
                //nactu si potrebne udaje
                /** @var ContactList $contact */
                $contact = $mail->getHeader('from')
                    ->getContacts();
                $from = Strings::replace(Strings::lower($contact[0]), array('/</' => '', '/>/' => ''));
                $subject = $mail->getHeader('subject');

                //na zaklade odesilatele si zjistim idecko CIcka
                $ci = $this->ciModel->fetchCiId($from);
                //novy arrayhash s hodnotami pro vytvoreni noveho tiketu
                $newTicketValues = new ArrayHash;
                $newTicketValues->offsetSet('datum_vytvoreni', new DateTime);
                $newTicketValues->offsetSet('fronta_osoba', 4); // ID 5 ma uzivatel system
                $newTicketValues->offsetSet('incident_stav', 1); // ID 1 - Otevřen
                $newTicketValues->offsetSet('priorita', 3); // ID 3 - Normalni
                $newTicketValues->offsetSet('typ_incident', 2); // ID 2 - incident
                $newTicketValues->offsetSet('osoba_vytvoril', 5); // ID 5 - CD
                $newTicketValues->offsetSet('ukon', 1); // Web alarm
                $newTicketValues->offsetSet('ovlivneni', 2); // Normalni

                if ($ci) {
                    //pokud existuje zaznam v databazi vytvorim tiket svazany k CI
                    $newTicketValues->offsetSet('ci', $ci);
                    $newTicketValues->offsetSet('maly_popis', $subject);
                    $newTicketValues->offsetSet('obsah', $subject . chr(10) . chr(10) . $mail->getBody());    // telo mailu
                } else {
                    //Pokud neexistuje par odesilatel - CI je potreba jej vytvorit
                    $newTicketValues->offsetSet('ci', 3);    // ID 3 - portal.patyk.cz
                    $newTicketValues->offsetSet('maly_popis', '!!! Nebylo mozne priradit k odesilateli CI');
                    $newTicketValues->offsetSet('obsah', $subject . chr(10) . chr(10) . $mail->getBody());    // telo mailu
                }

                //zapisu data do databaze

                $this->incidentModel->insert($newTicketValues);

                // nastavim mail jako precteny
                $mail->setFlags(array(Mail::FLAG_SEEN => true));
                //odeslu operace na server
                $connection->flush();
            }
        } catch (InvalidFilterValueException|ConnectionException $exc) {
            Debugger::log($exc->getMessage(), Debugger::ERROR);
            $this->terminate();
        }

        //hotovo dvacet zaviram kram :)
        $this->terminate();
    }
}
