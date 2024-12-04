<?php

namespace App\Factory\Forms;

use App\Model\ZemeModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as NetteForm;

class CompanyAddFormFactory
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
        $form->addText('nazev', 'Název:', null, 250)
            ->addRule(NetteForm::FILLED);
        $form->addText('ico', 'IČO:', null, 20)
            ->addRule(NetteForm::FILLED)
            ->addRule(NetteForm::FLOAT);
        $form->addText('dic', 'DIČ:', null, 20);
        $form->addText('ulice', 'Ulice:', null, 100)
            ->addRule(NetteForm::FILLED);
        $form->addText('obec', 'Obec:', null, 100)
            ->addRule(NetteForm::FILLED);
        $form->addText('psc', 'PSČ:', null, 15)
            ->addRule(NetteForm::FILLED);
        $form->addSelect('zeme', 'Stát:', $this->zemeModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addRule(NetteForm::FILLED);
        $form->addText('cislo_uctu', 'Číslo účtu:', null, 50);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;

    }

}
