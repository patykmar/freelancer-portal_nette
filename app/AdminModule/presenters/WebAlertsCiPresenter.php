<?php

/**
 * Description of WebAlertsCiPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\Admin\OdCiGrid;
use App\Model\OdCiModel;
use App\Form\Admin\Add\OdCiForm as AddOdCiForm;
use App\Form\Admin\Edit\OdCiForm as EditOdCiForm;
use Exception;
use Nette\Application\AbortException;
use Nette\Database\Context;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class WebAlertsCiPresenter extends AdminbasePresenter
{
    private $odCiModel;
    private $odCiContext;

    public function __construct(OdCiModel $odCiModel, Context $odCiContext)
    {
        parent::__construct();
        $this->odCiModel = $odCiModel;
        $this->odCiContext = $odCiContext;
    }

    /*************************************** PART DEFINE GRIDS **************************************/

    protected function createComponentGrid(): OdCiGrid
    {
        return new OdCiGrid($this->odCiContext->table('od_ci'));
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

    public function createComponentAdd(): AddOdCiForm
    {
        $form = new AddOdCiForm();
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(AddOdCiForm $form)
    {
        try {
            $v = $form->getValues();
            $this->odCiModel->insert($v);
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
            $v = $this->odCiModel->fetch($id);

            // odeberu idecko z pole
//            $v->offsetUnset('id');

            // upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): EditOdCiForm
    {
        $form = new EditOdCiForm();
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(EditOdCiForm $form)
    {
        try {
            $v = $form->getValues();
            $this->odCiModel->update($v['new'], $v['id']);
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
    public function actionDrop($id)
    {
        try {
            $this->odCiModel->fetch($id);
            $this->odCiModel->removeItem($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('WebAlertsCi:default');    // change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('WebAlertsCi:default');    // change it !!!
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('WebAlertsCi:default');    // change it !!!
        }
    }
}
