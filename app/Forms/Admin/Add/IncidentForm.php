<?php

namespace App\Form\Admin\Add;

/**
 * Description of IncidentForm
 *
 * @author Martin Patyk
 */

use App\Model\CiModel;
use App\Model\OsobaModel;
use App\Model\OvlivneniModel;
use App\Model\PrioritaModel;
use App\Model\TypIncidentModel;
use App\Model\UkonModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class IncidentForm extends UIForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        $this->addSelect('osoba_vytvoril', 'Vytvořil:')
            ->addRule(Form::FILLED);
        $this->addSelect('typ_incident', 'Typ tiketu:')
            ->addRule(Form::FILLED);
        $this->addSelect('priorita', 'Priorita:')
            ->addRule(Form::FILLED);
        $this->addSelect('ovlivneni', 'Ovlivnění:')
            ->setPrompt(' - - - ');
        $this->addSelect('ci', 'Produkt:', CiModel::fetchPairs())
            ->addRule(Form::FILLED);
        $this->addSelect('ukon', 'Služba:', UkonModel::fetchPairs())
            ->setPrompt(' - - - ');
        $this->addText('maly_popis', 'Malý popis:', null, 100)
            ->addRule(Form::FILLED);
        $this->addTextArea('obsah', 'Popis požadavku')
            ->addRule(Form::FILLED);
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}