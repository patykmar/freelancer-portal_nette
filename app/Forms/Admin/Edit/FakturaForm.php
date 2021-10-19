<?php

namespace App\Form\Admin\Edit;

/**
 * Description of FakturaForm
 *
 * @author Martin Patyk
 */

use App\Model\FormaUhradyModel;
use App\Model\OsobaModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Container;
use App\Model;
use Nette\Forms\Form;

class FakturaForm extends UIForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        $this->addHidden('id');
        /** @var Container */
        $new = $this->addContainer('new');
        $new->addText('dodavatel_nazev', 'Dodavatel nazev:', NULL, 250)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_ico', 'Dodavatel ICO:', NULL, 20)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_dic', 'dodavatel DIC:', NULL, 20);
        $new->addText('dodavatel_ulice', 'Dodavatel ulice:', NULL, 100);
        $new->addText('dodavatel_obec', 'Dodavatel obec:', NULL, 100)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_psc', 'Dodavatel PSC:', NULL, 15)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_zeme', 'Dodavatel zeme:', NULL, 100)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_cislo_uctu', 'Dodavatel cislo uctu:', NULL, 50)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_iban', 'Dodavatel IBAN:', NULL, 100);
        $new->addText('odberatel_nazev', 'Odberatel nazev:', NULL, 250)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_ico', 'Odberatel ICO:', NULL, 20)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_dic', 'Odberatel DIC:', NULL, 20);
        $new->addText('odberatel_ulice', 'Odberatel ulice:', NULL, 100);
        $new->addText('odberatel_obec', 'Odberatel obec:', NULL, 100)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_psc', 'Odberatel PSC:', NULL, 15)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_zeme', 'Odberatel zeme:', NULL, 100)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_cislo_uctu', 'Odberatel cislo uctu:', NULL, 50)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_iban', 'Odberatel IBAN:', NULL, 100);
        $new->addText('splatnost', 'Splatnost:', null, 5)
            ->setType('number')
            ->addRule(Form::INTEGER)
            ->addRule(Form::RANGE, NULL, array(1, 999))
            ->addRule(Form::FILLED);
        $new->addText('datum_vystaveni', 'Datum vystaveni:')
            ->addRule(Form::FILLED);
        $new->addText('datum_splatnosti', 'Datum splatnosti:')
            ->addRule(Form::FILLED);
        $new->addText('datum_zaplaceni', 'Datum zaplaceni:');
        $new->addText('vs', 'Variabilni symbol:', NULL, 10);
        $new->addText('ks', 'Konstantni symbol:', NULL, 10);
        $new->addSelect('vytvoril', 'Vytvoril:', OsobaModel::fetchPairs())
            ->addRule(Form::FILLED);
        $new->addSelect('forma_uhrady', 'Forma uhrady:', FormaUhradyModel::fetchPairs())
            ->addRule(Form::FILLED);
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}