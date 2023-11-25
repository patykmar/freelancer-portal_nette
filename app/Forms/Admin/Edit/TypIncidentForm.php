<?php

namespace App\Form\Admin\Edit;

/**
 * Description of TypIncidentForm
 *
 * @author Martin Patyk
 */

use App\Model\TypIncidentModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class TypIncidentForm extends UIForm
{
    public function __construct(TypIncidentModel $typIncidentModel, IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('nazev', 'Název:', null, 100)
            ->addRule(Form::FILLED);
        $new->addText('zkratka', 'Zkratka:', null, 10)
            ->addRule(Form::FILLED);
        $new->addText('koeficient_cena', 'Koeficient cena:', null, 10)
            ->setType('number')
            ->addRule(Form::FLOAT);
        $new->addText('koeficient_cas', 'Koeficient čas:', null, 10)
            ->setType('number')
            ->addRule(Form::FLOAT);
        $new->addSelect('typ_incident', 'Typ incidentu - rodič:', $typIncidentModel->fetchPairsMain())
            ->setPrompt(' - - - ');
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
