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

class CiForm extends UIForm {

	public function __construct(IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
		$this->addText('nazev', 'Název:', null, 250)
				  ->addRule(NetteForm::FILLED);
		$this->addSelect('ci', 'Předek:', CiModel::fetchPairs())
				  ->setPrompt(' - - - ');
		$this->addSelect('stav_ci', 'Stav:', StavCiModel::fetchPairs())
				  ->setPrompt(' - - - ')
				  ->addConditionOn($this['ci'], NetteForm::EQUAL, FALSE)
				  ->addRule(NetteForm::FILLED);
		$this->addSelect('fronta_tier_1', 'Výchozí fronta TIER 1:', FrontaModel::fetchPairs())
				  ->setPrompt(' - - - ')
				  //	pokud je stav nasazen je potreba vybrat i frontu
				  ->addConditionOn($this['stav_ci'], NetteForm::EQUAL, 3)
				  ->addRule(NetteForm::FILLED);
		$this->addSelect('fronta_tier_2', 'Výchozí fronta TIER 2:', FrontaModel::fetchPairs())
				  ->setPrompt(' - - - ')
				  //	pokud je stav nasazen je potreba vybrat i frontu
				  ->addConditionOn($this['stav_ci'], NetteForm::EQUAL, 3)
				  ->addRule(NetteForm::FILLED);
		$this->addSelect('fronta_tier_3', 'Výchozí fronta TIER 3:', FrontaModel::fetchPairs())
				  ->setPrompt(' - - - ')
				  //	pokud je stav nasazen je potreba vybrat i frontu
				  ->addConditionOn($this['stav_ci'], NetteForm::EQUAL, 3)
				  ->addRule(NetteForm::FILLED);
		$this->addSelect('firma', 'Firma:', FirmaModel::fetchPairs())
				  ->setPrompt(' - - - ')
				  //	pokud neni vybran predek je potreba vyplnit toto pole
				  ->addConditionOn($this['ci'], NetteForm::EQUAL, FALSE)
				  ->addRule(NetteForm::FILLED);
		$this->addSelect('tarif', 'Tarif:', TarifModel::fetchPairs())
				  ->setPrompt(' - - - ')
				  //	pokud neni vybran predek je potreba vyplnit toto pole
				  ->addConditionOn($this['ci'], NetteForm::EQUAL, FALSE)
				  ->addRule(NetteForm::FILLED);
		$this->addCheckbox('zobrazit', 'Zobrazit ?');
		$this->addTextArea('obsah', 'Obsah:')
				  ->addRule(NetteForm::FILLED);
		//	Obrana před Cross-Site Request Forgery (CSRF)
		$this->addProtection('Vypršel časový limit, odešlete formulář znovu');
		//	Tlacitko odeslat
		$this->addSubmit('btSbmt', 'Ulož');
		return $this;
	}
}