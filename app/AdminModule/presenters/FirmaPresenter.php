<?php

/**
 * Description of FirmaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\Admin\FirmaGrid;
use App\Model\ZemeModel;
use Exception;
use App\Forms\Admin\Add;
use App\Forms\Admin\Edit;
use App\Model\FirmaModel;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\Database\Context;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class FirmaPresenter extends AdminbasePresenter
{
    private FirmaModel $firmaModel;
    private Context $firmaContext;
    private ZemeModel $zemeModel;

    public function __construct(FirmaModel $firmaModel, Context $firmaContext, ZemeModel $zemeModel)
    {
        parent::__construct();
        $this->firmaModel = $firmaModel;
        $this->firmaContext = $firmaContext;
        $this->zemeModel = $zemeModel;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid(): FirmaGrid
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

    public function createComponentAdd(): Add\FirmaForm
    {
        $form = new Add\FirmaForm($this->zemeModel);
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortExceptionAlias
     */
    public function add(Add\FirmaForm $form)
    {
        try {
            $v = $form->getValues();
            $v->offsetSet('datum_vytvoreni', new DateTime);
            $v->offsetSet('datum_upravy', new DateTime);
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
     * @throws AbortExceptionAlias
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            //nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->firmaModel->fetchById($id);

            //odeberu idecko z pole
//            $v->offsetUnset('id');

            //upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Edit\FirmaForm
    {
        $form = new Edit\FirmaForm($this->zemeModel);
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortExceptionAlias
     */
    public function edit(Edit\FirmaForm $form)
    {
        try {
            $v = $form->getValues();
            $v['new']->offsetSet('datum_upravy', new DateTime);
            $this->firmaModel->updateItem($v['new'], $v['id']);
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
    public function actionDrop(int $id)
    {
        try {
            try {
                $this->firmaModel->fetchById($id);
                $this->firmaModel->remove($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('Firma:default'); // change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('Firma:default'); // change it !!!
            }
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Firmas:default');    //change it !!!
        }
    }

}
