<?php

/**
 * Description of FormatDatumPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule;

use DibiException;
use Exception;
use Gridy\Admin\FormatDatumGrid;
use MyForms\Admin\Add\FormatDatumForm as AddFormatDatumForm;
use MyForms\Admin\Edit\FormatDatumForm as EditFormatDatumForm;
use App\Model\FormatDatumModel;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\DI\Container;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

class FormatDatumPresenter extends AdminbasePresenter
{
    /** @var FormatDatumModel */
    private $model;

    /**
     * @param Container $context
     */
    public function __construct(Container $context)
    {
        parent::__construct($context);
        $this->model = new FormatDatumModel();
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        return new FormatDatumGrid($this->context->database->context->table('format_datum'));
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

    public function createComponentAdd()
    {
        $form = new AddFormatDatumForm;
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    public function add(AddFormatDatumForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->insert($v);
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
     */
    public function renderEdit($id)
    {
        try {
            $this->setView('../_edit');
            //	nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->model->fetch($id);

            //	odeberu idecko z pole
            $v->offsetUnset('id');
            //	upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit()
    {
        $form = new EditFormatDatumForm;
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    public function edit(EditFormatDatumForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->update($v['new'], $v['id']);
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
    public function actionDrop($id)
    {
        try {
            try {
                $this->model->fetch($id);
                $this->model->remove($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('FormatDatum:default');    //	change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('FormatDatum:default');    //	change it !!!
            }
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('FormatDatum:default');    //	change it !!!
        }
    }
}