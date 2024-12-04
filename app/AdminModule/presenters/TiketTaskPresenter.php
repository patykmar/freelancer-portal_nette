<?php

/**
 * This presenter handle actions about create ticket tasks.
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\TicketTaskFormFactory;
use App\Model\IncidentModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Tracy\Debugger;

class TiketTaskPresenter extends AdminbasePresenter
{
    private IncidentModel $incidentModel;
    private TicketTaskFormFactory $ticketTaskFormFactory;

    public function __construct(
        IncidentModel         $incidentModel,
        TicketTaskFormFactory $ticketTaskFormFactory
    )
    {
        parent::__construct();
        $this->incidentModel = $incidentModel;
        $this->ticketTaskFormFactory = $ticketTaskFormFactory;
    }

    /**
     * @throws AbortException
     */
    public function actionDefault()
    {
        // redirect to tickets default
        $this->redirect(':Admin:Tickets:');
    }

    /*************************************** PART ADD *************************************
     * @throws BadRequestException
     */

    public function renderAdd(int $id)
    {
        $this->setView('../_add');
        $v = $this->incidentModel->fetchById($id);

        // nastavim cislo rodicovskeho tiketu
        $v->offsetSet('incident', $v['id']);

        // odeberu nepotrebne udaje
        $v->offsetUnset('maly_popis');
        $v->offsetUnset('obsah');

        // naplnim formular hodnotami rodice
        $this['add']->setDefaults($v);
    }

    public function createComponentAdd(): Form
    {
        $form = $this->ticketTaskFormFactory->create($this->getUser()->getId());
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    public function add(Form $form)
    {
        try {
            $v = $form->getValues();

            $v->offsetSet('datum_vytvoreni', new DateTime);
            $v->offsetSet('osoba_vytvoril', $this->identity->getId());
            $v->offsetSet('incident_stav', 1);    // stav: otevren
            $v->offsetSet('typ_incident', 3);    // ITASK

            $this->incidentModel->insertNewItem($v);
            $this->flashMessage('Nový záznam byl přidán');
            $this->presenter->redirect('Tickets:edit', $this->incidentModel->getLastId());
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
    }
}
