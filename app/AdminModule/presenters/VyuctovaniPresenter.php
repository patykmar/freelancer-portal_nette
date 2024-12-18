<?php

/**
 * Description of VyuctovaniPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\ForeignKeyAddFormFactory;
use App\Factory\Grids\VyuctovaniDataGridFactory;
use App\Model\FakturaModel;
use App\Model\FirmaModel;
use App\Model\IncidentModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class VyuctovaniPresenter extends AdminbasePresenter
{
    private FakturaModel $fakturaModel;
    private IncidentModel $modelIncident;
    private FirmaModel $firmaModel;
    private ForeignKeyAddFormFactory $foreignKeyAddFormFactory;
    private VyuctovaniDataGridFactory $gridFactory;

    public function __construct(
        FakturaModel              $fakturaModel,
        IncidentModel             $modelIncident,
        FirmaModel                $firmaModel,
        ForeignKeyAddFormFactory  $foreignKeyAddFormFactory,
        VyuctovaniDataGridFactory $gridFactory
    )
    {
        parent::__construct();
        $this->fakturaModel = $fakturaModel;
        $this->modelIncident = $modelIncident;
        $this->firmaModel = $firmaModel;
        $this->foreignKeyAddFormFactory = $foreignKeyAddFormFactory;
        $this->gridFactory = $gridFactory;
    }

    /**
     * Cast DEFAULT, definice Gridu
     * incident_stav:=4 == vyreseno
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        return $this->gridFactory->create();
    }

    /**
     * Zobrazi pocet nezauctovanych uzavrenych tiketu a celkovou cenu k proplaceni
     * pro kazdou firmu zvlast.
     * incident_stav:=5 == uzavreno
     * @throws DataGridException
     */
    protected function createComponentGridNezauctovanaPrace(): DataGrid
    {
        return $this->gridFactory->create();
    }

    public function renderDefault()
    {
        $this->template->h1 = 'Nezaucnotavana prace';
        $this->setView('../_default');
    }

    /**
     * Vychozi zobrazeni nevyfakturovanou praci
     */
    public function renderOld(): void
    {
        $model = $this->modelIncident->retrieveListOfUnpaidWork();
        $this->template->items = $model;
    }

    /**
     * Vygeneruje fakturu pro firmu dle $id.
     * Nacte si vsechny tikety, ktere jsou uzavrene od firmy
     * @param int $id Identifikator firma, odberatel pro kterou se generuje faktura
     * @throws AbortException
     * @throws Exception
     */
    public function actionGenerujFakturu(int $id): void
    {
        try {
            //zjistim identitu prave prihlaseneho cloveka
            $identita = $this->getUser()->getIdentity();

            //nactu si inicialy od dodavatele a odberatele
            $novaFaktura = $this->firmaModel->fetchDodavatelOdberatel($identita->__get("data")['firma'], $id);

            //existuje firma?
            if (!$novaFaktura) {
                throw new InvalidArgumentException('Dodavatel nebo odberatel nenelezen!');
            }

            // pridam k fakture udaje nezbytne pro vygenerovani
            $novaFaktura->offsetSet('splatnost', 14);
            $novaFaktura->offsetSet('datum_vystaveni', new DateTime());
            $novaFaktura->offsetSet('datum_splatnosti', DateTime::from(DateTime::DAY * $novaFaktura['splatnost']));
            $novaFaktura->offsetSet('forma_uhrady', 1);
            $novaFaktura->offsetSet('vytvoril', $this->getUser()->getId());
            $novaFaktura->offsetSet('ks', 3658);
            $novaFaktura->offsetSet('id_odberatel', $id);

            //vygeneruj novou fakturu
            $this->fakturaModel->insertFromTickets($novaFaktura);
            #//presmeruji aplikaci, aby vygenerovala PDF soubor
            #$this->redirect('Faktura:GeneratePdf', $this->Model->getLastId());
            $this->redirect('Faktura:');
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    /*************************************** PART ADD **************************************/

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
     * @throws AbortException
     */
    public function add(Form $form)
    {
        try {
            $v = $form->getValues();
            $v->offsetUnset('id');
            $this->fakturaModel->insert($v);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Nový záznam nebyl přidán');
        }
        $this->flashMessage('Nový záznam byl přidán');
        $this->redirect('default');
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
            //odeberu idecko z pole
//            $v->offsetUnset('id');
            //upravene hodnoty odeslu do formulare
            //nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $this['edit']->setDefaults($this->fakturaModel->fetchById($id));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Form
    {
        $form = $this->foreignKeyAddFormFactory->create();
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
            $this->fakturaModel->updateItem($v['new'], $v['id']);
        } catch (Exception $exc) {
            Debugger::log($exc->getMessage());
            $form->addError('Záznam nebyl změněn');
        }
        $this->flashMessage('Záznam byl úspěšně změněn');
        $this->redirect('default');
    }

    /*************************************** PART DROP **************************************/

    /**
     * @param int $id Identifikator polozky
     * @throws AbortException
     */
    public function actionDrop(int $id)
    {
        try {
            $this->fakturaModel->fetchById($id);
            $this->fakturaModel->remove($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('VyuctovaniPresenter:default'); //change it !!!
        } catch (Exception $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('VyuctovaniPresenter:default'); //change it !!!
        }
    }
}
