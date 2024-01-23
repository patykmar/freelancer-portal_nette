<?php

namespace App\Factory\Forms;

use App\Model\CiModel;
use App\Model\FrontaOsobaModel;
use App\Model\IncidentStavModel;
use App\Model\OsobaModel;
use App\Model\OvlivneniModel;
use App\Model\PrioritaModel;
use App\Model\TypIncidentModel;
use App\Model\UkonModel;
use App\Model\ZpusobUzavreniModel;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Form as FormAlias;

class TicketEditFormFactory
{
    private FormFactory $formFactory;
    private CiModel $ciModel;
    private FrontaOsobaModel $frontaOsobaModel;
    private UkonModel $ukonModel;
    private OvlivneniModel $ovlivneniModel;
    private ZpusobUzavreniModel $zpusobUzavreniModel;
    private TypIncidentModel $typIncidentModel;
    private PrioritaModel $prioritaModel;
    private IncidentStavModel $incidentStavModel;
    private OsobaModel $osobaModel;

    public function __construct(
        FormFactory         $formFactory,
        CiModel             $ciModel,
        FrontaOsobaModel    $frontaOsobaModel,
        UkonModel           $ukonModel,
        OvlivneniModel      $ovlivneniModel,
        ZpusobUzavreniModel $zpusobUzavreniModel,
        TypIncidentModel    $typIncidentModel,
        PrioritaModel       $prioritaModel,
        IncidentStavModel   $incidentStavModel,
        OsobaModel          $osobaModel
    )
    {
        $this->formFactory = $formFactory;
        $this->ciModel = $ciModel;
        $this->frontaOsobaModel = $frontaOsobaModel;
        $this->ukonModel = $ukonModel;
        $this->ovlivneniModel = $ovlivneniModel;
        $this->zpusobUzavreniModel = $zpusobUzavreniModel;
        $this->typIncidentModel = $typIncidentModel;
        $this->prioritaModel = $prioritaModel;
        $this->incidentStavModel = $incidentStavModel;
        $this->osobaModel = $osobaModel;
    }

    public function create(): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('id');
        $new = $form->addContainer('new');
        $new->addText('idTxt', 'Incident:');
        $new->addText('firma_nazev', 'Firma:');
        $new->addText('maly_popis', 'Popis:');
        $new->addSelect('typ_incident', 'Typ incidentu: ', $this->typIncidentModel->fetchPairs());
        $new->addSelect('priorita', 'Priorita:', $this->prioritaModel->fetchPairs());
        $new->addSelect('incident_stav', 'Stav incidentu:', $this->incidentStavModel->fetchPairs());
        $new->addSelect('fronta_osoba', 'Přiřazeno:', $this->frontaOsobaModel->fetchSpecialistPairsWithQueueName());
        $new->addSelect('ukon', 'Služba:', $this->ukonModel->fetchPairs());
        $new->addSelect('ovlivneni', 'Ovlivnění:', $this->ovlivneniModel->fetchPairs());
        $new->addSelect('ci', 'Produkt:', $this->ciModel->fetchAllPairsWithCompanyName());
        $new->addSelect('osoba_vytvoril', 'Vytvořil:', $this->osobaModel->fetchAllPairsWithCompanyName());
        $new->addSelect('zpusob_uzavreni', 'Způsob uzavření:', $this->zpusobUzavreniModel->fetchPairs());
        $new->addText('fronta', 'Fronta:')
            ->setAttribute('readonly', 'readonly');
        $new->addTextArea('obsah', 'Popis požadavku:');
        $new->addText('datum_vytvoreni', 'Vytvořeno:')
            ->setAttribute('readonly', 'readonly');
        $new->addText('datum_ukonceni', 'Dokončení:')
            ->setAttribute('readonly', 'readonly');
        $new->addText('datum_reakce', 'Reakce:')
            ->setAttribute('readonly', 'readonly');
        $new->addTextArea('wl', 'Záznam práce:');
        $new->addTextArea('obsah_uzavreni', 'Odůvodnění:');

        $this->addConditions($new);

        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');

        return $form;
    }

    private function addConditions(Container $container): void
    {
        $container['idTxt']
            ->setAttribute('readonly', 'readonly');
        $container['firma_nazev']
            ->setAttribute('readonly', 'readonly');
        $container['typ_incident']
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addRule(FormAlias::FILLED);
        $container['priorita']
            ->addRule(FormAlias::FILLED);
        $container['incident_stav']
            ->addRule(FormAlias::FILLED);
        $container['ovlivneni']
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        // pokud vyberu zpusob uzavreni je potreba vybrat take ukon ktery byl proveden
        $container['ukon']
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addConditionOn($container['zpusob_uzavreni'], FormAlias::MIN_LENGTH, 1)
            ->addRule(FormAlias::FILLED);
        $container['ovlivneni']
            ->addConditionOn($container['zpusob_uzavreni'], FormAlias::MIN_LENGTH, 1)
            ->addRule(FormAlias::FILLED);
        //pokud je nastaven stav na vyresen je potreba vybrat zpusob uzavreni
        $container['zpusob_uzavreni']
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addConditionOn($container['incident_stav'], FormAlias::EQUAL, 4);
        //pokud vyberu zpusob uzavreni pak je potreba neco napsat do oduvodneni
        $container['obsah_uzavreni']
            ->addConditionOn($container['zpusob_uzavreni'], FormAlias::MIN_LENGTH, 1)
            ->addRule(FormAlias::FILLED)
            ->addConditionOn($container['incident_stav'], FormAlias::EQUAL, 4)
            ->addRule(FormAlias::FILLED);
        $container['obsah']
            ->addRule(FormAlias::FILLED);
        $container['fronta_osoba']
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $container['osoba_vytvoril']
            ->addRule(FormAlias::FILLED);

    }

}
