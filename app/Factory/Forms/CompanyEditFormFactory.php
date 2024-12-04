<?php

namespace App\Factory\Forms;

use App\Model\ZemeModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class CompanyEditFormFactory
{
    private FormFactory $formFactory;
    private ZemeModel $zemeModel;

    /**
     * @param FormFactory $formFactory
     * @param ZemeModel $zemeModel
     */
    public function __construct(FormFactory $formFactory, ZemeModel $zemeModel)
    {
        $this->formFactory = $formFactory;
        $this->zemeModel = $zemeModel;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('id');
        $new = $form->addContainer('new');
        $new->addText('nazev', 'Název:', null, 250)
            ->addRule(FormAlias::Filled);
        $new->addText('ico', 'IČO:', null, 20)
            ->addRule(FormAlias::Filled)
            ->addRule(FormAlias::Float);
        $new->addText('dic', 'DIČ:', null, 20);
        $new->addText('ulice', 'Ulice:', null, 100)
            ->addRule(FormAlias::Filled);
        $new->addText('obec', 'Obec:', null, 100)
            ->addRule(FormAlias::Filled);
        $new->addText('psc', 'PSČ:', null, 15)
            ->addRule(FormAlias::Filled);
        $new->addSelect('zeme', 'Stát:', $this->zemeModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addRule(FormAlias::Filled);
        $new->addText('cislo_uctu', 'Číslo účtu:', null, 50);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

}
