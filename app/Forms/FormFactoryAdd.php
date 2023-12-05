<?php

namespace App\Form\Klient;

/**
 * Description of FormFactoryAdd
 *
 * @author Martin Patyk
 */

use App\Model\CiModel;
use App\Model\FirmaModel;
use App\Model\FrontaModel;
use App\Model\StavCiModel;
use App\Model\TarifModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class FormFactoryAdd extends UIForm
{
    const EMPTY_PROMPT = ' - - - ';
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
     * @param IContainer|null $parent
     * @param null $name
     */
    public function __construct(CiModel     $ciModel,
                                FrontaModel $frontaModel,
                                FirmaModel  $firmaModel,
                                TarifModel  $tarifModel,
                                StavCiModel $stavCiModel,
                                IContainer  $parent = null,
                                            $name = null)
    {
        parent::__construct($parent, $name);

        $this->ciModel = $ciModel;
        $this->frontaModel = $frontaModel;
        $this->firmaModel = $firmaModel;
        $this->tarifModel = $tarifModel;
        $this->stavCiModel = $stavCiModel;

        $this->addText('nazev', 'Název:', null, 250)
            ->addRule(Form::FILLED);
        $this->addSelect('ci', 'Předek:', $this->ciModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT);
        $this->addSelect('stav_ci', 'Stav:', $this->stavCiModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT)
            ->addConditionOn($this['ci'], Form::EQUAL, false)
            ->addRule(Form::FILLED);
        $this->addSelect('fronta_tier_1', 'Výchozí fronta TIER 1:', $this->frontaModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT)
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], Form::EQUAL, 3)
            ->addRule(Form::FILLED);
        $this->addSelect('fronta_tier_2', 'Výchozí fronta TIER 2:', $this->frontaModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT)
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], Form::EQUAL, 3)
            ->addRule(Form::FILLED);
        $this->addSelect('fronta_tier_3', 'Výchozí fronta TIER 3:', $this->frontaModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT)
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], Form::EQUAL, 3)
            ->addRule(Form::FILLED);
        $this->addSelect('firma', 'Firma:', $this->firmaModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT)
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($this['ci'], Form::EQUAL, false)
            ->addRule(Form::FILLED);
        $this->addSelect('tarif', 'Tarif:', $this->tarifModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT)
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($this['ci'], Form::EQUAL, false)
            ->addRule(Form::FILLED);
        $this->addCheckbox('zobrazit', 'Zobrazit ?');
        $this->addTextArea('obsah', 'Obsah:')
            ->addRule(Form::FILLED);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }

}
