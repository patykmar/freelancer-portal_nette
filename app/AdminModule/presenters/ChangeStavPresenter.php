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
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\Database\Context;
use Tracy\Debugger;


class ChangeStavPresenter extends AdminbasePresenter
{
    /** @var ChangeStavModel */
    private $changeStavModel;

    /** @var Context */
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
    protected function createComponentGrid()
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

    public function createComponentAdd()
    {
        $form = new Add\FkBaseForm;
        $form->onSuccess[] = callback($this, 'add');
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
     */
    public function renderEdit($id)
    {
        try {
            $this->setView('../_edit');
            //	nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->changeStavModel->fetch($id);
            //	odeberu idecko z pole
//            $v->offsetUnset('id');
            //	upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (\Nette\InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit()
    {
        $form = new Edit\FkBaseForm;
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

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
    public function actionDrop($id)
    {
        try {
            try {
                $this->changeStavModel->fetch($id);
                $this->changeStavModel->remove($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('ChangeStav:default');    //	change it !!!
            } catch (\Nette\InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('ChangeStav:default');    //	change it !!!
            }
        } catch (\DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('ChangeStav:default');    //	change it !!!
        }
    }
}