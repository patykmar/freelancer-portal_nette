<?php

namespace App\Form\Admin\Add;

/**
 * Description of FakturaPolozkaForm
 *
 * @author Martin Patyk
 */

use App\Model\DphModel;
use App\Model\JednotkaModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form as NetteForm;
use App\Model;

class FakturaPolozkaForm extends UIForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        $this->addHidden('faktura');
        $this->addText('nazev', 'Nazev:', NULL, 250)
            ->addRule(NetteForm::FILLED);
        $this->addText('pocet_polozek', 'Pocet polozek:', NULL, 5)
            ->setType('number')
            ->addRule(NetteForm::INTEGER)
            ->addRule(NetteForm::RANGE, NULL, array(0, 999));
        $this->addSelect('jednotka', 'Jednotka:', JednotkaModel::fetchPairs())
            ->setPrompt(' - - - ');
        $this->addSelect('dph', 'DPH:', DphModel::fetchPairs())
            ->setPrompt(' - - - ');
        $this->addSelect('cssclass', 'css:')
            ->setItems(array(
                'faktura-polozka',
                'faktura-nadpis'
            ), FALSE);
        $this->addText('cena', 'Cena:', NULL, 10)
            ->addRule(NetteForm::FLOAT);
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}