<?php

/**
 * Description of SlaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule;

use App\Model\SlaModel;
use Exception;
use Gridy\Admin\SlaGrid;
use MyForms\Admin\Edit;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;


class SlaPresenter extends AdminbasePresenter
{
    /** @var SlaModel */
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new SlaModel();
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        $id = $this->presenter->getParameter('id', null);
        if (isset($id)) {
            return new SlaGrid($this->context->database->context->table('sla')->where(array('tarif' => $id)));
        } else {
            return new SlaGrid($this->context->database->context->table('sla'));
        }
    }

    public function renderDefault($id = null)
    {
    }

    /*************************************** PART EDIT **************************************/

    /**
     * @param int $id Identifikator polozky
     * @throws AbortException|BadRequestException
     */
    public function renderEdit($id)
    {
        try {
            //	nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->model->fetch($id);
            //	odeberu idecko z pole a jine nepotrebne veci
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
        $form = new Edit\SlaForm();
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(Edit\SlaForm $form)
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
}