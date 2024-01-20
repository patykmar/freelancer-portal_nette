<?php

namespace App\Forms\Admin\Edit;

/**
 * Description of FakturaPolozkaForm
 *
 * @author Martin Patyk
 */

use App\Model\DphModel;
use App\Model\FakturaPolozkaCssModel;
use App\Model\JednotkaModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;

class FakturaPolozkaForm extends UIForm
{
    const EMPTY_PROMPT = IForm::INPUT_SELECT_PROMPT;
    private JednotkaModel $jednotkaModel;
    private DphModel $dphModel;
    private FakturaPolozkaCssModel $fakturaPolozkaCssModel;

    public function __construct(
        JednotkaModel          $jednotkaModel,
        DphModel               $dphModel,
        FakturaPolozkaCssModel $fakturaPolozkaCssModel,
        IContainer             $parent = null,
                               $name = null)
    {
        $this->jednotkaModel = $jednotkaModel;
        $this->dphModel = $dphModel;
        $this->fakturaPolozkaCssModel = $fakturaPolozkaCssModel;

        parent::__construct($parent, $name);
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addHidden('faktura');
        $new->addText('nazev', 'Nazev:', null, 250);
        $new->addText('dodatek', 'Dodatek:', null, 250);
        $new->addText('pocet_polozek', 'Pocet polozek:', null, 5);
        $new->addText('koeficient_cena', 'Koeficient cena:', null, 5);
        $new->addText('sleva', 'Sleva:', null, 5);
        $new->addSelect('jednotka', 'Jednotka:', $this->jednotkaModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT);
        $new->addSelect('dph', 'DPH:', $this->dphModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT);
        $new->addSelect('cssclass', 'css:', $this->fakturaPolozkaCssModel->fetchPairs())
            ->setPrompt(self::EMPTY_PROMPT);
        $new->addText('cena', 'Cena:', null, 10);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;

    }
}
