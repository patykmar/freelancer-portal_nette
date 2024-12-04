<?php

namespace App\Factory\Forms;

use App\Model\FirmaModel;
use App\Model\FormatDatumModel;
use App\Model\TimeZoneModel;
use App\Model\TypOsobyModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class PersonEditFormFactory
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
        $form->addHidden('id');
        $new = $form->addContainer('new');
        $new->addText('jmeno', 'Jméno:', null, 100)
            ->addRule(FormAlias::FILLED);
        $new->addText('prijmeni', 'Příjmení:', null, 100)
            ->addRule(FormAlias::FILLED);
        $new->addText('email', 'E-mail:', null, 150)
            ->addRule(FormAlias::FILLED)
            ->addRule(FormAlias::EMAIL);
        $new->addSelect('firma', 'Firma:', $this->firmaModel->fetchPairs())
            ->addRule(FormAlias::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $new->addSelect('typ_osoby', 'Typ osoby:', $this->typOsobyModel->fetchPairs())
            ->addRule(FormAlias::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $new->addSelect('time_zone', 'Časová zona:', $this->timeZoneModel->fetchPairs())
            ->addRule(FormAlias::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $new->addSelect('format_datum', 'Formád datumu:', $this->formatDatumModel->fetchPairs())
            ->addRule(FormAlias::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $new->addCheckbox('je_admin', 'Jde o admina?');
        //Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }


}
