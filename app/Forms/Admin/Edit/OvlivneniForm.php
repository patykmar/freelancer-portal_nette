<?php

namespace App\Forms\Admin\Edit;

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
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('nazev', 'Název:', null, 255)
            ->addRule(Form::FILLED);
        $new->addText('koeficient_cena', 'Koeficient cena:', null, 10)
            ->setType('number')
            ->addRule(Form::FLOAT);
        $new->addText('koeficient_cas', 'Koeficient čas:', null, 10)
            ->setType('number')
            ->addRule(Form::FLOAT);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}