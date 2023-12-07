<?php

namespace App\Forms\Admin\Edit;

/**
 * Description of OsobaForm
 *
 * @author Martin Patyk
 */

use App\Model\FirmaModel;
use App\Model\FormatDatumModel;
use App\Model\TimeZoneModel;
use App\Model\TypOsobyModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class OsobaForm extends UIForm
{
    private TypOsobyModel $typOsobyModel;
    private FirmaModel $firmaModel;
    private TimeZoneModel $timeZoneModel;
    private FormatDatumModel $formatDatumModel;

    /**
     * @param IContainer|null $parent
     * @param null $name
     */
    public function __construct(
        TypOsobyModel    $typOsobyModel,
        FirmaModel       $firmaModel,
        TimeZoneModel    $timeZoneModel,
        FormatDatumModel $formatDatumModel,
        IContainer $parent = null,
                   $name = null
    )
    {
        parent::__construct($parent, $name);

        $this->typOsobyModel = $typOsobyModel;
        $this->firmaModel = $firmaModel;
        $this->timeZoneModel = $timeZoneModel;
        $this->formatDatumModel = $formatDatumModel;

        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('jmeno', 'Jméno:', null, 100)
            ->addRule(Form::FILLED);
        $new->addText('prijmeni', 'Příjmení:', null, 100)
            ->addRule(Form::FILLED);
        $new->addText('email', 'E-mail:', null, 150)
            ->addRule(Form::FILLED)
            ->addRule(Form::EMAIL);
        $new->addSelect('firma', 'Firma:', $this->firmaModel->fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(' - - - ');
        $new->addSelect('typ_osoby', 'Typ osoby:', $this->typOsobyModel->fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(' - - - ');
        $new->addSelect('time_zone', 'Časová zona:', $this->timeZoneModel->fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(' - - - ');
        $new->addSelect('format_datum', 'Formád datumu:', $this->formatDatumModel->fetchPairs())
            ->addRule(Form::FILLED)
            ->setPrompt(' - - - ');
        $new->addCheckbox('je_admin', 'Jde o admina?');
        //Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection('Vypršel časový limit, odešlete formulář znovu');
        //Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
