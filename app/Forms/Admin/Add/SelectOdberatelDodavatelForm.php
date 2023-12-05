<?php

namespace App\Form\Admin\Add;

/**
 * Description of SelectOdberatelDodavatelForm
 *
 * @author Martin Patyk
 */

use App\Model\FirmaModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;

class SelectOdberatelDodavatelForm extends UIForm
{
    const EMPTY_PROMPT = ' - - - ';
    private FirmaModel $firmaModel;

    public function __construct(FirmaModel $firmaModel, IContainer $parent = null, $name = null)
    {
        $this->firmaModel = $firmaModel;


        parent::__construct($parent, $name);
        $this->addSelect('dodvatel', 'Dodavatel:', $this->firmaModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT);
        $this->addSelect('odberatel', 'Odberatel:', $this->firmaModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
