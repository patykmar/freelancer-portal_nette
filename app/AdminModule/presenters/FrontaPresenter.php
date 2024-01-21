<?php

/**
 * Description of FrontaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\ForeignKeyAddFormFactory;
use App\Factory\Forms\ForeignKeyEditFormFactory;
use App\Grids\FkGrid;
use Exception;
use App\Model\FrontaModel;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Database\Context;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class FrontaPresenter extends AdminbasePresenter
{
    private FrontaModel $frontaModel;
    private Context $frontaContext;
    private ForeignKeyAddFormFactory $foreignKeyAddFormFactory;
    private ForeignKeyEditFormFactory $foreignKeyEditFormFactory;

    public function __construct(
        FrontaModel               $frontaModel,
        Context                   $frontaContext,
        ForeignKeyAddFormFactory  $foreignKeyAddFormFactory,
        ForeignKeyEditFormFactory $foreignKeyEditFormFactory
    )
    {
        parent::__construct();
        $this->frontaModel = $frontaModel;
        $this->frontaContext = $frontaContext;
        $this->foreignKeyAddFormFactory = $foreignKeyAddFormFactory;
        $this->foreignKeyEditFormFactory = $foreignKeyEditFormFactory;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid(): FkGrid
    {
        return new FkGrid($this->frontaContext->table('fronta'));
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
        $form = $this->foreignKeyAddFormFactory->create();
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
            $this->frontaModel->insertNewItem($v);
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
            // nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->frontaModel->fetchById($id);

            // odeberu idecko z pole
            // $v->offsetUnset('id');

            // upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Form
    {
        $form = $this->foreignKeyEditFormFactory->create();
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
            $this->frontaModel->updateItem($v['new'], $v['id']);
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
                $this->frontaModel->fetchById($id);
                $this->frontaModel->remove($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('Fronta:default');    // change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('Fronta:default');    // change it !!!
            }
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Fronta:default');    // change it !!!
        }
    }
}
