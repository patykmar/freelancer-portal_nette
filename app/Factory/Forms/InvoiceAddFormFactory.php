<?php

namespace App\Factory\Forms;

use App\Model\DphModel;
use App\Model\FormaUhradyModel;
use App\Model\JednotkaModel;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Form as NetteForm;

class InvoiceAddFormFactory
{
    private FormFactory $formFactory;
    private FormaUhradyModel $formaUhradyModel;
    private JednotkaModel $jednotkaModel;
    private DphModel $dphModel;

    public function __construct(
        FormFactory      $formFactory,
        FormaUhradyModel $formaUhradyModel,
        JednotkaModel    $jednotkaModel,
        DphModel         $dphModel
    )
    {
        $this->formFactory = $formFactory;
        $this->formaUhradyModel = $formaUhradyModel;
        $this->jednotkaModel = $jednotkaModel;
        $this->dphModel = $dphModel;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('vytvoril');
        $form->addText('dodavatel_nazev', 'Dodavatel nazev:', null, 250)
            ->addRule(NetteForm::FILLED);
        $form->addText('dodavatel_ico', 'Dodavatel ICO:', null, 20)
            ->addRule(NetteForm::FILLED);
        $form->addText('dodavatel_dic', 'dodavatel DIC:', null, 20);
        $form->addText('dodavatel_ulice', 'Dodavatel ulice:', null, 100);
        $form->addText('dodavatel_obec', 'Dodavatel obec:', null, 100)
            ->addRule(NetteForm::FILLED);
        $form->addText('dodavatel_psc', 'Dodavatel PSC:', null, 15)
            ->addRule(NetteForm::FILLED);
        $form->addText('dodavatel_zeme', 'Dodavatel zeme:', null, 100)
            ->addRule(NetteForm::FILLED);
        $form->addText('dodavatel_cislo_uctu', 'Dodavatel cislo uctu:', null, 50)
            ->addRule(NetteForm::FILLED);
        $form->addText('dodavatel_iban', 'Dodavatel IBAN:', null, 100);
        $form->addText('odberatel_nazev', 'Odberatel nazev:', null, 250)
            ->addRule(NetteForm::FILLED);
        $form->addText('odberatel_ico', 'Odberatel ICO:', null, 20)
            ->addRule(NetteForm::FILLED);
        $form->addText('odberatel_dic', 'Odberatel DIC:', null, 20);
        $form->addText('odberatel_ulice', 'Odberatel ulice:', null, 100);
        $form->addText('odberatel_obec', 'Odberatel obec:', null, 100)
            ->addRule(NetteForm::FILLED);
        $form->addText('odberatel_psc', 'Odberatel PSC:', null, 15)
            ->addRule(NetteForm::FILLED);
        $form->addText('odberatel_zeme', 'Odberatel zeme:', null, 100)
            ->addRule(NetteForm::FILLED);
        $form->addText('odberatel_cislo_uctu', 'Odberatel cislo uctu:', null, 50)
            ->addRule(NetteForm::FILLED);
        $form->addText('odberatel_iban', 'Odberatel IBAN:', null, 100);
        $form->addText('splatnost', 'Splatnost:', null, 5)
            ->setType('number')
            ->addRule(NetteForm::INTEGER)
            ->addRule(NetteForm::RANGE, null, array(1, 999))
            ->addRule(NetteForm::FILLED);
        $form->addText('datum_vystaveni', 'Datum vystaveni:');
        $form->addText('datum_splatnosti', 'Datum splatnosti:');
        $form->addText('datum_zaplaceni', 'Datum zaplaceni:');
        $form->addText('vs', 'Variabilni symbol:', null, 10);
        $form->addText('ks', 'Konstantni symbol:', null, 10);
        $form->addSelect('forma_uhrady', 'Forma uhrady:', $this->formaUhradyModel->fetchPairs())
            ->addRule(NetteForm::FILLED);

        $polozky = $form->addContainer('polozky');
        for ($i = 0; $i < 10; $i++) {
            $this->makeInvoiceItemFormField($polozky, $i);
        }
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');
        return $form;
    }

    private function makeInvoiceItemFormField(Container $container, int $id): void
    {
        $item = $container->addContainer('polozka_' . $id);
        $item->addText('nazev', 'Nazev:', null, 250);
        $item->addText('pocet_polozek', 'Pocet polozek:', null, 5)
            ->setType('number')
            ->addRule(NetteForm::INTEGER)
            ->addRule(NetteForm::RANGE, null, array(0, 999));
        $item->addSelect('jednotka', 'Jednotka:', $this->jednotkaModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $item->addSelect('dph', 'DPH:', $this->dphModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $item->addSelect('cssclass', 'css:')
            ->setItems(array(
                'faktura-polozka',
                'faktura-nadpis'
            ), false);
        $item->addText('cena', 'Cena:', null, 10)
            ->addRule(NetteForm::FLOAT);
        unset($item);
    }
}
