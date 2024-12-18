<?php

namespace App\Factory\Forms;

use Nette\Forms\Form;
use Nette\Forms\Form as FormAlias;

class ZpusobUzavreniFormFactory
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
        $form->addText('nazev', 'Název:', null, 255)
            ->addRule(FormAlias::Filled);
        $form->addText('koeficient_cena', 'Koeficient cena:', null, 13)
            ->setType('number')
            ->addRule(FormAlias::Float)
            ->setRequired();
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

}