<?php

namespace App\Factory\Forms;

use App\Model\CiModel;
use App\Model\OsobaModel;
use App\Model\OvlivneniModel;
use App\Model\PrioritaModel;
use App\Model\TypIncidentModel;
use App\Model\UkonModel;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;
use Nette\Utils\DateTime;

class TicketAddFormFactory
{
    private const PRIORITY_NORMAL = 3;
    private const IMPACT_NORMAL = 2;
    private const TICKET_STATE_OPEN = 1;

    private FormFactory $formFactory;
    private OsobaModel $osobaModel;
    private TypIncidentModel $typIncidentModel;
    private PrioritaModel $prioritaModel;
    private OvlivneniModel $ovlivneniModel;
    private CiModel $ciModel;
    private UkonModel $ukonModel;

    public function __construct(
        FormFactory      $formFactory,
        OsobaModel       $osobaModel,
        TypIncidentModel $typIncidentModel,
        PrioritaModel    $prioritaModel,
        OvlivneniModel   $ovlivneniModel,
        CiModel          $ciModel,
        UkonModel        $ukonModel
    )
    {
        $this->formFactory = $formFactory;
        $this->osobaModel = $osobaModel;
        $this->typIncidentModel = $typIncidentModel;
        $this->prioritaModel = $prioritaModel;
        $this->ovlivneniModel = $ovlivneniModel;
        $this->ciModel = $ciModel;
        $this->ukonModel = $ukonModel;
    }

    public function create(int $userId): Form
    {
        $form = $this->formFactory->create();
        $form->addHidden('incident_stav');
        $form->addHidden('datum_vytvoreni');
        $form->addSelect('osoba_vytvoril', 'Vytvořil:', $this->osobaModel->fetchAllPairs())
            ->addRule(FormAlias::FILLED);
        $form->addSelect('typ_incident', 'Typ tiketu:', $this->typIncidentModel->fetchPairs())
            ->addRule(FormAlias::FILLED);
        $form->addSelect('priorita', 'Priorita:', $this->prioritaModel->fetchPairs())
            ->addRule(FormAlias::FILLED);
        $form->addSelect('ovlivneni', 'Ovlivnění:', $this->ovlivneniModel->fetchPairs())
            ->setPrompt(IForm::INPUT_SELECT_PROMPT);
        $form->addSelect('ci', 'Produkt:', $this->ciModel->fetchPairs())
            ->addRule(FormAlias::FILLED);
        $form->addSelect('ukon', 'Služba:', $this->ukonModel->fetchPairs())
            ->setPrompt(' - - - ');
        $form->addText('maly_popis', 'Malý popis:', null, 100)
            ->addRule(FormAlias::FILLED);
        $form->addTextArea('obsah', 'Popis požadavku')
            ->addRule(FormAlias::FILLED);
        // Obrana před Cross-Site Request Forgery (CSRF)
        $form->addProtection('Vypršel časový limit, odešlete formulář znovu');
        // Tlacitko odeslat
        $form->addSubmit('btSbmt', 'Ulož');

        //nastavim vychozi hodnoty pro furmular
        $form->setDefaults([
            'osoba_vytvoril' => $userId,
            'priorita' => self::PRIORITY_NORMAL,
            'ovlivneni' => self::IMPACT_NORMAL,
            'incident_stav' => self::TICKET_STATE_OPEN,
            'datum_vytvoreni' => new DateTime
        ]);
        return $form;
    }
}
