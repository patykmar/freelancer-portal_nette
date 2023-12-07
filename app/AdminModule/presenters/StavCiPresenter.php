<?php

/**
 * Description of StavCiPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\FkGrid;
use App\Model\StavCiModel;
use Exception;
use App\Forms\Admin\Add\FkBaseForm as AddFkBaseForm;
use App\Forms\Admin\Edit\FkBaseForm as EditFkBaseForm;
use Nette\Application\AbortException;
use Nette\Database\Context;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class StavCiPresenter extends AdminbasePresenter
{
    private StavCiModel $stavCiModel;
    private Context $stavCiContext;

    public function __construct(StavCiModel $stavCiModel, Context $stavCiContext)
    {
        parent::__construct();
        $this->stavCiModel = $stavCiModel;
        $this->stavCiContext = $stavCiContext;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        return new FkGrid($this->stavCiContext->table('stav_ci'));
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
        $form = new AddFkBaseForm;
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(AddFkBaseForm $form)
    {
        try {
            $v = $form->getValues();
            $this->stavCiModel->insert($v);
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
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            // nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->stavCiModel->fetch($id);

            // odeberu idecko z pole
//            $v->offsetUnset('id');

            // upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit()
    {
        $form = new EditFkBaseForm;
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(EditFkBaseForm $form)
    {
        try {
            $v = $form->getValues();
            $this->stavCiModel->update($v['new'], $v['id']);
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
            $this->stavCiModel->fetch($id);
            $this->stavCiModel->remove($id);
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