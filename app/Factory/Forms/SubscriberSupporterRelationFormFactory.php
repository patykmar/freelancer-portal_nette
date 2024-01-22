<?php

namespace App\Factory\Forms;

use App\Model\FirmaModel;
use Nette\Application\UI\Form;

class SubscriberSupporterRelationFormFactory
{
    private FormFactory $formFactory;
    private FirmaModel $firmaModel;

    public function __construct(
        FormFactory $formFactory,
        FirmaModel  $firmaModel
    )
    {
        $this->formFactory = $formFactory;
        $this->firmaModel = $firmaModel;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('id');
        $form->addSelect('dodvatel', 'Dodavatel:', $this->firmaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $form->addSelect('odberatel', 'Odberatel:', $this->firmaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

}
