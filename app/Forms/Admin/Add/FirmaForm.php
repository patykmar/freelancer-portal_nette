<?php

namespace App\Forms\Admin\Add;

use App\Model\ZemeModel;
use Nette\Application\UI\Form as UIForm;
use Nette\Forms\Form as NetteForm;
use Nette\ComponentModel\IContainer;

class FirmaForm extends UIForm
{
    public function __construct(ZemeModel $zemeModel, IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addText('nazev', 'Název:', null, 250)
            ->addRule(NetteForm::FILLED);
        $this->addText('ico', 'IČO:', null, 20)
            ->addRule(NetteForm::FILLED)
            ->addRule(NetteForm::FLOAT);
        $this->addText('dic', 'DIČ:', null, 20);
        $this->addText('ulice', 'Ulice:', null, 100)
            ->addRule(NetteForm::FILLED);
        $this->addText('obec', 'Obec:', null, 100)
            ->addRule(NetteForm::FILLED);
        $this->addText('psc', 'PSČ:', null, 15)
            ->addRule(NetteForm::FILLED);
        $this->addSelect('zeme', 'Stát:', $zemeModel->fetchPairs())
            ->setPrompt(' - - - ')
            ->addRule(NetteForm::FILLED);
        $this->addText('cislo_uctu', 'Číslo účtu:', null, 50);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
