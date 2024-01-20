<?php

namespace App\Forms\Admin\Add;

/**
 * Description of OvlivneniForm
 *
 * @author Martin Patyk
 */

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class OvlivneniForm extends UIForm
{
    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addText('nazev', 'Název:', null, 255)
            ->addRule(Form::FILLED);
        $this->addInteger('koeficient_cena', 'Koeficient cena:', null, 10)
            ->addRule(Form::FLOAT);
        $this->addInteger('koeficient_cas', 'Koeficient čas:', null, 10)
            ->addRule(Form::FLOAT);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}