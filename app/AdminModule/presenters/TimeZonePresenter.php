<?php

/**
 * Description of TimeZonePresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\Admin\TimeZoneGrid;
use App\Model\TimeZoneModel;
use Exception;
use App\Forms\Admin\Edit\TimeZoneForm as EditTimeZoneForm;
use App\Forms\Admin\Add\TimeZoneForm as AddTimeZoneForm;
use Nette\Application\AbortException;
use Nette\Database\Context;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class TimeZonePresenter extends AdminbasePresenter
{
    private TimeZoneModel $timeZoneModel;
    private Context $timeZoneContext;

    public function __construct(TimeZoneModel $timeZoneModel, Context $timeZoneContext)
    {
        parent::__construct();
        $this->timeZoneModel = $timeZoneModel;
        $this->timeZoneContext = $timeZoneContext;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        return new TimeZoneGrid($this->timeZoneContext->table('time_zone'));
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
        $form = new AddTimeZoneForm();
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(AddTimeZoneForm $form)
    {
        try {
            $v = $form->getValues();
            $this->timeZoneModel->insert($v);
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
            $v = $this->timeZoneModel->fetchById($id);

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
        $form = new EditTimeZoneForm();
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(EditTimeZoneForm $form)
    {
        try {
            $v = $form->getValues();
            $this->timeZoneModel->updateItem($v['new'], $v['id']);
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
            $this->timeZoneModel->fetchById($id);
            $this->timeZoneModel->removeItem($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('TimeZone:default');    // change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('TimeZone:default');    // change it !!!
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('TimeZone:default');    // change it !!!
        }
    }
}
