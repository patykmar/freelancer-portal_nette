<?php

namespace App\Forms\Admin\Add;

/**
 * Description of FrontaOsobaForm
 *
 * @author Martin Patyk
 */

use App\Model\FrontaModel;
use App\Model\OsobaModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class FrontaOsobaForm extends UIForm
{
    private $frontaModel;
    private $osobaModel;

    public function __construct(
        FrontaModel $frontaModel,
        OsobaModel  $osobaModel,
        IContainer  $parent = null,
                    $name = null
    )
    {
        $this->frontaModel = $frontaModel;
        $this->osobaModel = $osobaModel;

        parent::__construct($parent, $name);
        $this->addSelect('fronta', 'Fronta:', $this->frontaModel->fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $this->addSelect('osoba', 'Osoba:', $this->osobaModel->fetchPairsSpecialistSystem())
            ->addRule(Form::FILLED)
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
