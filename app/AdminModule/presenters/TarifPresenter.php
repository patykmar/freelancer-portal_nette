<?php

/**
 * Description of TarifPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule;

use App\Model\TarifModel;
use DibiException;
use Gridy\Admin\TarifGrid;
use App\Form\Admin\Add\TarifForm as AddTarifForm;
use App\Form\Admin\Edit\TarifForm as EditTarifForm;
use Nette\Application\AbortException;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;


class TarifPresenter extends AdminbasePresenter
{
    /** @var TarifModel */
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new TarifModel;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        return new TarifGrid($this->context->database->context->table('tarif'));
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
        $form = new AddTarifForm();
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(AddTarifForm $form)
    {
        try {
            $v = $form->getValues();
            //	vlozim novy tarif do databaze
            $this->model->insert($v);
            $this->flashMessage('Nový záznam byl přidán');
            $this->redirect('default');
        } catch (InvalidArgumentException $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
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
        $form = new EditTarifForm();
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(EditTarifForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->update($v['new'], $v['id']);
            $this->flashMessage('Záznam byl úspěšně změněn');
            $this->redirect('default');
        } catch (DibiException $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Záznam nebyl změněn');
        }
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
            $this->redirect('Tarif:default'); //	change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('Tarif:default'); //	change it !!!

        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Tarif:default'); //	change it !!!
        }
    }
}