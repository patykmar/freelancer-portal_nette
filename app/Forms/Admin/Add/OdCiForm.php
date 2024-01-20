<?php

namespace App\Forms\Admin\Add;

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
    private CiModel $ciModel;

    public function __construct(CiModel $ciModel, IContainer $parent = null, $name = null)
    {
        $this->ciModel = $ciModel;

        parent::__construct($parent, $name);
        $this->addText('od', 'Odesilatel:', null, 150)
            ->addRule(Form::FILLED);
        $this->addSelect('ci', 'Produkt:', $this->ciModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addRule(Form::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;

    }

}
