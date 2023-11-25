<?php

namespace App\Form\Admin\Edit;

/**
 * Description of FirmaForm
 *
 * @author Martin Patyk
 */

use App\Model\ZemeModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class FirmaForm extends UIForm
{
    public function __construct(ZemeModel $zemeModel, IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('nazev', 'Název:', null, 250)
            ->addRule(Form::FILLED);
        $new->addText('ico', 'IČO:', null, 20)
            ->addRule(Form::FILLED)
            ->addRule(Form::FLOAT);
        $new->addText('dic', 'DIČ:', null, 20);
        $new->addText('ulice', 'Ulice:', null, 100)
            ->addRule(Form::FILLED);
        $new->addText('obec', 'Obec:', null, 100)
            ->addRule(Form::FILLED);
        $new->addText('psc', 'PSČ:', null, 15)
            ->addRule(Form::FILLED);
        $new->addSelect('zeme', 'Stát:', $zemeModel->fetchPairs())
            ->setPrompt(' - - - ')
            ->addRule(Form::FILLED);
        $new->addText('cislo_uctu', 'Číslo účtu:', null, 50);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
