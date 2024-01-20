<?php

namespace App\Forms\Admin\Add;

/**
 * Description of TypIncidentForm
 *
 * @author Martin Patyk
 */

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class TypIncidentForm extends UIForm
{
    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addText('nazev', 'Název:', null, 100)
            ->addRule(Form::FILLED);
        $this->addText('zkratka', 'Zkratka:', null, 10)
            ->addRule(Form::FILLED);
        $this->addText('koeficient_cena', 'Koeficient cena:', null, 10)
            ->setType('number')
            ->addRule(Form::FLOAT);
        $this->addText('koeficient_cas', 'Koeficient čas:', null, 10)
            ->setType('number')
            ->addRule(Form::FLOAT);
        $this->addSelect('typ_incident', 'Typ incidentu - rodič:')
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
