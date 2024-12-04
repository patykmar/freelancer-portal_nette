<?php

namespace App\Factory\Forms;

use App\Model\FormaUhradyModel;
use App\Model\OsobaModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

/** @deprecated */
class InvoiceEditFormFactory
{
    private FormFactory $formFactory;
    private OsobaModel $osobaModel;
    private FormaUhradyModel $formaUhradyModel;

    public function __construct(
        FormFactory      $formFactory,
        OsobaModel       $osobaModel,
        FormaUhradyModel $formaUhradyModel
    )
    {
        $this->formFactory = $formFactory;
        $this->osobaModel = $osobaModel;
        $this->formaUhradyModel = $formaUhradyModel;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('id');
        $new = $form->addContainer('new');
        $new->addText('dodavatel_nazev', 'Dodavatel nazev:', null, 250)
            ->addRule(FormAlias::Filled);
        $new->addText('dodavatel_ico', 'Dodavatel ICO:', null, 20)
            ->addRule(FormAlias::Filled);
        $new->addText('dodavatel_dic', 'dodavatel DIC:', null, 20);
        $new->addText('dodavatel_ulice', 'Dodavatel ulice:', null, 100);
        $new->addText('dodavatel_obec', 'Dodavatel obec:', null, 100)
            ->addRule(FormAlias::Filled);
        $new->addText('dodavatel_psc', 'Dodavatel PSC:', null, 15)
            ->addRule(FormAlias::Filled);
        $new->addText('dodavatel_zeme', 'Dodavatel zeme:', null, 100)
            ->addRule(FormAlias::Filled);
        $new->addText('dodavatel_cislo_uctu', 'Dodavatel cislo uctu:', null, 50)
            ->addRule(FormAlias::Filled);
        $new->addText('dodavatel_iban', 'Dodavatel IBAN:', null, 100);
        $new->addText('odberatel_nazev', 'Odberatel nazev:', null, 250)
            ->addRule(FormAlias::Filled);
        $new->addText('odberatel_ico', 'Odberatel ICO:', null, 20)
            ->addRule(FormAlias::Filled);
        $new->addText('odberatel_dic', 'Odberatel DIC:', null, 20);
        $new->addText('odberatel_ulice', 'Odberatel ulice:', null, 100);
        $new->addText('odberatel_obec', 'Odberatel obec:', null, 100)
            ->addRule(FormAlias::Filled);
        $new->addText('odberatel_psc', 'Odberatel PSC:', null, 15)
            ->addRule(FormAlias::Filled);
        $new->addText('odberatel_zeme', 'Odberatel zeme:', null, 100)
            ->addRule(FormAlias::Filled);
        $new->addText('odberatel_cislo_uctu', 'Odberatel cislo uctu:', null, 50)
            ->addRule(FormAlias::Filled);
        $new->addText('odberatel_iban', 'Odberatel IBAN:', null, 100);
        $new->addText('splatnost', 'Splatnost:', null, 5)
            ->setType('number')
            ->addRule(FormAlias::INTEGER)
            ->addRule(FormAlias::RANGE, null, array(1, 999))
            ->addRule(FormAlias::Filled);
        $new->addText('datum_vystaveni', 'Datum vystaveni:')
            ->addRule(FormAlias::Filled);
        $new->addText('datum_splatnosti', 'Datum splatnosti:')
            ->addRule(FormAlias::Filled);
        $new->addText('datum_zaplaceni', 'Datum zaplaceni:');
        $new->addText('vs', 'Variabilni symbol:', null, 10);
        $new->addText('ks', 'Konstantni symbol:', null, 10);
        $new->addSelect('vytvoril', 'Vytvoril:', $this->osobaModel->fetchPairs())
            ->addRule(FormAlias::Filled);
        $new->addSelect('forma_uhrady', 'Forma uhrady:', $this->formaUhradyModel->fetchPairs())
            ->addRule(FormAlias::Filled);
        //Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        //Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož')
            ->setHtmlAttribute('class', 'btn btn-success');
        return $form;
    }
}
