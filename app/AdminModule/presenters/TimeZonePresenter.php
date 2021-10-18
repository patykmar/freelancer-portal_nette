<?php

/**
 * Description of TimeZonePresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule;

use App\Model\TimeZoneModel;
use DibiException;
use Exception;
use Gridy\Admin\TimeZoneGrid;
use MyForms\Admin\Edit\TimeZoneForm as EditTimeZoneForm;
use MyForms\Admin\Add\TimeZoneForm as AddTimeZoneForm;
use Nette\Application\AbortException;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

class TimeZonePresenter extends AdminbasePresenter
{
    /** @var TimeZoneModel */
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new TimeZoneModel;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        return new TimeZoneGrid($this->context->database->context->table('time_zone'));
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
        $form = new AddTimeZoneForm();
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(AddTimeZoneForm $form)
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
        $form = new EditTimeZoneForm();
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(EditTimeZoneForm $form)
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
            $this->model->fetch($id);
            $this->model->remove($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('TimeZone:default');    //	change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('TimeZone:default');    //	change it !!!
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('TimeZone:default');    //	change it !!!
        }
    }
}