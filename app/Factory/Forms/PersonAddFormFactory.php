<?php

namespace App\Factory\Forms;

use App\Model\FirmaModel;
use App\Model\FormatDatumModel;
use App\Model\TimeZoneModel;
use App\Model\TypOsobyModel;
use Nette\Application\UI\Form;

class PersonAddFormFactory
{
    private FormFactory $formFactory;
    private TypOsobyModel $typOsobyModel;
    private FirmaModel $firmaModel;
    private TimeZoneModel $timeZoneModel;
    private FormatDatumModel $formatDatumModel;

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
        $form->addText('jmeno', 'Jméno:', null, 100)
            ->addRule(Form::FILLED);
        $form->addText('prijmeni', 'Příjmení:', null, 100)
            ->addRule(Form::FILLED);
        $form->addText('email', 'E-mail:', null, 150)
            ->addRule(Form::FILLED)
            ->addRule(Form::EMAIL);
        $form->addSelect('firma', 'Firma:', $this->firmaModel->fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $form->addSelect('typ_osoby', 'Typ osoby:', $this->typOsobyModel->fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $form->addSelect('time_zone', 'Časová zona:', $this->timeZoneModel->fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $form->addSelect('format_datum', 'Formád datumu:', $this->formatDatumModel->fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $form->addCheckbox('je_admin', 'Jde o admina?');
        //Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

}
