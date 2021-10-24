<?php

/**
 * Description of FirmaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\Admin\FirmaGrid;
use DibiException;
use Exception;
use App\Form\Admin\Add;
use App\Form\Admin\Edit;
use App\Model\FirmaModel;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\Database\Context;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

class FirmaPresenter extends AdminbasePresenter
{
    /** @var FirmaModel */
    private $firmaModel;

    /** @var Context */
    private $firmaContext;

    public function __construct(FirmaModel $firmaModel, Context $firmaContext)
    {
        parent::__construct();
        $this->firmaModel = $firmaModel;
        $this->firmaContext = $firmaContext;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        return new FirmaGrid($this->firmaContext->table('firma'));
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

    public function createComponentAdd()
    {
        $form = new Add\FirmaForm();
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    public function add(Add\FirmaForm $form)
    {
        try {
            $v = $form->getValues();
            $v->offsetSet('datum_vytvoreni', new \Nette\DateTime);
            $v->offsetSet('datum_upravy', new \Nette\DateTime);
            $this->firmaModel->insert($v);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
        $this->flashMessage('Nový záznam byl přidán');
        $this->redirect('default');
    }

    /**
     * Cast EDIT
     * @param int $id Identifikator polozky
     */
    public function renderEdit($id)
    {
        try {
            $this->setView('../_edit');
            //	nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->firmaModel->fetch($id);

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
        $form = new Edit\FirmaForm();
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    public function edit(Edit\FirmaForm $form)
    {
        try {
            $v = $form->getValues();
            $v['new']->offsetSet('datum_upravy', new \Nette\DateTime);
            $this->firmaModel->update($v['new'], $v['id']);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Záznam nebyl změněn');
        }
        $this->flashMessage('Záznam byl úspěšně změněn');
        $this->redirect('default');
    }

    /**
     * Cast DROP
     * @param int $id Identifikator polozky
     * @throws AbortExceptionAlias
     */
    public function actionDrop($id)
    {
        try {
            try {
                $this->firmaModel->fetch($id);
                $this->firmaModel->remove($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('Firma:default'); // change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('Firma:default'); // change it !!!
            }
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Firmas:default');    //	change it !!!
        }
    }
}