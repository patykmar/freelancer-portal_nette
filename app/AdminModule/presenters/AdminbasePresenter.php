<?php

/**
 * Description of AdminbasePresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule;

use App\BasePresenter;
use Nette\Environment;
use Nette\Http\UserStorage;
use Nette\Security\Identity;

abstract class AdminbasePresenter extends BasePresenter
{

    /**
     * (non-phpDoc)
     * @see Nette\Application\Presenter#startup()
     */

    /** @var int identifikator prave prihlaseneho uzivatele */
    protected $userId;

    /** @var Identity identita prihlaseneho uzivatele */
    protected $identity;

    protected function startup()
    {
        parent::startup();
        $user = Environment::getUser();
        if (!$user->isLoggedIn()) {
            if ($user->getLogoutReason() === UserStorage::INACTIVITY) {
                $this->flashMessage('Byl jste odhlašen z důvodu nečinnosti');
            }
            $this->redirect(':Front:Sign:in');
        }

        $this->userId = $this->getUser()->getId();
        $this->identity = $this->getUser()->getIdentity();

        //	pokud nejsi admin, nemas tu co pohledavat
        if (!$this->identity->data['je_admin']) {
            $this->redirect(':Klient:Homepage:');
        }
    }

    public function __construct()
    {
        parent::__construct();
        $this->cssFiles->addFile('vzhled.css');
    }

    public function createComponentAdminMainMenu($name)
    {
        $nav = new \Navigation\Navigation($this, $name);
        $nav->setupHomepage('Administrace', $this->link(':Admin:Homepage:'));
        $nav->add('Klient', $this->link(':Klient:Homepage:'));

        $inc = $nav->add('Tikety', $this->link('Tickets:'));
        $inc->add('Typ Incident', $this->link('TypIncident:'));
        $inc->add('Incident stav', $this->link('IncidentStav:'));
        $inc->add('Zpusob uzavreni', $this->link('ZpusobUzavreni:'));
        $inc->add('Ukon', $this->link('Ukon:'));
        $inc->add('Ovlivnění', $this->link('Ovlivneni:'));
        $inc->add('Vytvořit tiket', $this->link('Tickets:add'));

//        $ord = $nav->add('Objednávky', NULL);
//        $ord->add('Aktivní', $this->link('Inc:objednavkyAktivni'));
//        $ord->add('Vyřešené', $this->link('Inc:objednavkyVyresene'));

        $change = $nav->add('Change', null);
        $change->add('Zpusob uzavreni', $this->link('ZpusobUzavreni:'));
        $change->add('Change Stav', $this->link('ChangeStav:'));
        $change->add('Typ Change', $this->link('TypChange:'));

        $faktury = $nav->add('Vyuctování', $this->link('Vyuctovani:'));
//        $faktury->add('Nezaúčtovaná prace', $this->link('Vyuctovani:NezauctovanaPrace'));
        $faktury->add('Faktury', $this->link('Faktura:'));

        $osoba = $nav->add('Osoby', $this->link('Osoba:'));
        $osoba->add('Typ Osoby', $this->link('TypOsoby:'));
        $osoba->add('Time Zone', $this->link('TimeZone:'));
        $osoba->add('Format Datum', $this->link('FormatDatum:'));
        $osoba->add('Fronta', $this->link('Fronta:'));
        $osoba->add('Fronta-Osoba', $this->link('FrontaOsoba:'));

        $firma = $nav->add('Evidované firmy', $this->link('Firma:'));
        $firma->add('Zeme', $this->link('Zeme:'));

        $tarif = $nav->add('Tarif', $this->link('Tarif:'));
        $tarif->add('SLA', $this->link('Sla:'));
        $tarif->add('Priorita', $this->link('Priorita:'));

        $ci = $nav->add('CI databaze', $this->link('Ci:'));
        $ci->add('Stav ci', $this->link('StavCi:'));
        $ci->add('Web alerts - CI', $this->link('WebAlertsCi:'));

        $nav->add('Odhlasit', $this->link(':Front:Sign:out'));
    }
}