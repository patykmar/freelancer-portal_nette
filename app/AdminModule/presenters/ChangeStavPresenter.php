<?php


/**
 * Description of ChangeStavPresenter
 *
 * @author Martin Patyk
 */


namespace App\AdminModule\Presenters;

use App\Grids\FkGrid;
use Exception;
use App\Form\Admin\Add;
use App\Form\Admin\Edit;
use App\Model\ChangeStavModel;
use InvalidArgumentException;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\Database\Context;
use Tracy\Debugger;


class ChangeStavPresenter extends AdminbasePresenter
{
    private $changeStavModel;
    private $changeStavContext;

    public function __construct(
        ChangeStavModel $changeStavModel,
        Context $changeStavContext
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
        return new FkGrid($this->changeStavContext->table('change_stav'));
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

    public function createComponentAdd(): Add\FkBaseForm
    {
        $form = new Add\FkBaseForm;
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    public function add(Add\FkBaseForm $form)
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
     * @throws AbortExceptionAlias
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            // nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->changeStavModel->fetch($id);
            // odeberu idecko z pole
//            $v->offsetUnset('id');
            // upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Edit\FkBaseForm
    {
        $form = new Edit\FkBaseForm;
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortExceptionAlias
     */
    public function edit(Edit\FkBaseForm $form)
    {
        try {
            $v = $form->getValues();
            $this->changeStavModel->update($v['new'], $v['id']);
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
                $this->changeStavModel->fetch($id);
                $this->changeStavModel->removeItem($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('ChangeStav:default');    //	change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('ChangeStav:default');    //	change it !!!
            }
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('ChangeStav:default');    //	change it !!!
        }
    }
}