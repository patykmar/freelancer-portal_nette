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
use DibiException;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class FormFactoryAdd extends UIForm
{
    /** @var FrontaModel $frontaModel */
    private $frontaModel;

    /**
     * @param FrontaModel $frontaModel
     * @param IContainer|null $parent
     * @param null $name
     * @throws DibiException
     */
    public function __construct(
        FrontaModel $frontaModel,
        IContainer  $parent = null,
                    $name = null
    )
    {
        parent::__construct($parent, $name);

        $this->frontaModel = $frontaModel;

        $this->addText('nazev', 'Název:', null, 250)
            ->addRule(Form::FILLED);
        $this->addSelect('ci', 'Předek:', CiModel::fetchPairs())
            ->setPrompt(' - - - ');
        $this->addSelect('stav_ci', 'Stav:', StavCiModel::fetchPairs())
            ->setPrompt(' - - - ')
            ->addConditionOn($this['ci'], Form::EQUAL, false)
            ->addRule(Form::FILLED);
        $this->addSelect('fronta_tier_1', 'Výchozí fronta TIER 1:', $this->frontaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], Form::EQUAL, 3)
            ->addRule(Form::FILLED);
        $this->addSelect('fronta_tier_2', 'Výchozí fronta TIER 2:', $this->frontaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], Form::EQUAL, 3)
            ->addRule(Form::FILLED);
        $this->addSelect('fronta_tier_3', 'Výchozí fronta TIER 3:', $this->frontaModel->fetchPairs())
            ->setPrompt(' - - - ')
            //pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($this['stav_ci'], Form::EQUAL, 3)
            ->addRule(Form::FILLED);
        $this->addSelect('firma', 'Firma:', FirmaModel::fetchPairs())
            ->setPrompt(' - - - ')
            //pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($this['ci'], Form::EQUAL, false)
            ->addRule(Form::FILLED);
        $this->addSelect('tarif', 'Tarif:', TarifModel::fetchPairs())
            ->setPrompt(' - - - ')
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
