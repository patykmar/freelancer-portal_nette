<?php

/**
 * Description of TypChangePresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule;

use App\Model\TypChangeModel;
use DibiException;
use Exception;
use Gridy\FkGrid;
use MyForms\Admin\Add\FkBaseForm as AddFkBaseForm;
use MyForms\Admin\Edit\FkBaseForm as EditFkBaseForm;
use Nette\Application\AbortException;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

class TypChangePresenter extends AdminbasePresenter
{
    /** @var TypChangeModel */
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new TypChangeModel;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        return new FkGrid($this->context->database->context->table('typ_change'));
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
     * @throws AbortException
     */
    public function edit(EditFkBaseForm $form)
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
            $this->redirect('TypChange:default');    //	change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('TypChange:default');    //	change it !!!
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('TypChange:default');    //	change it !!!
        }
    }
}