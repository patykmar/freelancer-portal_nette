<?php

namespace App\Factory\Forms;

use Nette\Application\UI\Form;

class ImpactEditFormFactory
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
        $new = $this->addContainer('new');
        $new->addText('nazev', 'Název:', null, 255)
            ->addRule(Form::FILLED);
        $new->addText('koeficient_cena', 'Koeficient cena:', null, 10)
            ->setType('number')
            ->addRule(Form::FLOAT);
        $new->addText('koeficient_cas', 'Koeficient čas:', null, 10)
            ->setType('number')
            ->addRule(Form::FLOAT);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }
}
