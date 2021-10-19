<?php

/**
 * This presenter handle actions about create ticket tasks.
 *
 * @author Martin Patyk
 */

namespace App\AdminModule;

use Exception;
use App\Form\Admin\Add\TaskForm;
use App\Model;
use Nette\Application\AbortException;
use Nette\ArrayHash;
use Nette\DateTime;
use Nette\Diagnostics\Debugger;

class TiketTaskPresenter extends AdminbasePresenter
{
    /** @var Model\IncidentModel */
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Model\IncidentModel;
    }

    /**
     * @throws AbortException
     */
    public function actionDefault()
    {
        //	redirect to tickets default
        $this->redirect(':Admin:Tickets:');
    }

    /*************************************** PART ADD **************************************/

    public function renderAdd($id)
    {
        $this->setView('../_add');
        /** @var ArrayHash hodnoty rodicovskeho tikety */
        $v = $this->model->fetch($id);

        //	nastavim cislo rodicovskeho tiketu
        $v->offsetSet('incident', $v['id']);

        //	odeberu nepotrebne udaje
        $v->offsetUnset('maly_popis');
        $v->offsetUnset('obsah');

        //	naplnim formular hodnotami rodice
        $this['add']->setDefaults($v);
    }

    public function createComponentAdd()
    {
        $form = new TaskForm();
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    public function add(TaskForm $form)
    {
        try {
            $v = $form->getValues();

            $v->offsetSet('datum_vytvoreni', new DateTime);
            $v->offsetSet('osoba_vytvoril', $this->identity->getId());
            $v->offsetSet('incident_stav', 1);    // stav: otevren
            $v->offsetSet('typ_incident', 3);    // ITASK

            $this->model->insert($v);
            $this->flashMessage('Nový záznam byl přidán');
            $this->presenter->redirect('Tickets:edit', $this->model->fetchLastItem());
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
    }
}