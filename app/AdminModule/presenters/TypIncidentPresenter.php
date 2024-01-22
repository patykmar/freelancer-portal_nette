<?php

/**
 * Description of TypIncidentPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\TypIncidentFormFactory;
use App\Grids\Admin\TypIncidentGrid;
use App\Model\TypIncidentModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Tracy\ILogger;

class TypIncidentPresenter extends AdminbasePresenter
{
    private TypIncidentModel $typIncidentModel;
    private Context $typIncidentu;
    private TypIncidentFormFactory $typIncidentFormFactory;

    public function __construct(
        Context                $context,
        TypIncidentModel       $typIncidentModel,
        TypIncidentFormFactory $typIncidentFormFactory
    )
    {
        parent::__construct();
        $this->typIncidentModel = $typIncidentModel;
        $this->typIncidentu = $context;
        $this->typIncidentFormFactory = $typIncidentFormFactory;
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

    public function createComponentAdd(): Form
    {
        $form = $this->typIncidentFormFactory->create();
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(Form $form)
    {
        try {
            $v = $form->getValues();
            $v->offsetUnset('id');
            $this->typIncidentModel->insertNewItem($v);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage(), ILogger::ERROR);
            $form->addError('Nový záznam nebyl přidán');
        }
        $this->flashMessage('Nový záznam byl přidán');
        $this->redirect('default');
    }

    /*************************************** PART EDIT **************************************/

    /**
     * @param int $id Identifikator polozky
     * @throws AbortException
     * @throws BadRequestException
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            //nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->typIncidentModel->fetchById($id);

            //odeberu idecko z pole
//            $v->offsetUnset('id');

            //upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults($v);
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Form
    {
        $form = $this->typIncidentFormFactory->create();
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(Form $form)
    {
        try {
            $v = $form->getValues();
            $this->typIncidentModel->updateItem($v, $v['id']);
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
            $this->typIncidentModel->fetchById($id);
            $this->typIncidentModel->removeItem($id);
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
