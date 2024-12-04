<?php

namespace App\Factory\Forms;

use App\Model\CiModel;
use App\Model\FirmaModel;
use App\Model\FrontaModel;
use App\Model\StavCiModel;
use App\Model\TarifModel;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Form as FormAlias;

class CiEditFormFactory
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
        $form->addHidden('id');
        $new = $form->addContainer('new');
        $new->addText('nazev', 'Název:', null, 250)
            ->addRule(FormAlias::FILLED);
        $new->addSelect('ci', 'Předek:', $this->ciModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $new->addSelect('stav_ci', 'Stav:', $this->stavCiModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addConditionOn($new['ci'], FormAlias::EQUAL, false)
            ->addRule(FormAlias::FILLED);
        $this->createFormQueuueField($new, 1);
        $this->createFormQueuueField($new, 2);
        $this->createFormQueuueField($new, 3);
        $new->addSelect('firma', 'Firma:', $this->firmaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($new['ci'], FormAlias::EQUAL, false)
            ->addRule(FormAlias::FILLED);
        $new->addSelect('tarif', 'Tarif:', $this->tarifModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($new['ci'], FormAlias::EQUAL, false)
            ->addRule(FormAlias::FILLED);
        $new->addCheckbox('zobrazit', 'Zobrazit ?');
        $new->addTextArea('obsah', 'Obsah:')
            ->addRule(FormAlias::FILLED);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

    private function createFormQueuueField(Container $container, int $id)
    {
        $container->addSelect('fronta_tier_' . $id, "Výchozí fronta TIER $id:", $this->frontaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($container['stav_ci'], Form::EQUAL, 3)
            ->setRequired('Zadejte frontu na kterou se budou prirazovat tikety');
    }

}
