<?php

namespace App\Form\Admin\Add;

/**
 * Description of OvlivneniForm
 *
 * @author Martin Patyk
 */

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class OvlivneniForm extends UIForm
{
    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addText('nazev', 'Název:', null, 255)
            ->addRule(Form::FILLED);
        $this->addText('koeficient_cena', 'Koeficient cena:', null, 10)
            ->setType('number')
            ->addRule(Form::FLOAT);
        $this->addText('koeficient_cas', 'Koeficient čas:', null, 10)
            ->setType('number')
            ->addRule(Form::FLOAT);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}