<?php

/**
 * Description of CiPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\CiAddFormFactory;
use App\Factory\Forms\CiEditFormFactory;
use App\Factory\Grids\CiDataGridFactory;
use App\Model\CiLogModel;
use App\Model\CiModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class CiPresenter extends AdminbasePresenter
{
    private CiModel $ciModel;
    private CiLogModel $ciLogModel;
    private CiAddFormFactory $addFormFactory;
    private CiEditFormFactory $editFormFactory;
    private CiDataGridFactory $gridFactory;

    public function __construct(
        CiModel           $ciModel,
        CiLogModel        $ciLogModel,
        CiAddFormFactory  $addFormFactory,
        CiEditFormFactory $editFormFactory,
        CiDataGridFactory $gridFactory
    )
    {
        parent::__construct();
        $this->ciModel = $ciModel;
        $this->ciLogModel = $ciLogModel;
        $this->addFormFactory = $addFormFactory;
        $this->editFormFactory = $editFormFactory;
        $this->gridFactory = $gridFactory;
    }

    /**
     * Cast DEFAULT, definice Gridu
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        return $this->gridFactory->create();
    }

    public function renderDefault()
    {
        $this->setView('../_default');
    }

    /**
     * Cast ADD
     * @param ?int $id identifikator predka. Pokud je nastavena hodnota
     * vlozi se jako cizi klic do SelectBoxu.
     */
    public function renderAdd(?int $id): void
    {
        //nastaveni sablony
        $this->setView('../_add');
    }

    /**
     * @throws AbortException
     */
    public function renderAddChild(int $id)
    {
        try {
            $this->setView('../_add');
            $this->ciModel->fetchById($id);

            // u potomka neni potreba specifikovat nektere cizi klice
            $this['add']->offsetUnset('fronta_tier_1');
            $this['add']->offsetUnset('fronta_tier_2');
            $this['add']->offsetUnset('fronta_tier_3');
            $this['add']->offsetUnset('stav_ci');
            $this['add']->offsetUnset('firma');
            $this['add']->offsetUnset('tarif');

            $this['add']->onSuccess[] = [$this, 'add'];
            $this['add']->setDefaults(array('ci' => $id));
        } catch (BadRequestException $exc) {
            // zapisu chybu do logy
            Debugger::log($exc->getMessage());

            $this->flashMessage('Predchozi CI nenalezenno');
            $this->redirect('default');
        }
    }

    public function createComponentAdd(): Form
    {
        $form = $this->addFormFactory->create();
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
            $ci_log = $this->createLog($form->components);
            $v->offsetSet('log', $ci_log);
            $v->offsetSet('osoba_vytvoril', $this->userId);
            $v->offsetSet('datum_vytvoreni', new DateTime);
            $this->ciModel->insertNewItem($v);
        } catch (InvalidArgumentException $exc) {
            $form->addError('Nový záznam nebyl přidán');
            $this->flashMessage($exc->getMessage());
        }
        $this->flashMessage('Nový záznam byl přidán');
        $this->redirect('default');
    }

    /**
     * Cast Edit, definice Gridu, ktery zobrazuje potomky
     * @throws DataGridException
     */
    protected function createComponentPotomciGrid(): DataGrid
    {
        //nactu si idecko editovaneho predka
        $id = $this->presenter->getParameter('id');
        return $this->gridFactory->createPotomciCi($id);
    }

    /**
     * Cast EDIT
     * @param int $id Identifikator polozky
     * @throws AbortException
     */
    public function renderEdit(int $id)
    {
        try {
            #$this->setView('../_edit');
            //nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->ciModel->fetchById($id);
            $this->template->id = $id;
            //do sablony poslu log
            $this->template->ciLog = $this->ciLogModel->fetchAllByCi($id);
//            //odeberu idecko z pole
//            $v->offsetUnset('id');
            //upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (BadRequestException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Form
    {
        $form = $this->editFormFactory->create();
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
            $this->ciModel->updateItem($v['new'], $v['id']);
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
     * @throws AbortException
     */
    public function actionDrop(int $id)
    {
        try {
            try {
                $this->ciModel->fetchById($id);
                $this->ciModel->removeItem($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('Ci:default'); //change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('Ci:default'); //change it !!!
            }
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Ci:default'); //change it !!!
        }
    }

}
