<?php

/**
 * Description of IncidentStavPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\FkGrid;
use App\Model\IncidentStavModel;
use DibiException;
use Exception;
use App\Form\Admin\Add\FkBaseForm as AddFkBaseForm;
use App\Form\Admin\Edit\FkBaseForm as EditFkBaseForm;
use Nette\Application\AbortException;
use Nette\Database\Context;
use Nette\Database\IRow;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

class IncidentStavPresenter extends AdminbasePresenter
{
    /** @var string */
    private $tableName = 'incident_stav';

    /** @var IncidentStavModel */
    private $model;

    /** @var Context */
    private $netteModel;

    /**
     * @param Context $context
     * @param IncidentStavModel $model
     */
    public function __construct(Context $context, IncidentStavModel $model)
    {
        parent::__construct();
        $this->netteModel = $context;
        $this->model = $model;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid(): FkGrid
    {
        return new FkGrid($this->netteModel->table($this->tableName));
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

    public function createComponentAdd(): AddFkBaseForm
    {
        $form = new AddFkBaseForm;
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    /**
     * @throws AbortException
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
     * @throws AbortException
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            /** @var bool|IRow $v nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji */
            $v = $this->model->fetch($id);

            // upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): EditFkBaseForm
    {
        $form = new EditFkBaseForm;
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    /**
     * @throws AbortException
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
     * @throws AbortException
     */
    public function actionDrop($id)
    {
        try {
            try {
                $this->model->fetch($id);
                $this->model->remove($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('IncidentStav:default');    // change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('IncidentStav:default');    // change it !!!
            }
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('IncidentStav:default');    // change it !!!
        }
    }
}
