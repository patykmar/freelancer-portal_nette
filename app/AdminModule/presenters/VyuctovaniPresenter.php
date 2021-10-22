<?php

/**
 * Description of VyuctovaniPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Model\FakturaModel;
use App\Model\FirmaModel;
use App\Model\IncidentModel;
use DibiException;
use Gridy\Admin\VyuctovaniGrid;
use App\Form\Admin\Add\FkBaseForm as AddFkBaseForm;
use App\Form\Admin\Edit\FkBaseForm as EditFkBaseForm;
use Nette\Application\AbortException;
use Nette\DateTime;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;

class VyuctovaniPresenter extends AdminbasePresenter
{

    /** @var FakturaModel */
    private $model;

    /** @var IncidentModel Description */
    private $modelIncident;

    public function __construct()
    {
        parent::__construct();
        $this->model = new FakturaModel;
        $this->modelIncident = new IncidentModel;
    }

    /**
     * Cast DEFAULT, definice Gridu
     * incident_stav:=4 == vyreseno
     */
    protected function createComponentGrid()
    {
        return new VyuctovaniGrid($this->context->database->context
            ->table('incident'));
    }

    /**
     * Zobrazi pocet nezauctovanych uzavrenych tiketu a celkovou cenu k proplaceni
     * pro kazdou firmu zvlast.
     * incident_stav:=5 == uzavreno
     */
    protected function createComponentGridNezauctovanaPrace()
    {
        return new VyuctovaniGrid($this->context->database->context
            ->table('incident')
            ->where(array(
                'incident_stav' => 5,
                'faktura' => NULL,
            )));
    }

    public function renderDefault()
    {
        $this->template->h1 = 'Nezaucnotavana prace';
        $this->setView('../_default');
    }

    /**
     * Vychozi zobrazeni nevyfakturovanou praci
     */
    public function renderOld()
    {
        $model = $this->modelIncident->fetchFactory();
        $model->select('[firma].[nazev]')
            ->select('[firma].[id]')->as('firma_id')
            ->select('count(incident.id)')->as('pocet_incidentu')
            ->select('sum(tarif.cena * sla.cena_koeficient * zpusob_uzavreni.cana_koeficient)')->as('cena_nevyuctovano')
            ->leftJoin('ci')->on('(incident.ci = ci.id)')
            ->leftJoin('firma')->on('(ci.firma = firma.id)')
            ->leftJoin('tarif')->on('(ci.tarif = tarif.id)')
            ->leftJoin('sla')->on('(sla.priorita = incident.priorita AND sla.typ_incident = incident.typ_incident AND sla.tarif = ci.tarif)')
            ->leftJoin('zpusob_uzavreni')->on('(incident.zpusob_uzavreni = zpusob_uzavreni.id)')
            ->where('incident_stav = %i', 5)
            ->and('faktura')->is(NULL)
            ->groupBy('ci.firma');
        $this->template->items = $model;
    }

    public function renderTest()
    {
    }

    /**
     * Vygeneruje fakturu pro firmu dle $id.
     * Nacte si vsechny tikety, ktere jsou uzavrene od firmy
     * @param int $id Identifikator firma, odberatel pro kterou se generuje faktura
     * @throws DibiException|AbortException
     */
    public function actionGenerujFakturu($id)
    {
        try {
            //	zjistim identitu prave prihlaseneho cloveka
            $identita = $this->getUser()->getIdentity();

            //	nactu si inicialy od dodavatele a odberatele
            $novaFaktura = FirmaModel::fetchDodavatelOdberatel($identita->data['firma'], $id);

            //	existuje firma?
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

            //	vygeneruj novou fakturu
            $this->model->insertFromTickets($novaFaktura);
            #//	presmeruji aplikaci, aby vygenerovala PDF soubor
            #$this->redirect('Faktura:GeneratePdf', $this->model->getLastId());
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

    public function createComponentAdd()
    {
        $form = new AddFkBaseForm;
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(AddFkBaseForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->insert($v);
        } catch (DibiException $exc) {
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
        $form = new EditFkBaseForm;
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function edit(EditFkBaseForm $form)
    {
        try {
            $v = $form->getValues();
            $this->model->update($v['new'], $v['id']);
        } catch (DibiException $exc) {
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
    public function actionDrop($id)
    {
        try {
            $this->model->fetch($id);
            $this->model->remove($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('VyuctovaniPresenter:default'); //	change it !!!
        } catch (DibiException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('VyuctovaniPresenter:default'); //	change it !!!
        }
    }
}