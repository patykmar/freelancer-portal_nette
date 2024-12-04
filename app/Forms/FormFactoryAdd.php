<?php

namespace App\Forms;

/**
 * Description of FormFactoryAdd
 *
 * @author Martin Patyk
 */

use App\Factory\Forms\IForm;
use App\Model\CiModel;
use App\Model\FirmaModel;
use App\Model\FrontaModel;
use App\Model\StavCiModel;
use App\Model\TarifModel;
use Nette\Application\UI\Form as UIForm;
use Nette\Forms\Form;

class FormFactoryAdd extends UIForm
{
    private CiModel $ciModel;
    private FrontaModel $frontaModel;
    private FirmaModel $firmaModel;
    private TarifModel $tarifModel;
    private StavCiModel $stavCiModel;

    /**
     * @param CiModel $ciModel
     * @param FrontaModel $frontaModel
     * @param FirmaModel $firmaModel
     * @param TarifModel $tarifModel
     * @param StavCiModel $stavCiModel
     */
    public function __construct(
        CiModel     $ciModel,
        FrontaModel $frontaModel,
        FirmaModel  $firmaModel,
        TarifModel  $tarifModel,
        StavCiModel $stavCiModel
    )
    {
        parent::__construct();

        $this->ciModel = $ciModel;
        $this->frontaModel = $frontaModel;
        $this->firmaModel = $firmaModel;
        $this->tarifModel = $tarifModel;
        $this->stavCiModel = $stavCiModel;

        $this->addText('nazev', 'Název:', null, 250)
            ->addRule(Form::Filled);
        $this->addSelect('ci', 'Předek:', $this->ciModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $this->addSelect('stav_ci', 'Stav:', $this->stavCiModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addConditionOn($this['ci'], Form::EQUAL, false)
            ->addRule(Form::Filled);
        $this->addSelect('fronta_tier_1', 'Výchozí fronta TIER 1:', $this->frontaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], Form::EQUAL, 3)
            ->addRule(Form::Filled);
        $this->addSelect('fronta_tier_2', 'Výchozí fronta TIER 2:', $this->frontaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], Form::EQUAL, 3)
            ->addRule(Form::Filled);
        $this->addSelect('fronta_tier_3', 'Výchozí fronta TIER 3:', $this->frontaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], Form::EQUAL, 3)
            ->addRule(Form::Filled);
        $this->addSelect('firma', 'Firma:', $this->firmaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($this['ci'], Form::EQUAL, false)
            ->addRule(Form::Filled);
        $this->addSelect('tarif', 'Tarif:', $this->tarifModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($this['ci'], Form::EQUAL, false)
            ->addRule(Form::Filled);
        $this->addCheckbox('zobrazit', 'Zobrazit ?');
        $this->addTextArea('obsah', 'Obsah:')
            ->addRule(Form::Filled);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }

}
