<?php

namespace App\Factory\Forms;

use App\Model\CiModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class EmailLinkToCiFormFactory
{
    private FormFactory $formFactory;
    private CiModel $ciModel;

    /**
     * @param FormFactory $formFactory
     * @param CiModel $ciModel
     */
    public function __construct(
        FormFactory $formFactory,
        CiModel     $ciModel
    )
    {
        $this->formFactory = $formFactory;
        $this->ciModel = $ciModel;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('id');
        $form->addText('od', 'Odesilatel:', null, 150)
            ->addRule(FormAlias::Filled);
        $form->addSelect('ci', 'Produkt:', $this->ciModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addRule(FormAlias::Filled);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

}
