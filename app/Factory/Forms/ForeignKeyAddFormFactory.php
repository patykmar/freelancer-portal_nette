<?php

namespace App\Factory\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class ForeignKeyAddFormFactory
{
    private FormFactory $formFactory;

    /**
     * @param FormFactory $formFactory
     */
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addText('nazev', 'Název:', null, 100)
            ->addRule(FormAlias::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }
}
