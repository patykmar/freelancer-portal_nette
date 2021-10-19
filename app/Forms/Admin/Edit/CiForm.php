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
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
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
        $new->addSelect('ci', 'Předek:', CiModel::fetchPairs())
            ->setPrompt(' - - - ');
        $new->addSelect('stav_ci', 'Stav:', StavCiModel::fetchPairs())
            ->setPrompt(' - - - ')
            ->addConditionOn($new['ci'], Form::EQUAL, FALSE)
            ->addRule(Form::FILLED);
        $new->addSelect('fronta_tier_1', 'Výchozí fronta TIER 1:', FrontaModel::fetchPairs())
            ->setPrompt(' - - - ')
            //	pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($new['stav_ci'], Form::EQUAL, 3)
            ->setRequired('Zadejte frontu na kterou se budou prirazovat tikety');
        $new->addSelect('fronta_tier_2', 'Výchozí fronta TIER 2:', FrontaModel::fetchPairs())
            ->setPrompt(' - - - ')
            //	pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($new['stav_ci'], Form::EQUAL, 3)
            ->setRequired('Zadejte frontu na kterou se budou prirazovat tikety');
        $new->addSelect('fronta_tier_3', 'Výchozí fronta TIER 3:', FrontaModel::fetchPairs())
            ->setPrompt(' - - - ')
            //	pokud je stav nasazen je potreba vybrat i frontu
            ->addConditionOn($new['stav_ci'], Form::EQUAL, 3)
            ->setRequired('Zadejte frontu na kterou se budou prirazovat tikety');
        $new->addSelect('firma', 'Firma:', FirmaModel::fetchPairs())
            ->setPrompt(' - - - ')
            //	pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($new['ci'], Form::EQUAL, FALSE)
            ->addRule(Form::FILLED);
        $new->addSelect('tarif', 'Tarif:', TarifModel::fetchPairs())
            ->setPrompt(' - - - ')
            //	pokud neni vybran predek je potreba vyplnit toto pole
            ->addConditionOn($new['ci'], Form::EQUAL, FALSE)
            ->addRule(Form::FILLED);
        $new->addCheckbox('zobrazit', 'Zobrazit ?');
        $new->addTextArea('obsah', 'Obsah:')
            ->addRule(Form::FILLED);
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}