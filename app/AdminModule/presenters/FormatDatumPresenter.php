<?php

/**
 * Description of FormatDatumPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\DateFormatAddFormFactory;
use App\Factory\Forms\DateFormatEditFormFactory;
use App\Factory\Grids\SimpleDataGridFactory;
use Exception;
use App\Model\FormatDatumModel;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class FormatDatumPresenter extends AdminbasePresenter
{
    private FormatDatumModel $formatDatumModel;
    private DateFormatAddFormFactory $dateFormatAddFormFactory;
    private DateFormatEditFormFactory $dateFormatEditFormFactory;
    private SimpleDataGridFactory $gridFactory;

    public function __construct(
        FormatDatumModel          $formatDatumModel,
        DateFormatAddFormFactory  $dateFormatAddFormFactory,
        DateFormatEditFormFactory $dateFormatEditFormFactory,
        SimpleDataGridFactory     $gridFactory
    )
    {
        parent::__construct();
        $this->formatDatumModel = $formatDatumModel;
        $this->dateFormatAddFormFactory = $dateFormatAddFormFactory;
        $this->dateFormatEditFormFactory = $dateFormatEditFormFactory;
        $this->gridFactory = $gridFactory;
    }

    /**
     * Cast DEFAULT, definice Gridu
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        return $this->gridFactory->createDateFormat();
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
        $form = $this->dateFormatAddFormFactory->create();
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
            $this->formatDatumModel->insertNewItem($v);
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
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            // nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->formatDatumModel->fetchById($id);

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
        $form = $this->dateFormatEditFormFactory->create();
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
            $this->formatDatumModel->updateItem($v['new'], $v['id']);
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
                $this->formatDatumModel->fetchById($id);
                $this->formatDatumModel->remove($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('FormatDatum:default');    // change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('FormatDatum:default');    // change it !!!
            }
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('FormatDatum:default');    // change it !!!
        }
    }
}
