<?php

namespace App\Forms\Admin\Edit;

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
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('nazev', 'Název:', null, 255)
            ->addRule(Form::FILLED);
        $new->addText('cena', 'Cena:', null, 13)
            ->setType('number')
            ->addRule(Form::FLOAT);
        $new->addText('cas_realizace', 'Čas realizace (Sec):')
            ->setType('number')
            ->addRule(Form::INTEGER);
        $new->addText('cas_reakce', 'Čas reakce (Sec):')
            ->setType('number')
            ->addRule(Form::INTEGER);
        $new->addTextArea('popis', 'Popis:')
            ->addRule(Form::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
