<?php

/**
 * Description of TarifPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\TariffAddFormFactory;
use App\Factory\Forms\TariffEditFormFactory;
use App\Factory\Grids\SimpleDataGridFactory;
use App\Model\TarifModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class TarifPresenter extends AdminbasePresenter
{
    private TarifModel $tarifModel;
    private TariffAddFormFactory $tariffAddFormFactory;
    private TariffEditFormFactory $tariffEditFormFactory;
    private SimpleDataGridFactory $gridFactory;

    /**
     * @param TarifModel $tarifModel
     * @param TariffAddFormFactory $tariffAddFormFactory
     * @param TariffEditFormFactory $tariffEditFormFactory
     * @param SimpleDataGridFactory $gridFactory
     */
    public function __construct(
        TarifModel            $tarifModel,
        TariffAddFormFactory  $tariffAddFormFactory,
        TariffEditFormFactory $tariffEditFormFactory,
        SimpleDataGridFactory $gridFactory
    )
    {
        parent::__construct();
        $this->tarifModel = $tarifModel;
        $this->tariffAddFormFactory = $tariffAddFormFactory;
        $this->tariffEditFormFactory = $tariffEditFormFactory;
        $this->gridFactory = $gridFactory;
    }


    /**
     * Cast DEFAULT, definice Gridu
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        return $this->gridFactory->createTariffDataGrid();
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
        $form = $this->tariffAddFormFactory->create();
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    /**
     * @param Form $form
     * @throws AbortException
     */
    public function add(Form $form)
    {
        try {
            $v = $form->getValues();
            //vlozim novy tarif do databaze
            $this->tarifModel->insert($v);
            $this->flashMessage('Nový záznam byl přidán');
            $this->redirect('default');
        } catch (InvalidArgumentException $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
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
            $v = $this->tarifModel->fetchById($id);
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
        $form = $this->tariffEditFormFactory->create();
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @param Form $form
     */
    public function edit(Form $form)
    {
        try {
            $v = $form->getValues();
            $this->tarifModel->updateItem($v['new'], $v['id']);
            $this->flashMessage('Záznam byl úspěšně změněn');
            $this->redirect('default');
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Záznam nebyl změněn');
        }
    }

    /*************************************** PART DROP **************************************/

    /**
     * @param int $id Identifikator polozky
     * @throws AbortException
     */
    public function actionDrop(int $id)
    {
        try {
            $this->tarifModel->fetchById($id);
            $this->tarifModel->removeItem($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('Tarif:default'); //change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('Tarif:default'); //change it !!!

        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Tarif:default'); //change it !!!
        }
    }

}
