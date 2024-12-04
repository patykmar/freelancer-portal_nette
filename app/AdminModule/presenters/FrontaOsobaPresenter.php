<?php

/**
 * Description of FrontaOsobaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\QueueOsobaAddFormFactory;
use App\Factory\Forms\QueueOsobaEditFormFactory;
use App\Factory\Grids\OsobaDataGridFactory;
use Exception;
use App\Model\FrontaOsobaModel;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class FrontaOsobaPresenter extends AdminbasePresenter
{
    private FrontaOsobaModel $frontaOsobaModel;
    private QueueOsobaAddFormFactory $queueOsobaAddFormFactory;
    private QueueOsobaEditFormFactory $queueOsobaEditFormFactory;
    private OsobaDataGridFactory $gridFactory;

    public function __construct(
        FrontaOsobaModel          $frontaOsobaModel,
        QueueOsobaAddFormFactory  $queueOsobaAddFormFactory,
        QueueOsobaEditFormFactory $queueOsobaEditFormFactory,
        OsobaDataGridFactory      $gridFactory
    )
    {
        parent::__construct();
        $this->frontaOsobaModel = $frontaOsobaModel;
        $this->queueOsobaAddFormFactory = $queueOsobaAddFormFactory;
        $this->queueOsobaEditFormFactory = $queueOsobaEditFormFactory;
        $this->gridFactory = $gridFactory;
    }

    /**
     * Cast DEFAULT, definice Gridu
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        return $this->gridFactory->createFrontaOsobaGrid();
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

    public function createComponentAdd(): Form
    {
        $form = $this->queueOsobaAddFormFactory->create();
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @throws AbortExceptionAlias
     */
    public function add(Form $form)
    {
        try {
            $v = $form->getValues();
            $this->frontaOsobaModel->insert($v);
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
     * @throws BadRequestException
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            //nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->frontaOsobaModel->fetchById($id);

            //odeberu idecko z pole
//            $v->offsetUnset('id');

            //upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Form
    {
        $form = $this->queueOsobaEditFormFactory->create();
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortExceptionAlias
     */
    public function edit(Form $form)
    {
        try {
            $v = $form->getValues();
            $this->frontaOsobaModel->updateItem($v['new'], $v['id']);
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
                $this->frontaOsobaModel->fetchById($id);
                $this->frontaOsobaModel->remove($id);
                $this->flashMessage('Položka byla odebrána'); //Položka byla odebrána
                $this->redirect('FrontaOsobaPresenter:default');    //change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('FrontaOsobaPresenter:default');    //change it !!!
            }
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('FrontaOsobaPresenter:default');    //change it !!!
        }
    }

}
