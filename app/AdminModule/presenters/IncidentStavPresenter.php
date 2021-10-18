<?php

/**
 * Description of IncidentStavPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule;

use App\Model\IncidentStavModel;
use DibiException;
use Exception;
use Gridy\FkGrid;
use MyForms\Admin\Add\FkBaseForm as AddFkBaseForm;
use MyForms\Admin\Edit\FkBaseForm as EditFkBaseForm;
use Nette\DI\Container;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

class IncidentStavPresenter extends AdminbasePresenter
{
    /** @var IncidentStavModel */
    private $model;

    /**
     * @param Container $context
     */
    public function __construct(Container $context)
    {
        parent::__construct($context);
        $this->model = new IncidentStavModel;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        return new FkGrid($this->context->database->context->table('incident_stav'));
    }

    public function renderDefault()
    {
        $this->setView('../_default');
    }

    /*************************************** PART ADD **************************************/
    
    public function renderAdd()
    {
        $this->setView('../_add');
    }

    public function createComponentAdd()
    {
        $form = new AddFkBaseForm;
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function add(AddFkBaseForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->insert($v);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
        $this->flashMessage('Nový záznam byl přidán');
        $this->redirect('default');
    }

    /*************************************** PART EDIT **************************************/

    /**
     * @param int $id Identifikator polozky
     */
    public function renderEdit($id)
    {
        try {
            $this->setView('../_edit');
            //	nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->model->fetch($id);

            //	odeberu idecko z pole
            $v->offsetUnset('id');

            //	upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit()
    {
        $form = new EditFkBaseForm;
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function edit(EditFkBaseForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->update($v['new'], $v['id']);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Záznam nebyl změněn');
        }
        $this->flashMessage('Záznam byl úspěšně změněn');
        $this->redirect('default');
    }

    /*************************************** PART DROP **************************************/

    /**
     * @param int $id Identifikator polozky
     * @throws \Nette\Application\AbortException
     */
    public function actionDrop($id)
    {
        try {
            try {
                $this->model->fetch($id);
                $this->model->remove($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('IncidentStav:default');    //	change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('IncidentStav:default');    //	change it !!!
            }
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('IncidentStav:default');    //	change it !!!
        }
    }
}