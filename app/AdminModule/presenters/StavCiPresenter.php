<?php

/**
 * Description of StavCiPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\ForeignKeyFormFactory;
use App\Factory\Grids\SimpleDataGridFactory;
use App\Model\StavCiModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Forms\Form;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class StavCiPresenter extends AdminbasePresenter
{
    private StavCiModel $stavCiModel;
    private SimpleDataGridFactory $simpleDataGridFactory;
    private ForeignKeyFormFactory $foreignKeyFormFactory;

    public function __construct(
        StavCiModel           $stavCiModel,
        SimpleDataGridFactory $simpleDataGridFactory,
        ForeignKeyFormFactory $foreignKeyFormFactory
    )
    {
        parent::__construct();
        $this->stavCiModel = $stavCiModel;
        $this->simpleDataGridFactory = $simpleDataGridFactory;
        $this->foreignKeyFormFactory = $foreignKeyFormFactory;
    }

    /**
     * Cast DEFAULT, definice Gridu
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        return $this->simpleDataGridFactory->createStavCi();
    }

    public function renderDefault()
    {
        $this->setView('../_default');
    }

    /**
     * Cast ADD
     */
    public function renderAdd()
    {
        $this->setView('../_add');
    }

    public function createComponentAdd(): Form
    {
        $form = $this->foreignKeyFormFactory->create();
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
            $this->stavCiModel->insertNewItem($v);
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
     * @throws BadRequestException
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            // upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults($this->stavCiModel->fetchById($id));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Form
    {
        $form = $this->foreignKeyFormFactory->create();
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
            $this->stavCiModel->updateItem($v, $v['id']);
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
            $this->stavCiModel->fetchById($id);
            $this->stavCiModel->removeItem($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('StavCi:default');    // change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('StavCi:default');    // change it !!!
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('StavCi:default');    // change it !!!
        }
    }
}
