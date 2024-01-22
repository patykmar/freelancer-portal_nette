<?php

/**
 * Description of OvlivneniPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\ImpactAddFormFactory;
use App\Factory\Forms\ImpactEditFormFactory;
use App\Grids\Admin\OvlivneniGrid;
use App\Model\OvlivneniModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class OvlivneniPresenter extends AdminbasePresenter
{
    private Context $netteModel;
    private OvlivneniModel $model;
    private ImpactAddFormFactory $impactAddFormFactory;
    private ImpactEditFormFactory $impactEditFormFactory;

    public function __construct(
        Context               $context,
        OvlivneniModel        $ovlivneniModel,
        ImpactAddFormFactory  $impactAddFormFactory,
        ImpactEditFormFactory $impactEditFormFactory
    )
    {
        parent::__construct();
        $this->model = $ovlivneniModel;
        $this->netteModel = $context;
        $this->impactAddFormFactory = $impactAddFormFactory;
        $this->impactEditFormFactory = $impactEditFormFactory;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid(): OvlivneniGrid
    {
        return new OvlivneniGrid($this->netteModel->table('ovlivneni'));
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
        $form = $this->impactAddFormFactory->create();
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
            $this->model->insertNewItem($v);
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
     * @throws BadRequestException
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');

            // nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->model->fetchById($id);

            // odeberu idecko z pole
//            $v->offsetUnset('id');

            // upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Form
    {
        $form = $this->impactEditFormFactory->create();
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
            $this->model->updateItem($v['new'], $v['id']);
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
            $this->model->fetchById($id);
            $this->model->removeItem($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('Ovlivneni:default');    // change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('Ovlivneni:default');    // change it !!!
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Ovlivneni:default');    // change it !!!
        }
    }
}
