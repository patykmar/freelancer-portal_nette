<?php

namespace App\Factory\Forms;

use App\Model\DphModel;
use App\Model\FakturaPolozkaCssModel;
use App\Model\JednotkaModel;
use Nette\Application\UI\Form;

class FakturaPolozkaEditFormFactory
{
    private JednotkaModel $jednotkaModel;
    private DphModel $dphModel;
    private FakturaPolozkaCssModel $fakturaPolozkaCssModel;
    private FormFactory $formFactory;

    public function __construct(
        JednotkaModel          $jednotkaModel,
        DphModel               $dphModel,
        FakturaPolozkaCssModel $fakturaPolozkaCssModel,
        FormFactory            $formFactory
    )
    {
        $this->jednotkaModel = $jednotkaModel;
        $this->dphModel = $dphModel;
        $this->fakturaPolozkaCssModel = $fakturaPolozkaCssModel;
        $this->formFactory = $formFactory;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();

        $form->addHidden('id');
        $new = $form->addContainer('new');
        $new->addHidden('faktura');
        $new->addText('nazev', 'Nazev:', null, 250);
        $new->addText('dodatek', 'Dodatek:', null, 250);
        $new->addText('pocet_polozek', 'Pocet polozek:', null, 5);
        $new->addText('koeficient_cena', 'Koeficient cena:', null, 5);
        $new->addText('sleva', 'Sleva:', null, 5);
        $new->addSelect('jednotka', 'Jednotka:', $this->jednotkaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $new->addSelect('dph', 'DPH:', $this->dphModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $new->addSelect('cssclass', 'css:', $this->fakturaPolozkaCssModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $new->addText('cena', 'Cena:', null, 10);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

}
