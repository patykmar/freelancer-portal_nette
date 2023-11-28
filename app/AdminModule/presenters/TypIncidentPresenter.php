<?php

/**
 * Description of TypIncidentPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\Admin\TypIncidentGrid;
use App\Model\TypIncidentModel;
use Exception;
use App\Form\Admin\Add\TypIncidentForm as AddTypIncidentForm;
use App\Form\Admin\Edit\TypIncidentForm as EditTypIncidentForm;
use Nette\Application\AbortException;
use Nette\Database\Context;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class TypIncidentPresenter extends AdminbasePresenter
{
    private $typIncidentModel;
    private $typIncidentu;

    public function __construct(Context $context, TypIncidentModel $typIncidentModel)
    {
        parent::__construct();
        $this->typIncidentModel = $typIncidentModel;
        $this->typIncidentu = $context;
    }

    protected function createComponentGrid(): TypIncidentGrid
    {
        return new TypIncidentGrid($this->typIncidentu->table('typ_incident'));
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

    public function createComponentAdd(): AddTypIncidentForm
    {
        $form = new AddTypIncidentForm();
        $form['typ_incident']->setItems($this->typIncidentModel->fetchPairs());
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(AddTypIncidentForm $form)
    {
        try {
            $v = $form->getValues();
            $this->typIncidentModel->insert($v);
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
            //nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->typIncidentModel->fetch($id);

            //odeberu idecko z pole
//            $v->offsetUnset('id');

            //upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): EditTypIncidentForm
    {
        $form = new EditTypIncidentForm($this->typIncidentModel);
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(EditTypIncidentForm $form)
    {
        try {
            $v = $form->getValues();
            $this->typIncidentModel->update($v['new'], $v['id']);
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
            $this->typIncidentModel->fetch($id);
            $this->typIncidentModel->remove($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('TypIncident:default');    //change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('TypIncident:default');    //change it !!!
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('TypIncident:default');    //change it !!!
        }
    }
}
