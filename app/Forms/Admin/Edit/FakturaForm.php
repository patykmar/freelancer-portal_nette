<?php

namespace App\Forms\Admin\Edit;

/**
 * Description of FakturaForm
 *
 * @author Martin Patyk
 */

use App\Model\FormaUhradyModel;
use App\Model\OsobaModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class FakturaForm extends UIForm
{
    public function __construct(
        OsobaModel       $osobaModel,
        FormaUhradyModel $formaUhradyModel,
        IContainer       $parent = null,
        string           $name = null
    )
    {
        parent::__construct($parent, $name);
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('dodavatel_nazev', 'Dodavatel nazev:', null, 250)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_ico', 'Dodavatel ICO:', null, 20)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_dic', 'dodavatel DIC:', null, 20);
        $new->addText('dodavatel_ulice', 'Dodavatel ulice:', null, 100);
        $new->addText('dodavatel_obec', 'Dodavatel obec:', null, 100)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_psc', 'Dodavatel PSC:', null, 15)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_zeme', 'Dodavatel zeme:', null, 100)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_cislo_uctu', 'Dodavatel cislo uctu:', null, 50)
            ->addRule(Form::FILLED);
        $new->addText('dodavatel_iban', 'Dodavatel IBAN:', null, 100);
        $new->addText('odberatel_nazev', 'Odberatel nazev:', null, 250)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_ico', 'Odberatel ICO:', null, 20)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_dic', 'Odberatel DIC:', null, 20);
        $new->addText('odberatel_ulice', 'Odberatel ulice:', null, 100);
        $new->addText('odberatel_obec', 'Odberatel obec:', null, 100)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_psc', 'Odberatel PSC:', null, 15)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_zeme', 'Odberatel zeme:', null, 100)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_cislo_uctu', 'Odberatel cislo uctu:', null, 50)
            ->addRule(Form::FILLED);
        $new->addText('odberatel_iban', 'Odberatel IBAN:', null, 100);
        $new->addText('splatnost', 'Splatnost:', null, 5)
            ->setType('number')
            ->addRule(Form::INTEGER)
            ->addRule(Form::RANGE, null, array(1, 999))
            ->addRule(Form::FILLED);
        $new->addText('datum_vystaveni', 'Datum vystaveni:')
            ->addRule(Form::FILLED);
        $new->addText('datum_splatnosti', 'Datum splatnosti:')
            ->addRule(Form::FILLED);
        $new->addText('datum_zaplaceni', 'Datum zaplaceni:');
        $new->addText('vs', 'Variabilni symbol:', null, 10);
        $new->addText('ks', 'Konstantni symbol:', null, 10);
        $new->addSelect('vytvoril', 'Vytvoril:', $osobaModel->fetchPairs())
            ->addRule(Form::FILLED);
        $new->addSelect('forma_uhrady', 'Forma uhrady:', $formaUhradyModel->fetchPairs())
            ->addRule(Form::FILLED);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož')
            ->setHtmlAttribute('class', 'btn btn-success');
        return $this;
    }
}
