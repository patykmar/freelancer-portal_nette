<?php

/**
 * Description of UkonPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule;

use App\Model\UkonModel;
use DibiException;
use Gridy\Admin\UkonGrid;
use App\Form\Admin\Edit\UkonForm as EditUkonForm;
use App\Form\Admin\Add\UkonForm as AddUkonForm;
use Nette\Application\AbortException;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

class UkonPresenter extends AdminbasePresenter
{

    /** @var UkonModel */
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new UkonModel();
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        return new UkonGrid($this->context->database->context->table('ukon'));
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
        $this['add']->setDefaults(
            array(
                'cas_realizace' => '5184000',    //	2 mesice
                'cas_reakce' => '2592000',    // 1 mesic
            )
        );
    }

    public function createComponentAdd()
    {
        $form = new AddUkonForm;
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(AddUkonForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->insert($v);
        } catch (DibiException $exc) {
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
        $form = new EditUkonForm;
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(EditUkonForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->update($v['new'], $v['id']);
        } catch (DibiException $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Záznam nebyl změněn');
        }
        $this->flashMessage('Záznam byl úspěšně změněn');
        $this->redirect('default');
    }

    /**
     * Cast DROP
     * @param int $id Identifikator polozky
     * @throws AbortException
     */
    public function actionDrop($id)
    {
        try {
            $this->model->fetch($id);
            $this->model->remove($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('Ukon:default'); //	change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('Ukon:default'); //	change it !!!
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Ukon:default'); //	change it !!!
        }
    }
}