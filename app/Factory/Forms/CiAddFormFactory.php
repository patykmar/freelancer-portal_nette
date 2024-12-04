<?php

namespace App\Factory\Forms;

use App\Model\CiModel;
use App\Model\FirmaModel;
use App\Model\FrontaModel;
use App\Model\StavCiModel;
use App\Model\TarifModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as NetteForm;

class CiAddFormFactory
{
    private FormFactory $formFactory;
    private CiModel $ciModel;
    private StavCiModel $stavCiModel;
    private FrontaModel $frontaModel;
    private FirmaModel $firmaModel;
    private TarifModel $tarifModel;

    public function __construct(
        FormFactory $formFactory,
        CiModel     $ciModel,
        StavCiModel $stavCiModel,
        FrontaModel $frontaModel,
        FirmaModel  $firmaModel,
        TarifModel  $tarifModel
    )
    {
        $this->formFactory = $formFactory;
        $this->ciModel = $ciModel;
        $this->stavCiModel = $stavCiModel;
        $this->frontaModel = $frontaModel;
        $this->firmaModel = $firmaModel;
        $this->tarifModel = $tarifModel;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addText('nazev', 'Název:', null, 250)
            ->addRule(NetteForm::Filled);
        $form->addSelect('ci', 'Předek:', $this->ciModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $form->addSelect('stav_ci', 'Stav:', $this->stavCiModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addConditionOn($form['ci'], NetteForm::EQUAL, false)
            ->addRule(NetteForm::Filled);
        $form->addSelect('fronta_tier_1', 'Výchozí fronta TIER 1:', $this->frontaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($form['stav_ci'], NetteForm::EQUAL, 3)
            ->addRule(NetteForm::Filled);
        $form->addSelect('fronta_tier_2', 'Výchozí fronta TIER 2:', $this->frontaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($form['stav_ci'], NetteForm::EQUAL, 3)
            ->addRule(NetteForm::Filled);
        $form->addSelect('fronta_tier_3', 'Výchozí fronta TIER 3:', $this->frontaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($form['stav_ci'], NetteForm::EQUAL, 3)
            ->addRule(NetteForm::Filled);
        $form->addSelect('firma', 'Firma:', $this->firmaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($form['ci'], NetteForm::EQUAL, false)
            ->addRule(NetteForm::Filled);
        $form->addSelect('tarif', 'Tarif:', $this->tarifModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($form['ci'], NetteForm::EQUAL, false)
            ->addRule(NetteForm::Filled);
        $form->addCheckbox('zobrazit', 'Zobrazit ?');
        $form->addTextArea('obsah', 'Obsah:')
            ->addRule(NetteForm::Filled);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }


}
