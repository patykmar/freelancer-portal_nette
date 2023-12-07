<?php

namespace App\Forms\Admin\Add;

/**
 * Formular pro tvorbu nove faktury
 *
 * @author Martin Patyk
 */

use App\Model\DphModel;
use App\Model\FormaUhradyModel;
use App\Model\JednotkaModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form as NetteForm;

class FakturaForm extends UIForm
{
    private FormaUhradyModel $formaUhradyModel;
    private JednotkaModel $jednotkaModel;
    private DphModel $dphModel;

    public function __construct(
        FormaUhradyModel $formaUhradyModel,
        JednotkaModel    $jednotkaModel,
        DphModel         $dphModel,
        IContainer       $parent = null,
                         $name = null
    )
    {
        parent::__construct($parent, $name);

        $this->formaUhradyModel = $formaUhradyModel;
        $this->jednotkaModel = $jednotkaModel;
        $this->dphModel = $dphModel;

        $this->addHidden('vytvoril');
        $this->addText('dodavatel_nazev', 'Dodavatel nazev:', null, 250)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_ico', 'Dodavatel ICO:', null, 20)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_dic', 'dodavatel DIC:', null, 20);
        $this->addText('dodavatel_ulice', 'Dodavatel ulice:', null, 100);
        $this->addText('dodavatel_obec', 'Dodavatel obec:', null, 100)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_psc', 'Dodavatel PSC:', null, 15)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_zeme', 'Dodavatel zeme:', null, 100)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_cislo_uctu', 'Dodavatel cislo uctu:', null, 50)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_iban', 'Dodavatel IBAN:', null, 100);
        $this->addText('odberatel_nazev', 'Odberatel nazev:', null, 250)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_ico', 'Odberatel ICO:', null, 20)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_dic', 'Odberatel DIC:', null, 20);
        $this->addText('odberatel_ulice', 'Odberatel ulice:', null, 100);
        $this->addText('odberatel_obec', 'Odberatel obec:', null, 100)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_psc', 'Odberatel PSC:', null, 15)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_zeme', 'Odberatel zeme:', null, 100)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_cislo_uctu', 'Odberatel cislo uctu:', null, 50)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_iban', 'Odberatel IBAN:', null, 100);
        $this->addText('splatnost', 'Splatnost:', null, 5)
            ->setType('number')
            ->addRule(NetteForm::INTEGER)
            ->addRule(NetteForm::RANGE, null, array(1, 999))
            ->addRule(NetteForm::FILLED);
        $this->addText('datum_vystaveni', 'Datum vystaveni:');
        $this->addText('datum_splatnosti', 'Datum splatnosti:');
        $this->addText('datum_zaplaceni', 'Datum zaplaceni:');
        $this->addText('vs', 'Variabilni symbol:', null, 10);
        $this->addText('ks', 'Konstantni symbol:', null, 10);
        $this->addSelect('forma_uhrady', 'Forma uhrady:', $this->formaUhradyModel->fetchPairs())
            ->addRule(NetteForm::FILLED);

        $polozky = $this->addContainer('polozky');
        for ($i = 0; $i < 10; $i++) {
            $item = $polozky->addContainer('polozka_' . $i);
            $item->addText('nazev', 'Nazev:', null, 250);
            $item->addText('pocet_polozek', 'Pocet polozek:', null, 5)
                ->setType('number')
                ->addRule(NetteForm::INTEGER)
                ->addRule(NetteForm::RANGE, null, array(0, 999));
            $item->addSelect('jednotka', 'Jednotka:', $this->jednotkaModel->fetchPairs())
                ->setPrompt(' - - - ');
            $item->addSelect('dph', 'DPH:', $this->dphModel->fetchPairs())
                ->setPrompt(' - - - ');
            $item->addSelect('cssclass', 'css:')
                ->setItems(array(
                    'faktura-polozka',
                    'faktura-nadpis'
                ), false);
            $item->addText('cena', 'Cena:', null, 10)
                ->addRule(NetteForm::FLOAT);
            unset($item);
        }
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }

}
