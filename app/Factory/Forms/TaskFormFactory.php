<?php

namespace App\Factory\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class TaskFormFactory
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
        $form->addText('cena', 'Cena:', null, 13)
            ->setType('number')
            ->setRequired()
            ->addRule(FormAlias::Float);
        $form->addText('cas_realizace', 'Čas realizace (Sec):', null, 255)
            ->setType('number')
            ->addRule(FormAlias::Filled);
        $form->addText('cas_reakce', 'Čas reakce (Sec):', null, 255)
            ->setType('number')
            ->addRule(FormAlias::Filled);
        $form->addTextArea('popis', 'Popis:')
            ->addRule(FormAlias::Filled);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }
}
