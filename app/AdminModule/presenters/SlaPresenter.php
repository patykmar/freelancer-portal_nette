<?php

/**
 * Description of SlaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\SlaAddFormFactory;
use App\Factory\Forms\SlaEditFormFactory;
use App\Factory\Grids\SlaDataGridFactory;
use App\Model\SlaModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class SlaPresenter extends AdminbasePresenter
{
    private SlaModel $slaModel;
    private SlaAddFormFactory $slaAddFormFactory;
    private SlaEditFormFactory $slaEditFormFactory;
    private SlaDataGridFactory $gridFactory;

    public function __construct(
        SlaModel           $slaModel,
        SlaAddFormFactory  $slaAddFormFactory,
        SlaEditFormFactory $slaEditFormFactory,
        SlaDataGridFactory $gridFactory
    )
    {
        parent::__construct();
        $this->slaModel = $slaModel;
        $this->slaAddFormFactory = $slaAddFormFactory;
        $this->slaEditFormFactory = $slaEditFormFactory;
        $this->gridFactory = $gridFactory;
    }

    /**
     * Cast DEFAULT, definice Gridu
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        $id = $this->presenter->getParameter('id');
        return $this->gridFactory->create($id);
    }

    public function createComponentAdd(): Form
    {
        return $this->slaAddFormFactory->create();
    }

    //TODO: Add handling new item form

    public function renderDefault($id = null)
    {
        //need to be here, otherwise it can be load default action
    }

    /*************************************** PART EDIT **************************************/

    /**
     * @param int $id Identifikator polozky
     * @throws AbortException|BadRequestException
     */
    public function renderEdit(int $id)
    {
        try {
            //nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->slaModel->fetchById($id);
            //odeberu idecko z pole a jine nepotrebne veci
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
        $form = $this->slaEditFormFactory->create();
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
            $this->slaModel->updateItem($v['new'], $v['id']);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Záznam nebyl změněn');
        }
        $this->flashMessage('Záznam byl úspěšně změněn');
        $this->redirect('default');
    }
}
