<?php

namespace App\Factory\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class TariffEditFormFactory
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
        $form->addHidden('id');
        $new = $form->addContainer('new');
        $new->addText('nazev', 'Název:', null, 100)
            ->addRule(FormAlias::Filled);
        $new->addText('cena', 'Cena:', null, 13)
            ->addRule(FormAlias::Filled)
            ->addRule(FormAlias::Float);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

}
