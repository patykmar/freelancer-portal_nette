<?php

namespace App\Factory\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class SlaEditFormFactory
{
    private FormFactory $formFactory;

    /**
     * @param FormFactory $formFactory
     * ¬*/
    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('id');
        $new = $form->addContainer('new');
        $new->addText('tarif', 'Název tarifu')
            ->setDisabled();
        $new->addSelect('priorita', 'Priorita')
            ->setDisabled();
        $new->addText('cena_koeficient', 'Koeficient', null, 5)
            ->addRule(FormAlias::FILLED)
            ->addRule(FormAlias::FLOAT);
        // casy reakce
        $new->addSelect('reakce_mesic', 'Měsíců:', SlaAddFormFactory::getTimeValue(SlaAddFormFactory::MONTHS))
            ->addRule(FormAlias::FILLED);
        $new->addSelect('reakce_den', 'Dnů', SlaAddFormFactory::getTimeValue(SlaAddFormFactory::DAYS))
            ->addRule(FormAlias::FILLED);
        $new->addSelect('reakce_hod', 'Hodin:', SlaAddFormFactory::getTimeValue(SlaAddFormFactory::HOURS))
            ->addRule(FormAlias::FILLED);
        $new->addSelect('reakce_min', 'Minut:', SlaAddFormFactory::getTimeValue(SlaAddFormFactory::MINUTES))
            ->addRule(FormAlias::FILLED);
        $new->addSelect('hotovo_mesic', 'Měsíců:', SlaAddFormFactory::getTimeValue(SlaAddFormFactory::MONTHS))
            ->addRule(FormAlias::FILLED);
        $new->addSelect('hotovo_den', 'Dnů', SlaAddFormFactory::getTimeValue(SlaAddFormFactory::DAYS))
            ->addRule(FormAlias::FILLED);
        $new->addSelect('hotovo_hod', 'Hodin:', SlaAddFormFactory::getTimeValue(SlaAddFormFactory::HOURS))
            ->addRule(FormAlias::FILLED);
        $new->addSelect('hotovo_min', 'Minut:', SlaAddFormFactory::getTimeValue(SlaAddFormFactory::MINUTES))
            ->addRule(FormAlias::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

}
