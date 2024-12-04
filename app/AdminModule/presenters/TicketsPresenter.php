<?php

/**
 * Description of IncPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Components\WorkLog\WorkLogControl;
use App\Factory\Components\WorkLogControlFactory;
use App\Factory\Forms\TicketAddFormFactory;
use App\Factory\Forms\TicketEditFormFactory;
use App\Factory\Grids\IncidentDataGridFactory;
use App\Model\IncidentLogModel;
use App\Model\IncidentModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Tracy\Debugger;
use Nette\Forms\Form;
use Nette\InvalidArgumentException;
use Nette\Utils\Strings;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class TicketsPresenter extends AdminbasePresenter
{
    private IncidentModel $incidentModel;
    private IncidentLogModel $modelIncWl;
    private TicketAddFormFactory $ticketAddFormFactory;
    private TicketEditFormFactory $ticketEditFormFactory;
    private IncidentDataGridFactory $incidentDataGrid;
    private WorkLogControlFactory $workLogControlFactory;

    public function __construct(
        IncidentModel           $incidentModel,
        IncidentLogModel        $incidentLogModel,
        TicketAddFormFactory    $ticketAddFormFactory,
        TicketEditFormFactory   $ticketEditFormFactory,
        IncidentDataGridFactory $incidentDataGrid,
        WorkLogControlFactory   $workLogControlFactory
    )
    {
        parent::__construct();
        $this->incidentModel = $incidentModel;
        $this->modelIncWl = $incidentLogModel;
        $this->ticketAddFormFactory = $ticketAddFormFactory;
        $this->ticketEditFormFactory = $ticketEditFormFactory;
        $this->incidentDataGrid = $incidentDataGrid;
        $this->workLogControlFactory = $workLogControlFactory;
    }

    // ************************************** PART CREATE COMPONENTS *************************************

    /**
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        return $this->incidentDataGrid->create();
    }

    /*************************************** PART HANDLE DEFAULT VALUE **************************************/

    public function renderDefault()
    {
        $this->setView('../_default');
    }

    /*************************************** PART ADD **************************************/

    protected function createComponentAdd(): Form
    {
        $form = $this->ticketAddFormFactory->create($this->getUser()->getId());
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortException
     * @throws BadRequestException
     */
    public function add(Form $form)
    {
        try {
            $v = $form->getValues();
            $this->incidentModel->insertNewItem($v);
            $this->presenter->flashMessage('Nový záznam byl přidán');
            $this->presenter->redirect('edit', $this->incidentModel->getLastId());
        } catch (InvalidArgumentException $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
    }

    /*************************************** PART EDIT **************************************/

    //for load work load
    protected function createComponentWl(): WorkLogControl
    {
        return $this->workLogControlFactory->create();
    }

    //component for edit new item
    protected function createComponentEditTiket(): Form
    {
        $form = $this->ticketEditFormFactory->create();
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    //grid child tickets
    protected function createComponentChildTaskList(string $parentId): DataGrid
    {
//        is_numeric($parentId) ;
        return $this->incidentDataGrid->createTicketChildTask((int)$parentId);
    }

    /**
     * @param int $id cislo tiketu
     * @throws BadRequestException
     */
    public function actionEdit(int $id)
    {
//        $this->cssFiles->addFile('incForm.css');
        try {

            $this->template->incId = $id;
            // nacitam data pro formular
            $ticket = $this->incidentModel->fetchWith3thPartyTable($id);
            //Pokud je inciden ve stavu vyresen, nebo uzavren neni mozne formular odeslat ke zpracovani

            // if ($ticket['incident_stav'] >= 4):
            // $this['editTiket']['btSbmt']->setDisabled();
            // endif;

            //nactu work-log
            $this->template->wl = $this->modelIncWl->fetchAllByIncidentId($id);
            $this->template->pocetPotomku = $ticket['pocetPotomku'];
            //odeberu idecko z pole
            $ticket->offsetUnset('id');
            //upravene hodnoty odeslu do formulare
            $this['editTiket']->setDefaults(array('id' => $id, 'new' => $ticket));
        } catch (InvalidArgumentException $exc) {
            // zapisu chybu do logy
            Debugger::log($exc->getMessage());
            #$this->presenter->flashMessage($exc->getMessage());
            throw new BadRequestException();
        }
    }

    /**
     * @throws AbortException
     */
    public function edit(Form $form)
    {
        try {
            /**
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
            $this->incidentModel->updateItem($v['new'], $v['id']);
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
            $this->incidentModel->fetchById($id);
            $this->incidentModel->remove($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('Tickets:default'); //change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('Tickets:default'); //change it !!!
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Tickets:default'); //change it !!!
        }
    }
}
