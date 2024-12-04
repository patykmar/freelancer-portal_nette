<?php

/**
 * Description of TimeZonePresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\TimeZoneFormFactory;
use App\Factory\Grids\SimpleDataGridFactory;
use App\Model\TimeZoneModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class TimeZonePresenter extends AdminbasePresenter
{
    private TimeZoneModel $timeZoneModel;
    private TimeZoneFormFactory $timeZoneFormFactory;
    private SimpleDataGridFactory $gridFactory;

    public function __construct(
        TimeZoneModel           $timeZoneModel,
        TimeZoneFormFactory     $timeZoneFormFactory,
        SimpleDataGridFactory $gridFactory
    )
    {
        parent::__construct();
        $this->timeZoneModel = $timeZoneModel;
        $this->timeZoneFormFactory = $timeZoneFormFactory;
        $this->gridFactory = $gridFactory;
    }

    /**
     * Cast DEFAULT, definice Gridu
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        return $this->gridFactory->createTimeZoneDataGrid();
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
        $form = $this->timeZoneFormFactory->create();
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
            $this->timeZoneModel->insertNewItem($v);
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
            // nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->timeZoneModel->fetchById($id);

            // odeberu idecko z pole
//            $v->offsetUnset('id');

            // upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults($v);
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Form
    {
        $form = $this->timeZoneFormFactory->create();
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
            $this->timeZoneModel->updateItem($v, $v['id']);
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
            $this->timeZoneModel->fetchById($id);
            $this->timeZoneModel->removeItem($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('TimeZone:default');    // change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('TimeZone:default');    // change it !!!
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('TimeZone:default');    // change it !!!
        }
    }
}
