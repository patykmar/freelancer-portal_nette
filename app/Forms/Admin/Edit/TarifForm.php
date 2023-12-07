<?php

namespace App\Forms\Admin\Edit;

/**
 * Description of TarifForm
 *
 * @author Martin Patyk
 */

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class TarifForm extends UIForm
{
    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('nazev', 'Název:', null, 100)
            ->addRule(Form::FILLED);
        $new->addText('cena', 'Cena:', null, 13)
            ->addRule(Form::FILLED)
            ->addRule(Form::FLOAT);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
