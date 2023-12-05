<?php

namespace App\Form\Admin\Edit;

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
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('od', 'Odesilatel:', null, 150)
            ->addRule(Form::FILLED);
        $new->addSelect('ci', 'Produkt:', $this->ciModel->fetchPairs())
            ->setPrompt(' - - - ')
            ->addRule(Form::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
