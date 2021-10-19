<?php

namespace App\Form\Admin\Add;

/**
 * Description of TarifForm
 *
 * @author Martin Patyk
 */

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class TarifForm extends UIForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        $this->addText('nazev', 'Název:', null, 100)
            ->addRule(Form::FILLED);
        $this->addText('cena', 'Cena:', null, 13)
            ->addRule(Form::FILLED)
            ->addRule(Form::FLOAT);
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}