<?php

namespace App\Form\Admin\Add;

/**
 * Description of CiForm
 *
 * @author Martin Patyk
 */

use App\Model\CiModel;
use App\Model\FirmaModel;
use App\Model\FrontaModel;
use App\Model\StavCiModel;
use App\Model\TarifModel;
use Nette\Application\UI\Form as UIForm;
use Nette\Forms\Form as NetteForm;
use Nette\ComponentModel\IContainer;

class CiForm extends UIForm
{
    /** @var CiModel $ciModel */
    private $ciModel;

    /** @var StavCiModel $stavCiModel */
    private $stavCiModel;

    /** @var FrontaModel $frontaModel */
    private $frontaModel;

    /** @var FirmaModel $firmaModel */
    private $firmaModel;

    /** @var TarifModel $tarifModel */
    private $tarifModel;

    public function __construct(
        CiModel     $ciModel,
        StavCiModel $stavCiModel,
        FrontaModel $frontaModel,
        FirmaModel  $firmaModel,
        TarifModel  $tarifModel,
        IContainer  $parent = null,
                    $name = null
    )
    {
        $this->ciModel = $ciModel;
        $this->stavCiModel = $stavCiModel;
        $this->frontaModel = $frontaModel;
        $this->firmaModel = $firmaModel;
        $this->tarifModel = $tarifModel;

        parent::__construct($parent, $name);
        $this->addText('nazev', 'Název:', null, 250)
            ->addRule(NetteForm::FILLED);
        $this->addSelect('ci', 'Předek:', $this->ciModel->fetchPairs())
            ->setPrompt(' - - - ');
        $this->addSelect('stav_ci', 'Stav:', $this->stavCiModel->fetchPairs())
            ->setPrompt(' - - - ')
            ->addConditionOn($this['ci'], NetteForm::EQUAL, false)
            ->addRule(NetteForm::FILLED);
        $this->addSelect('fronta_tier_1', 'Výchozí fronta TIER 1:', $this->frontaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], NetteForm::EQUAL, 3)
            ->addRule(NetteForm::FILLED);
        $this->addSelect('fronta_tier_2', 'Výchozí fronta TIER 2:', $this->frontaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], NetteForm::EQUAL, 3)
            ->addRule(NetteForm::FILLED);
        $this->addSelect('fronta_tier_3', 'Výchozí fronta TIER 3:', $this->frontaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], NetteForm::EQUAL, 3)
            ->addRule(NetteForm::FILLED);
        $this->addSelect('firma', 'Firma:', $this->firmaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($this['ci'], NetteForm::EQUAL, false)
            ->addRule(NetteForm::FILLED);
        $this->addSelect('tarif', 'Tarif:', $this->tarifModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($this['ci'], NetteForm::EQUAL, false)
            ->addRule(NetteForm::FILLED);
        $this->addCheckbox('zobrazit', 'Zobrazit ?');
        $this->addTextArea('obsah', 'Obsah:')
            ->addRule(NetteForm::FILLED);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }

}
