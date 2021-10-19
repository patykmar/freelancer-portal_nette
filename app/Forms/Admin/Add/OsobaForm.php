<?php

namespace App\Form\Admin\Add;

/**
 * Description of OsobaForm
 *
 * @author Martin Patyk
 */

use App\Model\FirmaModel;
use App\Model\FormatDatumModel;
use App\Model\TimeZoneModel;
use App\Model\TypOsobyModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class OsobaForm extends UIForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        $this->addText('jmeno', 'Jméno:', null, 100)
            ->addRule(Form::FILLED);
        $this->addText('prijmeni', 'Příjmení:', null, 100)
            ->addRule(Form::FILLED);
        $this->addText('email', 'E-mail:', null, 150)
            ->addRule(Form::FILLED)
            ->addRule(Form::EMAIL);
        $this->addSelect('firma', 'Firma:', FirmaModel::fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(' - - - ');
        $this->addSelect('typ_osoby', 'Typ osoby:', TypOsobyModel::fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(' - - - ');
        $this->addSelect('time_zone', 'Časová zona:', TimeZoneModel::fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(' - - - ');
        $this->addSelect('format_datum', 'Formád datumu:', FormatDatumModel::fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(' - - - ');
        $this->addCheckbox('je_admin', 'Jde o admina?');
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}