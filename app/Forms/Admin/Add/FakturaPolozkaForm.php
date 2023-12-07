<?php

namespace App\Forms\Admin\Add;

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

class FakturaPolozkaForm extends UIForm
{
    private JednotkaModel $jednotkaModel;
    private DphModel $dphModel;

    public function __construct(
        JednotkaModel $jednotkaModel,
        DphModel      $dphModel,
        IContainer    $parent = null,
                      $name = null)
    {
        parent::__construct($parent, $name);

        $this->jednotkaModel = $jednotkaModel;
        $this->dphModel = $dphModel;

        $this->addHidden('faktura');
        $this->addText('nazev', 'Nazev:', null, 250)
            ->addRule(NetteForm::FILLED);
        $this->addText('pocet_polozek', 'Pocet polozek:', null, 5)
            ->setType('number')
            ->addRule(NetteForm::INTEGER)
            ->addRule(NetteForm::RANGE, null, array(0, 999));
        $this->addSelect('jednotka', 'Jednotka:', $this->jednotkaModel->fetchPairs())
            ->setPrompt(' - - - ');
        $this->addSelect('dph', 'DPH:', $this->dphModel->fetchPairs())
            ->setPrompt(' - - - ');
        $this->addSelect('cssclass', 'css:')
            ->setItems(array(
                'faktura-polozka',
                'faktura-nadpis'
            ), false);
        $this->addText('cena', 'Cena:', null, 10)
            ->addRule(NetteForm::FLOAT);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
