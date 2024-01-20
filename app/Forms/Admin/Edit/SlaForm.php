<?php

namespace App\Forms\Admin\Edit;

/**
 * Description of SlaForm
 *
 * @author Martin Patyk
 */

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use App\Forms\Admin\Add\SlaForm as AddSlaForm;
use Nette\Forms\Form;

class SlaForm extends UIForm
{
    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('tarif', 'Název tarifu')
            ->setDisabled();
        $new->addText('priorita', 'Priorita')
            ->setDisabled();
        $new->addText('cena_koeficient', 'Koeficient', null, 5)
            ->addRule(Form::FILLED)
            ->addRule(Form::FLOAT);
        // casy reakce
        $new->addSelect('reakce_mesic', 'Měsíců:', AddSlaForm::getTimeValue(AddSlaForm::MONTHS))
            ->addRule(Form::FILLED);
        $new->addSelect('reakce_den', 'Dnů', AddSlaForm::getTimeValue(AddSlaForm::DAYS))
            ->addRule(Form::FILLED);
        $new->addSelect('reakce_hod', 'Hodin:', AddSlaForm::getTimeValue(AddSlaForm::HOURS))
            ->addRule(Form::FILLED);
        $new->addSelect('reakce_min', 'Minut:', AddSlaForm::getTimeValue(AddSlaForm::MINUTES))
            ->addRule(Form::FILLED);
        $new->addSelect('hotovo_mesic', 'Měsíců:', AddSlaForm::getTimeValue(AddSlaForm::MONTHS))
            ->addRule(Form::FILLED);
        $new->addSelect('hotovo_den', 'Dnů', AddSlaForm::getTimeValue(AddSlaForm::DAYS))
            ->addRule(Form::FILLED);
        $new->addSelect('hotovo_hod', 'Hodin:', AddSlaForm::getTimeValue(AddSlaForm::HOURS))
            ->addRule(Form::FILLED);
        $new->addSelect('hotovo_min', 'Minut:', AddSlaForm::getTimeValue(AddSlaForm::MINUTES))
            ->addRule(Form::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
