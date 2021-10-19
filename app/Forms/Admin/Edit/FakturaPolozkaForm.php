<?php

namespace App\Form\Admin\Edit;

/**
 * Description of FakturaPolozkaForm
 *
 * @author Martin Patyk
 */

use App\Model\DphModel;
use App\Model\FakturaPolozkaCssModel;
use App\Model\JednotkaModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Container;

class FakturaPolozkaForm extends UIForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        $this->addHidden('id');
        /** @var Container */
        $new = $this->addContainer('new');
        $new->addHidden('faktura');
        $new->addText('nazev', 'Nazev:', NULL, 250);
        $new->addText('dodatek', 'Dodatek:', NULL, 250);
        $new->addText('pocet_polozek', 'Pocet polozek:', NULL, 5);
        $new->addText('koeficient_cena', 'Koeficient cena:', NULL, 5);
        $new->addText('sleva', 'Sleva:', NULL, 5);
        $new->addSelect('jednotka', 'Jednotka:', JednotkaModel::fetchPairs())
            ->setPrompt(' - - - ');
        $new->addSelect('dph', 'DPH:', DphModel::fetchPairs())
            ->setPrompt(' - - - ');
        $new->addSelect('cssclass', 'css:', FakturaPolozkaCssModel::fetchPairs())
            ->setPrompt(' - - - ');
        $new->addText('cena', 'Cena:', NULL, 10);
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}