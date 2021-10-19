<?php

namespace App\Form\Admin\Add;

/**
 * Formular pro tvorbu nove faktury
 *
 * @author Martin Patyk
 */

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use App\Model;
use Nette\Forms\Form as NetteForm;
use Nette\Forms\Container;

class FakturaForm extends UIForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        $this->addHidden('vytvoril');
        $this->addText('dodavatel_nazev', 'Dodavatel nazev:', NULL, 250)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_ico', 'Dodavatel ICO:', NULL, 20)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_dic', 'dodavatel DIC:', NULL, 20);
        $this->addText('dodavatel_ulice', 'Dodavatel ulice:', NULL, 100);
        $this->addText('dodavatel_obec', 'Dodavatel obec:', NULL, 100)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_psc', 'Dodavatel PSC:', NULL, 15)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_zeme', 'Dodavatel zeme:', NULL, 100)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_cislo_uctu', 'Dodavatel cislo uctu:', NULL, 50)
            ->addRule(NetteForm::FILLED);
        $this->addText('dodavatel_iban', 'Dodavatel IBAN:', NULL, 100);
        $this->addText('odberatel_nazev', 'Odberatel nazev:', NULL, 250)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_ico', 'Odberatel ICO:', NULL, 20)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_dic', 'Odberatel DIC:', NULL, 20);
        $this->addText('odberatel_ulice', 'Odberatel ulice:', NULL, 100);
        $this->addText('odberatel_obec', 'Odberatel obec:', NULL, 100)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_psc', 'Odberatel PSC:', NULL, 15)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_zeme', 'Odberatel zeme:', NULL, 100)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_cislo_uctu', 'Odberatel cislo uctu:', NULL, 50)
            ->addRule(NetteForm::FILLED);
        $this->addText('odberatel_iban', 'Odberatel IBAN:', NULL, 100);
        $this->addText('splatnost', 'Splatnost:', null, 5)
            ->setType('number')
            ->addRule(NetteForm::INTEGER)
            ->addRule(NetteForm::RANGE, NULL, array(1, 999))
            ->addRule(NetteForm::FILLED);
        $this->addText('datum_vystaveni', 'Datum vystaveni:');
        $this->addText('datum_splatnosti', 'Datum splatnosti:');
        $this->addText('datum_zaplaceni', 'Datum zaplaceni:');
        $this->addText('vs', 'Variabilni symbol:', NULL, 10);
        $this->addText('ks', 'Konstantni symbol:', NULL, 10);
        $this->addSelect('forma_uhrady', 'Forma uhrady:', Model\FormaUhradyModel::fetchPairs())
            ->addRule(NetteForm::FILLED);

        /** @var Container */
        $polozky = $this->addContainer('polozky');
        for ($i = 0; $i < 10; $i++) {
            $item = $polozky->addContainer('polozka_' . $i);
            $item->addText('nazev', 'Nazev:', NULL, 250);
            $item->addText('pocet_polozek', 'Pocet polozek:', NULL, 5)
                ->setType('number')
                ->addRule(NetteForm::INTEGER)
                ->addRule(NetteForm::RANGE, NULL, array(0, 999));
            $item->addSelect('jednotka', 'Jednotka:', Model\JednotkaModel::fetchPairs())
                ->setPrompt(' - - - ');
            $item->addSelect('dph', 'DPH:', Model\DphModel::fetchPairs())
                ->setPrompt(' - - - ');
            $item->addSelect('cssclass', 'css:')
                ->setItems(array(
                    'faktura-polozka',
                    'faktura-nadpis'
                ), FALSE);
            $item->addText('cena', 'Cena:', NULL, 10)
                ->addRule(NetteForm::FLOAT);
            unset($item);
        }
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}