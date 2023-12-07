<?php

namespace App\Forms\Admin\Add;

/**
 * Description of UkonForm
 *
 * @author Martin Patyk
 */

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class UkonForm extends UIForm
{
    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addText('nazev', 'Název:', null, 255)
            ->addRule(Form::FILLED);
        $this->addText('cena', 'Cena:', null, 13)
            ->setType('number')
            ->addRule(Form::FLOAT);
        $this->addText('cas_realizace', 'Čas realizace (Sec):', null, 255)
            ->setType('number')
            ->addRule(Form::FILLED);
        $this->addText('cas_reakce', 'Čas reakce (Sec):', null, 255)
            ->setType('number')
            ->addRule(Form::FILLED);
        $this->addTextArea('popis', 'Popis:')
            ->addRule(Form::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}