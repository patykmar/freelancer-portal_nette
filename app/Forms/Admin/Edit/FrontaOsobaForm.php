<?php

namespace App\Form\Admin\Edit;

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

    /**
     * @param FrontaModel $frontaModel
     * @param OsobaModel $osobaModel
     * @param IContainer|null $parent
     * @param null $name
     */
    public function __construct(
        FrontaModel $frontaModel,
        OsobaModel  $osobaModel,
        IContainer  $parent = null,
                    $name = null
    )
    {
        parent::__construct($parent, $name);

        $this->frontaModel = $frontaModel;
        $this->osobaModel = $osobaModel;

        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addSelect('fronta', 'Fronta:', $this->frontaModel->fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(' - - - ');
        $new->addSelect('osoba', 'Osoba:', $this->osobaModel->fetchPairsSpecialistSystem())
            ->addRule(Form::FILLED)
            ->setPrompt(' - - - ');
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
