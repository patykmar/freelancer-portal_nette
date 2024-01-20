<?php

namespace App\Factory\Forms;

use App\Model\DphModel;
use App\Model\JednotkaModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as NetteForm;

class FakturaPolozkaAddFormFactory
{
    private JednotkaModel $jednotkaModel;
    private DphModel $dphModel;
    private FormFactory $formFactory;

    public function __construct(JednotkaModel $jednotkaModel, DphModel $dphModel, FormFactory $formFactory)
    {
        $this->jednotkaModel = $jednotkaModel;
        $this->dphModel = $dphModel;
        $this->formFactory = $formFactory;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('faktura');
        $form->addText('nazev', 'Nazev:', null, 250)
            ->addRule(NetteForm::FILLED);
        $form->addText('pocet_polozek', 'Pocet polozek:', null, 5)
            ->setType('number')
            ->addRule(NetteForm::INTEGER)
            ->addRule(NetteForm::RANGE, null, array(0, 999));
        $form->addSelect('jednotka', 'Jednotka:', $this->jednotkaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $form->addSelect('dph', 'DPH:', $this->dphModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $form->addSelect('cssclass', 'css:')
            ->setItems(array(
                'faktura-polozka',
                'faktura-nadpis'
            ), false);
        $form->addText('cena', 'Cena:', null, 10)
            ->addRule(NetteForm::FLOAT);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }


}
