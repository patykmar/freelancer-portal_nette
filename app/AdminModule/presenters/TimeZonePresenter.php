<?php

/**
 * Description of TimeZonePresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\Admin\TimeZoneGrid;
use App\Model\TimeZoneModel;
use DibiException;
use Exception;
use App\Form\Admin\Edit\TimeZoneForm as EditTimeZoneForm;
use App\Form\Admin\Add\TimeZoneForm as AddTimeZoneForm;
use Nette\Application\AbortException;
use Nette\Database\Context;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class TimeZonePresenter extends AdminbasePresenter
{
    /** @var TimeZoneModel */
    private $timeZoneModel;

    /** @var Context */
    private $timeZoneContext;

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
        $form->onSuccess[] = callback($this, 'add');
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
    public function renderEdit($id)
    {
        try {
            $this->setView('../_edit');
            //	nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->timeZoneModel->fetch($id);

            //	odeberu idecko z pole
//            $v->offsetUnset('id');

            //	upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit()
    {
        $form = new EditTimeZoneForm();
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(EditTimeZoneForm $form)
    {
        try {
            $v = $form->getValues();
            $this->timeZoneModel->update($v['new'], $v['id']);
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
            $this->timeZoneModel->fetch($id);
            $this->timeZoneModel->remove($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('TimeZone:default');    //	change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('TimeZone:default');    //	change it !!!
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('TimeZone:default');    //	change it !!!
        }
    }
}