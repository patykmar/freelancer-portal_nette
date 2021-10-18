<?php

/**
 * Description of FakturaPolozkaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule;

use App\Model\FakturaPolozkaModel;
use App\Model\FakturaModel;
use DibiException;
use App\Model;
use Exception;
use MyForms\Admin\Add\FakturaPolozkaForm as AddFakturaPolozkaForm;
use MyForms\Admin\Edit\FakturaPolozkaForm as EditFakturaPolozkaForm;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

class FakturaPolozkaPresenter extends AdminbasePresenter
{

    /** @var FakturaPolozkaModel */
    private $model;

    /** @var FakturaModel */
    private $modelFaktura;

    public function __construct(\Nette\DI\Container $context)
    {
        parent::__construct($context);
        $this->model = new Model\FakturaPolozkaModel;
        $this->modelFaktura = new Model\FakturaModel;
    }

    /**
     * presmeruju na faktury
     * @throws AbortExceptionAlias
     */
    public function actionDefault()
    {
        $this->redirect('Faktura:');
    }

    /*************************************** PART ADD **************************************/

    /**
     * @throws AbortExceptionAlias
     * @var $id int identifikator faktury
     */
    public function renderAdd($id)
    {
        try {
            $this->setView('../_add');
            //	overim ze je v systemu evidovana faktura s timto cislem
            $this->modelFaktura->fetch($id);

            // do vytvorene komponenty vlozim cislo faktury do ktere chci vlozit polozku
            $this['add']->setDefaults(Nette\ArrayHash::from(array('faktura' => $id)));
        } catch (BadRequestException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('Faktura:');
        }
    }

    public function createComponentAdd()
    {
        $form = new AddFakturaPolozkaForm;
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    /**
     * @param AddFakturaPolozkaForm $form
     * @throws \Nette\Application\AbortException
     */
    public function add(AddFakturaPolozkaForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->insert($v);
            $this->flashMessage('Nový záznam byl přidán');
            $this->redirect('Faktura:edit', $v['faktura']);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
    }

    /*************************************** PART EDIT **************************************/

    /**
     * @param int $id Identifikator polozky
     */
    public function renderEdit($id)
    {
        try {
            $this->setView('../_edit');
            //	nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->model->fetch($id);

            // pravidla pro formular
            $this['edit']['new']['nazev']
                ->addRule(Form::FILLED);

            $this['edit']['new']['pocet_polozek']
                ->setType('number')
                ->addRule(Form::FLOAT)
                ->addRule(Form::RANGE, NULL, array(0, 999));

            $this['edit']['new']['cena']
                ->addRule(Form::FLOAT);

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
        $form = new EditFakturaPolozkaForm;
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }


    /**
     * @throws AbortExceptionAlias
     */
    public function edit(EditFakturaPolozkaForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->update($v['new'], $v['id']);

            //	fresmeruji zpet na editovani faktury
            $this->flashMessage('Záznam byl úspěšně změněn');
            $this->redirect('Faktura:edit', $v['new']['faktura']);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Záznam nebyl změněn');
        }
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
                //	overim ze polozka existuje a zaroven si nactu jake fakture patri
                $v = $this->model->fetch($id);
                $this->model->remove($id);

                $this->flashMessage('Položka byla odebrána');

                //	presmeruji na editovani faktury
                $this->redirect('Faktura:edit', $v['faktura']);
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('FakturaPolozka:default');
            }
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('FakturaPolozka:default');
        }
    }
}