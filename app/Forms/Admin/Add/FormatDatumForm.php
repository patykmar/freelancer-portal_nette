<?php

namespace App\Forms\Admin\Add;

/**
 * Description of FormatDatumForm
 *
 * @author Martin Patyk
 */

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class FormatDatumForm extends UIForm
{
    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addText('nazev', 'Název:', null, 100)
            ->addRule(Form::FILLED);
        $this->addText('format', 'Formát datumu a času:', null, 10)
            ->addRule(Form::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}