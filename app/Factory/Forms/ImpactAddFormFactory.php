<?php

namespace App\Factory\Forms;

use Nette\Application\UI\Form;

class ImpactAddFormFactory
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
        $form->addText('nazev', 'Název:', null, 255)
            ->addRule(Form::FILLED);
        $form->addInteger('koeficient_cena', 'Koeficient cena:')
            ->addRule(Form::FLOAT);
        $form->addInteger('koeficient_cas', 'Koeficient čas:')
            ->addRule(Form::FLOAT);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

}
