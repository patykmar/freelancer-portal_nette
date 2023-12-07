<?php

/**
 * Description of CiPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\Admin\CiGrid;
use App\Grids\Admin\PotomciCiGrid;
use App\Model\CiLogModel;
use App\Model\CiModel;
use App\Model\FirmaModel;
use App\Model\FrontaModel;
use App\Model\StavCiModel;
use App\Model\TarifModel;
use Exception;
use App\Forms\Admin\Add\CiForm;
use App\Forms\Admin\Edit;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Database\Context;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class CiPresenter extends AdminbasePresenter
{
    private CiModel $ciModel;
    private StavCiModel $stavCiModel;
    private CiLogModel $ciLogModel;
    private Context $ciContext;
    private FrontaModel $frontaModel;
    private FirmaModel $firmaModel;
    private TarifModel $tarifModel;

    public function __construct(
        CiModel     $ciModel,
        StavCiModel $stavCiModel,
        CiLogModel  $modelCiLog,
        FrontaModel $frontaModel,
        FirmaModel  $firmaModel,
        TarifModel  $tarifModel,
        Context     $ciContext
    )
    {
        parent::__construct();
        $this->ciModel = $ciModel;
        $this->stavCiModel = $stavCiModel;
        $this->ciLogModel = $modelCiLog;
        $this->frontaModel = $frontaModel;
        $this->firmaModel = $firmaModel;
        $this->tarifModel = $tarifModel;
        $this->ciContext = $ciContext;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid(): CiGrid
    {
        return new CiGrid($this->ciContext->table('ci')->where(array('zobrazit' => 1)));
    }

    public function renderDefault()
    {
        $this->setView('../_default');
    }

    /**
     * Cast ADD
     * @param int $id identifikator predka. Pokud je nastavena hodnota
     * vlozi se jako cizi klic do SelectBoxu.
     */
    public function renderAdd($id = null)
    {
        //nastaveni sablony
        $this->setView('../_add');
    }

    public function renderAddChild(int $id)
    {
        try {
            $this->setView('../_add');
            $this->ciModel->fetch($id);

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

    public function createComponentAdd(): CiForm
    {
        $form = new CiForm(
            $this->ciModel,
            $this->stavCiModel,
            $this->frontaModel,
            $this->firmaModel,
            $this->tarifModel
        );
        $form->onSuccess[] = [$this, 'add'];
        return $form;
    }

    public function add(CiForm $form)
    {
        try {
            $v = $form->getValues();
            $ci_log = $this->createLog($form->components);
            $v->offsetSet('log', $ci_log);
            $v->offsetSet('osoba_vytvoril', $this->userId);
            $v->offsetSet('datum_vytvoreni', new \Nette\DateTime);
            $this->ciModel->insert($v);
        } catch (InvalidArgumentException $exc) {
            $form->addError('Nový záznam nebyl přidán');
            $this->flashMessage($exc->getMessage());
        }
        $this->flashMessage('Nový záznam byl přidán');
        $this->redirect('default');
    }

    /**
     * Cast Edit, definice Gridu, ktery zobrazuje potomky
     */
    protected function createComponentPotomciGrid(): PotomciCiGrid
    {
        //nactu si idecko editovaneho predka
        $id = $this->presenter->getParameter('id');
        return new PotomciCiGrid($this->ciContext->table('ci')->where(array('ci' => $id)));
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
            $v = $this->ciModel->fetch($id);
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

    public function createComponentEdit(): Edit\CiForm
    {
        $form = new Edit\CiForm(
            $this->ciModel,
            $this->stavCiModel,
            $this->frontaModel,
            $this->firmaModel,
            $this->tarifModel
        );
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(Edit\CiForm $form)
    {
        try {
            $v = $form->getValues();
            $this->ciModel->update($v['new'], $v['id']);
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
                $this->ciModel->fetch($id);
                $this->ciModel->remove($id);
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
