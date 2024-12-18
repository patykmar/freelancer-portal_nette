<?php

/**
 * Description of TypIncidentPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\TypIncidentFormFactory;
use App\Factory\Grids\TypIncidentDataGridFactory;
use App\Model\TypIncidentModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Tracy\ILogger;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class TypIncidentPresenter extends AdminbasePresenter
{
    private TypIncidentModel $typIncidentModel;
    private TypIncidentFormFactory $typIncidentFormFactory;
    private TypIncidentDataGridFactory $typIncidentDataGrid;

    public function __construct(
        TypIncidentModel       $typIncidentModel,
        TypIncidentFormFactory $typIncidentFormFactory,
        TypIncidentDataGridFactory $typIncidentDataGrid
    )
    {
        parent::__construct();
        $this->typIncidentModel = $typIncidentModel;
        $this->typIncidentFormFactory = $typIncidentFormFactory;
        $this->typIncidentDataGrid = $typIncidentDataGrid;
    }

    /**
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        return $this->typIncidentDataGrid->create();
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

    public function createComponentAdd(): Form
    {
        $form = $this->typIncidentFormFactory->create();
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(Form $form)
    {
        try {
            $v = $form->getValues();
            $v->offsetUnset('id');
            $this->typIncidentModel->insertNewItem($v);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage(), ILogger::ERROR);
            $form->addError('Nový záznam nebyl přidán');
        }
        $this->flashMessage('Nový záznam byl přidán');
        $this->redirect('default');
    }

    /*************************************** PART EDIT **************************************/

    /**
     * @param int $id Identifikator polozky
     * @throws AbortException
     * @throws BadRequestException
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            //upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults($this->typIncidentModel->fetchById($id));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Form
    {
        $form = $this->typIncidentFormFactory->create();
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(Form $form)
    {
        try {
            $v = $form->getValues();
            $this->typIncidentModel->updateItem($v, $v['id']);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage(), ILogger::ERROR);
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
            $this->typIncidentModel->fetchById($id);
            $this->typIncidentModel->removeItem($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('TypIncident:default');    //change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('TypIncident:default');    //change it !!!
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('TypIncident:default');    //change it !!!
        }
    }
}
