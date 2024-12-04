<?php

/**
 * Description of FakturaPolozkaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\InvoiceItemAddFormFactory;
use App\Factory\Forms\InvoiceItemEditFormFactory;
use App\Model\FakturaPolozkaModel;
use App\Model\FakturaModel;
use Exception;
use Nette\Application\AbortException as AbortExceptionAlias;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class FakturaPolozkaPresenter extends AdminbasePresenter
{
    private FakturaPolozkaModel $fakturaPolozkaModel;
    private FakturaModel $modelFaktura;
    private InvoiceItemAddFormFactory $fakturaPolozkaAddFormFactory;
    private InvoiceItemEditFormFactory $fakturaPolozkaEditFormFactory;

    public function __construct(
        FakturaPolozkaModel        $fakturaPolozkaModel,
        FakturaModel               $fakturaModel,
        InvoiceItemAddFormFactory  $fakturaPolozkaAddFormFactory,
        InvoiceItemEditFormFactory $fakturaPolozkaEditFormFactory
    )
    {
        parent::__construct();
        $this->fakturaPolozkaModel = $fakturaPolozkaModel;
        $this->modelFaktura = $fakturaModel;
        $this->fakturaPolozkaAddFormFactory = $fakturaPolozkaAddFormFactory;
        $this->fakturaPolozkaEditFormFactory = $fakturaPolozkaEditFormFactory;
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
    public function renderAdd(int $id)
    {
        try {
            $this->setView('../_add');
            // overim ze je v systemu evidovana faktura s timto cislem
            $this->modelFaktura->fetchById($id);

            // do vytvorene komponenty vlozim cislo faktury do ktere chci vlozit polozku
            $this['add']->setDefaults(ArrayHash::from(array('faktura' => $id)));
        } catch (BadRequestException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('Faktura:');
        }
    }

    public function createComponentAdd(): Form
    {
        $form = $this->fakturaPolozkaAddFormFactory->create();
        $form->onSuccess = [$this, 'add'];
        return $form;
    }

    /**
     * @param Form $form
     */
    public function add(Form $form)
    {
        try {
            $v = $form->getValues();
            $this->fakturaPolozkaModel->insert($v);
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
     * @throws AbortExceptionAlias
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            // nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->fakturaPolozkaModel->fetchById($id);

            // pravidla pro formular
            $this['edit']['new']['nazev']
                ->addRule(FormAlias::FILLED);

            $this['edit']['new']['pocet_polozek']
                ->setType('number')
                ->addRule(FormAlias::FLOAT)
                ->addRule(FormAlias::RANGE, null, array(0, 999));

            $this['edit']['new']['cena']
                ->addRule(FormAlias::FLOAT);

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
        $form = $this->fakturaPolozkaEditFormFactory->create();
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
            $this->fakturaPolozkaModel->updateItem($v['new'], $v['id']);

            // fresmeruji zpet na editovani faktury
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
    public function actionDrop(int $id)
    {
        try {
            try {
                // overim ze polozka existuje a zaroven si nactu jake fakture patri
                $v = $this->fakturaPolozkaModel->fetchById($id);
                $this->fakturaPolozkaModel->removeItem($id);

                $this->flashMessage('Položka byla odebrána');

                // presmeruji na editovani faktury
                $this->redirect('Faktura:edit', $v['faktura']);
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('FakturaPolozka:default');
            }
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('FakturaPolozka:default');
        }
    }
}
