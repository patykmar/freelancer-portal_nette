<?php

namespace App\Factory\Forms;

use App\Model\TypIncidentModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class TypIncidentFormFactory
{
    private FormFactory $formFactory;
    private TypIncidentModel $typIncidentModel;

    /**
     * @param FormFactory $formFactory
     * @param TypIncidentModel $typIncidentModel
     */
    public function __construct(
        FormFactory      $formFactory,
        TypIncidentModel $typIncidentModel
    )
    {
        $this->formFactory = $formFactory;
        $this->typIncidentModel = $typIncidentModel;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('id');
        $form->addText('nazev', 'Název:', null, 100)
            ->addRule(FormAlias::FILLED);
        $form->addText('zkratka', 'Zkratka:', null, 10)
            ->addRule(FormAlias::FILLED);
        $form->addText('koeficient_cena', 'Koeficient cena:', null, 10)
            ->setType('number')
            ->setRequired()
            ->addRule(FormAlias::FLOAT);
        $form->addText('koeficient_cas', 'Koeficient čas:', null, 10)
            ->setType('number')
            ->setRequired()
            ->addRule(FormAlias::FLOAT);
        $form->addSelect('typ_incident', 'Typ incidentu - rodič:', $this->typIncidentModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }
}
