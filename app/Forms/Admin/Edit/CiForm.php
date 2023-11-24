<?php

namespace App\Form\Admin\Edit;

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
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

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
        parent::__construct($parent, $name);

        $this->ciModel = $ciModel;
        $this->stavCiModel = $stavCiModel;
        $this->frontaModel = $frontaModel;
        $this->firmaModel = $firmaModel;
        $this->tarifModel = $tarifModel;

        $this->addHidden('id');
        /*
                $old = $this->addContainer('old');
                $old->addHidden('nazev');
                $old->addHidden('stav_ci');
                $old->addHidden('fronta_specialista');
                $old->addHidden('firma');
                $old->addHidden('tarif');
                $old->addHidden('zobrazit');
        */
        $new = $this->addContainer('new');
        $new->addText('nazev', 'Název:', null, 250)
            ->addRule(Form::FILLED);
        $new->addSelect('ci', 'Předek:', $this->ciModel->fetchPairs())
            ->setPrompt(' - - - ');
        $new->addSelect('stav_ci', 'Stav:', $this->stavCiModel->fetchPairs())
            ->setPrompt(' - - - ')
            ->addConditionOn($new['ci'], Form::EQUAL, false)
            ->addRule(Form::FILLED);
        $new->addSelect('fronta_tier_1', 'Výchozí fronta TIER 1:', $this->frontaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($new['stav_ci'], Form::EQUAL, 3)
            ->setRequired('Zadejte frontu na kterou se budou prirazovat tikety');
        $new->addSelect('fronta_tier_2', 'Výchozí fronta TIER 2:', $this->frontaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($new['stav_ci'], Form::EQUAL, 3)
            ->setRequired('Zadejte frontu na kterou se budou prirazovat tikety');
        $new->addSelect('fronta_tier_3', 'Výchozí fronta TIER 3:', $this->frontaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($new['stav_ci'], Form::EQUAL, 3)
            ->setRequired('Zadejte frontu na kterou se budou prirazovat tikety');
        $new->addSelect('firma', 'Firma:', $this->firmaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($new['ci'], Form::EQUAL, false)
            ->addRule(Form::FILLED);
        $new->addSelect('tarif', 'Tarif:', $this->tarifModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($new['ci'], Form::EQUAL, false)
            ->addRule(Form::FILLED);
        $new->addCheckbox('zobrazit', 'Zobrazit ?');
        $new->addTextArea('obsah', 'Obsah:')
            ->addRule(Form::FILLED);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }

}
