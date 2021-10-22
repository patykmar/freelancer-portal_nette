<?php

/**
 * Description of CiPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Model\CiLogModel;
use App\Model\CiModel;
use DibiException;
use Exception;
use Gridy\Admin\CiGrid;
use Gridy\Admin\PotomciCiGrid;
use App\Form\Admin\Add\CiForm;
use App\Form\Admin\Edit;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\Application\BadRequestException;
use Nette\DI\Container;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

class CiPresenter extends AdminbasePresenter
{

    /** @var CiModel */
    private $model;

    /** @var CiLogModel */
    private $modelCiLog;

    public function __construct(Container $context)
    {
        parent::__construct($context);
        $this->model = new CiModel();
        $this->modelCiLog = new CiLogModel;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
        return new CiGrid($this->context->database->context->table('ci')->where(array('zobrazit' => 1)));
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
        //	nastaveni sablony
        $this->setView('../_add');
    }

    public function renderAddChild($id)
    {
        try {
            $this->setView('../_add');
            $this->model->fetch($id);

            // u potomka neni potreba specifikovat nektere cizi klice
            $this['add']->offsetUnset('fronta_tier_1');
            $this['add']->offsetUnset('fronta_tier_2');
            $this['add']->offsetUnset('fronta_tier_3');
            $this['add']->offsetUnset('stav_ci');
            $this['add']->offsetUnset('firma');
            $this['add']->offsetUnset('tarif');

            $this['add']->onSuccess[] = callback($this, 'add');
            $this['add']->setDefaults(array('ci' => $id));
        } catch (BadRequestException $exc) {
            // zapisu chybu do logy
            Debugger::log($exc->getMessage());

            $this->flashMessage('Predchozi CI nenalezenno');
            $this->redirect('default');
        }
    }

    public function createComponentAdd()
    {
        $form = new CiForm;
        $form->onSuccess[] = callback($this, 'add');
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
            $this->model->insert($v);
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
    protected function createComponentPotomciGrid()
    {
        //	nactu si idecko editovaneho predka
        $id = $this->presenter->getParameter('id');
        return new PotomciCiGrid($this->context->database->context->table('ci')->where(array('ci' => $id)));
    }

    /**
     * Cast EDIT
     * @param int $id Identifikator polozky
     */
    public function renderEdit($id)
    {
        try {
            #$this->setView('../_edit');
            //	nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->model->fetch($id);
            $this->template->id = $id;
            //	do sablony poslu log
            $this->template->ciLog = $this->modelCiLog->fetchAllByCi($id);
            //	odeberu idecko z pole
            $v->offsetUnset('id');
            //	upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (BadRequestException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit()
    {
        $form = new Edit\CiForm;
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    /**
     * @throws \Nette\Application\AbortException
     */
    public function edit(Edit\CiForm $form)
    {
        try {
            $v = $form->getValues();
//            dump($v);
//            $dbData = $this->model->fetch($v['id']);
//            dump($dbData);
//            exit;
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
                $this->redirect('Ci:default'); //	change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('Ci:default'); //	change it !!!
            }
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Ci:default'); //	change it !!!
        }
    }
}