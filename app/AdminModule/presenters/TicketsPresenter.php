<?php

/**
 * Description of IncPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Model\IncidentLogModel;
use App\Model\IncidentModel;
use DibiException;
use Gridy\Admin\IncidentGrid;
use Gridy\TiketChildTaskGrid;
use App\Form\Admin\Add;
use App\Form\Admin\Edit;
use App\Model;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Database\Table\Selection;
use Nette\DateTime;
use Nette\Diagnostics\Debugger;
use Nette\Forms\Form;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;
use Portal\WorkLog\WorkLogControl;

class TicketsPresenter extends AdminbasePresenter
{
    /** @var IncidentModel */
    private $model;

    /** @var IncidentLogModel */
    private $modelIncWl;

    /** @var Selection */
    private $gridDb;

    /** @var Selection */
    private $childTaskDB;

    public function __construct()
    {
        parent::__construct();
        $this->model = new IncidentModel;
        $this->modelIncWl = new IncidentLogModel;
    }

    public function startup()
    {
        parent::startup();
        $this->gridDb = $this->context->database->context;
        $this->childTaskDB = $this->context->database->context->table('incident');
    }

    /*************************************** PART CREATE COMPONENTS **************************************/

    //	default grid
    protected function createComponentGrid()
    {
        return new IncidentGrid($this->gridDb);
    }

    /*************************************** PART HANDLE DEFAULT VALUE **************************************/

    public function renderDefault()
    {
        $this->setView('../_default');
    }

    /*************************************** PART ADD **************************************/

    protected function createComponentAdd()
    {
        $form = new Add\IncidentForm;
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    public function actionAdd()
    {
        $this->cssFiles->addFile('incForm.css');
        $this->setView('../_add');

        //	nastavim vychozi hodnoty pro furmular
        $this['add']->setDefaults(array(
            'osoba_vytvoril' => $this->userId,
            'priorita' => 3, // nastavim normalni prioritu
            'ovlivneni' => 2, // normalni
        ));
    }


    /**
     * @throws AbortException
     */
    public function add(Add\IncidentForm $form)
    {
        try {
            $v = $form->getValues();
            $v->offsetSet('datum_vytvoreni', new DateTime);
            #$v->offsetSet('osoba_vytvoril', $this->identity->getId());
            $v->offsetSet('incident_stav', 1);
            $this->model->insert($v);
            $this->presenter->flashMessage('Nový záznam byl přidán');
            $this->presenter->redirect('edit', $this->model->fetchLastItem());
        } catch (InvalidArgumentException $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
    }

    /*************************************** PART EDIT **************************************/

    //	for load work load
    protected function createComponentWl()
    {
        return new WorkLogControl();
    }

    //	component for edit new item
    protected function createComponentEditTiket()
    {
        $form = new Edit\IncidentForm;
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    //	grid child tickets
    protected function createComponentChildTaskList()
    {
        return new TiketChildTaskGrid($this->childTaskDB);
    }

    /**
     * @param int $id cislo tiketu
     * @throws BadRequestException
     */
    public function actionEdit($id)
    {
        $this->cssFiles->addFile('incForm.css');
        try {

            $this->template->incId = $id;
            //	podminka pro zobrazeni tiketu s potomkama
            $this->childTaskDB->where('incident = ?', $id);
            $this['childTaskList']->setIncident($id);
            $this['editTiket']['new']['idTxt']
                ->setAttribute('readonly', 'readonly');
            $this['editTiket']['new']['firma_nazev']
                ->setAttribute('readonly', 'readonly');
            $this['editTiket']['new']['ci']
                ->setItems(Model\CiModel::fetchAllPairsWithCompanyName());
            $this['editTiket']['new']['fronta_osoba']
                ->setItems(Model\FrontaOsobaModel::fetchSpecialistPairsWithQueueName())
                ->setPrompt(' - - - ');
            $this['editTiket']['new']['ukon']
                ->setItems(Model\UkonModel::fetchPairs())
                ->setPrompt(' - - - ');
            $this['editTiket']['new']['ovlivneni']
                ->setItems(Model\OvlivneniModel::fetchPairs())
                ->setPrompt(' - - - ');
            $this['editTiket']['new']['zpusob_uzavreni']
                ->setItems(Model\ZpusobUzavreniModel::fetchPairs())
                ->setPrompt(' - - - ');
            $this['editTiket']['new']['typ_incident']
                ->setItems(Model\TypIncidentModel::fetchPairs())
                ->setPrompt(' - - - ')
                ->addRule(Form::FILLED);
            $this['editTiket']['new']['priorita']
                ->setItems(Model\PrioritaModel::fetchPairs())
                ->addRule(Form::FILLED);
            $this['editTiket']['new']['incident_stav']
                ->setItems(Model\IncidentStavModel::fetchPairs())
                ->addRule(Form::FILLED);
            $this['editTiket']['new']['osoba_vytvoril']
                ->setItems(Model\OsobaModel::fetchAllPairsWithCompanyName())
                ->addRule(Form::FILLED);
            //	pokud je nastaven stav na vyresen je potreba vybrat zpusob uzavreni
            $this['editTiket']['new']['zpusob_uzavreni']
                ->addConditionOn($this['editTiket']['new']['incident_stav'], Form::EQUAL, 4)
                ->addRule(Form::FILLED);
            //	pokud vyberu zpusob uzavreni pak je potreba neco napsat do oduvodneni
            $this['editTiket']['new']['obsah_uzavreni']
                ->addConditionOn($this['editTiket']['new']['zpusob_uzavreni'], Form::MIN_LENGTH, 1)
                ->addRule(Form::FILLED)
                ->addConditionOn($this['editTiket']['new']['incident_stav'], Form::EQUAL, 4)
                ->addRule(Form::FILLED);
            // pokud vyberru zpusob uzavreni je potreba vybrat take ukon ktery byl proveden
            $this['editTiket']['new']['ukon']
                ->addConditionOn($this['editTiket']['new']['zpusob_uzavreni'], Form::MIN_LENGTH, 1)
                ->addRule(Form::FILLED);
            // pokud vyberu zpusob uzavreni je potreba vybrat take ovlivneni
            $this['editTiket']['new']['ovlivneni']
                ->addConditionOn($this['editTiket']['new']['zpusob_uzavreni'], Form::MIN_LENGTH, 1)
                ->addRule(Form::FILLED);
            // nacitam data pro formular
            $v = $this->model->fetchWith3thPartyTable($id);
            //	Pokud je inciden ve stavu vyresen, nebo uzavren neni mozne formular odeslat ke zpracovani

            // if ($v['incident_stav'] >= 4):
            // 	$this['editTiket']['btSbmt']->setDisabled();
            // endif;

            //	nactu work-log
            $this->template->wl = $this->modelIncWl->fetchAllByIncidentId($id);
            $this->template->pocetPotomku = $v['pocetPotomku'];
            //	odeberu idecko z pole
            $v->offsetUnset('id');
            //	upravene hodnoty odeslu do formulare
            $this['editTiket']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            // zapisu chybu do logy
            Debugger::log($exc->getMessage());
            #$this->presenter->flashMessage($exc->getMessage());
            throw new BadRequestException();
        }
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function edit(Edit\IncidentForm $form)
    {
        try {
            /*
             * Nactu si data odeslana z formulare do promenne $v a pro potreby
             * porovnavani zmeny stavu nactu take data z databaze a ulozim
             * do promenne $dbData.
             */
            $v = $form->getValues();
            $v['new']->offsetUnset('fronta');
            $v['new']->offsetSet('identity', $this->identity->getId());
            if (!empty($v['new']['wl'])):
                $v['new']->offsetSet('wl', '**Popis činnosti:** <br />' . Strings::trim($v['new']['wl']));
            endif;
            $this->model->update($v['new'], $v['id']);
            $this->presenter->flashMessage('Záznam byl úspěšně změněn');
            $this->redirect('edit', $v['id']);
        } catch (InvalidArgumentException $exc) {
            $form->addError($exc->getMessage());
            Debugger::log($exc->getMessage());
        }
    }

    /*************************************** PART DROP **************************************/

    /**
     * @param int $id Identifikator polozky
     * @throws AbortException
     */
    public function actionDrop($id)
    {
        try {
            $this->model->fetch($id);
            $this->model->remove($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('Tickets:default'); //	change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('Tickets:default'); //	change it !!!
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Tickets:default'); //	change it !!!
        }
    }
}