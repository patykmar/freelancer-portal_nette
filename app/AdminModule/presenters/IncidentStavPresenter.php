<?php

/**
 * Description of IncidentStavPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\FkGrid;
use App\Model\IncidentStavModel;
use Exception;
use App\Forms\Admin\Add\FkBaseForm as AddFkBaseForm;
use App\Forms\Admin\Edit\FkBaseForm as EditFkBaseForm;
use Nette\Application\AbortException;
use Nette\Database\Context;
use Nette\Database\IRow;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class IncidentStavPresenter extends AdminbasePresenter
{
    private IncidentStavModel $incidentStavModel;
    private Context $netteModel;

    public function __construct(Context $context, IncidentStavModel $model)
    {
        parent::__construct();
        $this->netteModel = $context;
        $this->incidentStavModel = $model;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid(): FkGrid
    {
        return new FkGrid($this->netteModel->table(IncidentStavModel::TABLE_NAME));
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
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(AddFkBaseForm $form)
    {
        try {
            $v = $form->getValues();
            $this->incidentStavModel->insert($v);
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
            $v = $this->incidentStavModel->fetch($id);

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
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(EditFkBaseForm $form)
    {
        try {
            $v = $form->getValues();
            $this->incidentStavModel->update($v['new'], $v['id']);
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
    public function actionDrop(int $id)
    {
        try {
            try {
                $this->incidentStavModel->fetch($id);
                $this->incidentStavModel->removeItem($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('IncidentStav:default');    // change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('IncidentStav:default');    // change it !!!
            }
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('IncidentStav:default');    // change it !!!
        }
    }
}
