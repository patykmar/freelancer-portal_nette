<?php

namespace App\Forms\Admin\Edit;

use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class ForeignKeyEditForm extends Form
{
    public function __construct()
    {
        parent::__construct();
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('nazev', 'Název:', null, 100)
            ->addRule(FormAlias::FILLED, 'Prosím vyplňte: %label');
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
