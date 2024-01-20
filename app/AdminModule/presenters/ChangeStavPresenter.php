<?php

/**
 * Description of ChangeStavPresenter
 *
 * @author Martin Patyk
 */


namespace App\AdminModule\Presenters;

use App\Forms\Admin\Add\ForeignKeyAddForm;
use App\Forms\Admin\Edit\ForeignKeyEditForm;
use App\Grids\FkGrid;
use Exception;
use App\Model\ChangeStavModel;
use InvalidArgumentException;
use Nette\Application\AbortException;
use Nette\Database\Context;
use Tracy\Debugger;


class ChangeStavPresenter extends AdminbasePresenter
{
    private ChangeStavModel $changeStavModel;
    private Context $changeStavContext;

    public function __construct(
        ChangeStavModel $changeStavModel,
        Context         $changeStavContext
    )
    {
        parent::__construct();
        $this->changeStavModel = $changeStavModel;
        $this->changeStavContext = $changeStavContext;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid(): FkGrid
    {
        return new FkGrid($this->changeStavContext->table(ChangeStavModel::TABLE_NAME));
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

    public function createComponentAdd(): ForeignKeyAddForm
    {
        $form = new ForeignKeyAddForm;
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(ForeignKeyAddForm $form)
    {
        try {
            $v = $form->getValues();
            $this->changeStavModel->insert($v);
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
     * @throws AbortException
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            // nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->changeStavModel->fetchById($id);
            // odeberu idecko z pole
//            $v->offsetUnset('id');
            // upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): ForeignKeyEditForm
    {
        $form = new ForeignKeyEditForm;
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(ForeignKeyEditForm $form)
    {
        try {
            $v = $form->getValues();
            $this->changeStavModel->updateItem($v['new'], $v['id']);
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
     * @throws AbortException
     */
    public function actionDrop(int $id)
    {
        try {
            $this->changeStavModel->fetchById($id);
            $this->changeStavModel->removeItem($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('ChangeStav:default');    // change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('ChangeStav:default');    // change it !!!
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('ChangeStav:default');    // change it !!!
        }
    }
}
