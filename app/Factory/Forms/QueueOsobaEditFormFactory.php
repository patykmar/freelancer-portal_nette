<?php

namespace App\Factory\Forms;

use App\Model\FrontaModel;
use App\Model\OsobaModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class QueueOsobaEditFormFactory
{
    private FrontaModel $frontaModel;
    private OsobaModel $osobaModel;
    private FormFactory $formFactory;

    public function __construct(FrontaModel $frontaModel, OsobaModel $osobaModel, FormFactory $formFactory)
    {
        $this->frontaModel = $frontaModel;
        $this->osobaModel = $osobaModel;
        $this->formFactory = $formFactory;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('id');
        $new = $form->addContainer('new');
        $new->addSelect('fronta', 'Fronta:', $this->frontaModel->fetchPairs())
            ->addRule(FormAlias::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $new->addSelect('osoba', 'Osoba:', $this->osobaModel->fetchPairsSpecialistSystem())
            ->addRule(FormAlias::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }
}
