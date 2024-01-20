<?php

namespace App\Forms\Admin\Add;

use Nette\Application\UI\Form;
use Nette\Forms\Form as NetteForm;

class ForeignKeyAddForm extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->addText('nazev', 'Název:', null, 100)
            ->addRule(NetteForm::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
