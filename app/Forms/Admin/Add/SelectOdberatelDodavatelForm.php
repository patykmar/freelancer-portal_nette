<?php

namespace App\Form\Admin\Add;

/**
 * Description of SelectOdberatelDodavatelForm
 *
 * @author Martin Patyk
 */

use App\Model\FirmaModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use App\Model;

class SelectOdberatelDodavatelForm extends UIForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        $this->addSelect('dodvatel', 'Dodavatel:', FirmaModel::fetchPairs())
            ->setPrompt(' - - - ');
        $this->addSelect('odberatel', 'Odberatel:', FirmaModel::fetchPairs())
            ->setPrompt(' - - - ');
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}