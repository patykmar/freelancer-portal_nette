<?php

/**
 * Description of SlaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\SlaAddFormFactory;
use App\Factory\Forms\SlaEditFormFactory;
use App\Grids\Admin\SlaGrid;
use App\Model\SlaModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Tracy\Debugger;
use Nette\InvalidArgumentException;


class SlaPresenter extends AdminbasePresenter
{
    private SlaModel $slaModel;
    private Context $slaContext;
    private SlaAddFormFactory $slaAddFormFactory;
    private SlaEditFormFactory $slaEditFormFactory;

    public function __construct(
        SlaModel           $slaModel,
        Context            $slaContext,
        SlaAddFormFactory  $slaAddFormFactory,
        SlaEditFormFactory $slaEditFormFactory
    )
    {
        parent::__construct();
        $this->slaModel = $slaModel;
        $this->slaContext = $slaContext;
        $this->slaAddFormFactory = $slaAddFormFactory;
        $this->slaEditFormFactory = $slaEditFormFactory;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid(): SlaGrid
    {
        $id = $this->presenter->getParameter('id');
        if (isset($id)) {
            return new SlaGrid($this->slaContext->table('sla')->where(array('tarif' => $id)));
        } else {
            return new SlaGrid($this->slaContext->table('sla'));
        }
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
