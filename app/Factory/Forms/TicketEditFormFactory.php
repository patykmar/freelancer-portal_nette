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
use Nette\Forms\Form as FormAlias;

class TicketEditFormFactory
{
    private readonly FormFactory $formFactory;
    private readonly CiModel $ciModel;
    private readonly FrontaOsobaModel $frontaOsobaModel;
    private readonly UkonModel $ukonModel;
    private readonly OvlivneniModel $ovlivneniModel;
    private readonly ZpusobUzavreniModel $zpusobUzavreniModel;
    private readonly TypIncidentModel $typIncidentModel;
    private readonly PrioritaModel $prioritaModel;
    private readonly IncidentStavModel $incidentStavModel;
    private readonly OsobaModel $osobaModel;

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
        $new->addText('idTxt', 'Incident:')
            ->setHtmlAttribute('readonly', 'readonly');
        $new->addText('firma_nazev', 'Firma:')
            ->setHtmlAttribute('readonly', 'readonly');
        $new->addText('maly_popis', 'Popis:');
        $new->addSelect('typ_incident', 'Typ incidentu: ', $this->typIncidentModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addRule(FormAlias::Filled);
        $new->addSelect('priorita', 'Priorita:', $this->prioritaModel->fetchPairs())
            ->addRule(FormAlias::Filled);
        $new->addSelect('incident_stav', 'Stav incidentu:', $this->incidentStavModel->fetchPairs())
            ->addRule(FormAlias::Filled);
        $new->addSelect('fronta_osoba', 'Přiřazeno:', $this->frontaOsobaModel->fetchSpecialistPairsWithQueueName())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $new->addSelect('ukon', 'Služba:', $this->ukonModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            // pokud vyberu zpusob uzavreni je potreba vybrat take ukon ktery byl proveden
            ->addConditionOn($new['zpusob_uzavreni'], FormAlias::MinLength, 1)
            ->addRule(FormAlias::Filled);
        $new->addSelect('ovlivneni', 'Ovlivnění:', $this->ovlivneniModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addRule(FormAlias::Filled)
            ->addConditionOn($new['zpusob_uzavreni'], FormAlias::MIN_LENGTH, 1);
        $new->addSelect('ci', 'Produkt:', $this->ciModel->fetchAllPairsWithCompanyName());
        $new->addSelect('osoba_vytvoril', 'Vytvořil:', $this->osobaModel->fetchAllPairsWithCompanyName())
            ->addRule(FormAlias::Filled);
        //pokud je nastaven stav na vyresen je potreba vybrat zpusob uzavreni
        $new->addSelect('zpusob_uzavreni', 'Způsob uzavření:', $this->zpusobUzavreniModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT)
            ->addConditionOn($new['incident_stav'], FormAlias::Equal, 4);
        $new->addText('fronta', 'Fronta:')
            ->setHtmlAttribute('readonly', 'readonly');
        $new->addTextArea('obsah', 'Popis požadavku:')
            ->addRule(FormAlias::Filled);
        $new->addText('datum_vytvoreni', 'Vytvořeno:')
            ->setHtmlAttribute('readonly', 'readonly');
        $new->addText('datum_ukonceni', 'Dokončení:')
            ->setHtmlAttribute('readonly', 'readonly');
        $new->addText('datum_reakce', 'Reakce:')
            ->setHtmlAttribute('readonly', 'readonly');
        $new->addTextArea('wl', 'Záznam práce:');
        //pokud vyberu zpusob uzavreni pak je potreba neco napsat do oduvodneni
        $new->addTextArea('obsah_uzavreni', 'Odůvodnění:')
            ->addConditionOn($new['zpusob_uzavreni'], FormAlias::MIN_LENGTH, 1)
            ->addRule(FormAlias::Filled)
            ->addConditionOn($new['incident_stav'], FormAlias::EQUAL, 4)
            ->addRule(FormAlias::Filled);

        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');

        return $form;
    }

}
