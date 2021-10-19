<?php

namespace App\Form\Admin\Add;

/**
 * Description of TypIncidentForm
 *
 * @author Martin Patyk
 */

use App\Model\TypIncidentModel;
use Nette\Application\UI\Form as UIForm,
    Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class TypIncidentForm extends UIForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
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
        $this->addSelect('typ_incident', 'Typ incidentu - rodič:', TypIncidentModel::fetchPairsMain())
            ->setPrompt(' - - - ');
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}