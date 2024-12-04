<?php

namespace App\Forms\Front;

/**
 * Description of CiForm
 *
 * @author Martin Patyk
 */

use App\Factory\Forms\IForm;
use Nette\Application\UI\Form as UIForm;
use Nette\Forms\Form;

class FeedBackNegativeForm extends UIForm
{
    public function __construct()
    {
        parent::__construct();
        $this->addHidden('id');
        $this->addTextArea('wl', 'Důvod zamítnutí:')
            ->addRule(Form::Filled);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}