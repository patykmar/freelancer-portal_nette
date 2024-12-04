<?php

namespace App\Factory\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

/**
 * @deprecated
*/
//TODO: Change name to ForeignKeyFormFactory
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

    public function create(int $maxLength = 100): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('id');
        $form->addText('nazev', 'Název:', null, $maxLength)
            ->addRule(FormAlias::Filled);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }
}
