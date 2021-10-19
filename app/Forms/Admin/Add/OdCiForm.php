<?php

namespace App\Form\Admin\Add;

/**
 * Description of OdCiForm
 *
 * @author Martin Patyk
 */

use App\Model\CiModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class OdCiForm extends UIForm
{

    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        $this->addText('od', 'Odesilatel:', NULL, 150)
            ->addRule(Form::FILLED);
        $this->addSelect('ci', 'Produkt:', CiModel::fetchPairs())
            ->setPrompt(' - - - ')
            ->addRule(Form::FILLED);
        //	Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //	Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}