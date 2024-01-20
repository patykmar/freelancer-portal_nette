<?php

namespace App\Forms\Admin\Add;

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form as NetteForm;

class FkBaseForm extends UIForm
{
    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addText('nazev', 'Název:', null, 100)
            ->addRule(NetteForm::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
