<?php

/**
 * Description of OsobaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Grids\Admin\OsobaGrid;
use App\Model\UserManager;
use DibiException;
use DibiRow;
use Exception;
use App\Form\Admin\Add\OsobaForm as AddOsobaForm;
use App\Form\Admin\Edit\OsobaForm as EditOsobaForm;
use App\Model\OsobaModel;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\ArrayHash;
use Nette\Database\Context;
use Nette\DateTime;
use Nette\Diagnostics\Debugger;
use Nette\InvalidArgumentException;
use SendMail\SendMailControler;

class OsobaPresenter extends AdminbasePresenter
{
    /** @var OsobaModel */
    private $osobaModel;

    /** @var Context */
    private $osobaContext;

    public function __construct(OsobaModel $osobaModel, Context $osobaContext)
    {
        parent::__construct();
        $this->osobaModel = $osobaModel;
        $this->osobaContext = $osobaContext;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid()
    {
//        dump($this->context->database->context);
//        exit;
        return new OsobaGrid($this->osobaContext->table('osoba'));
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
        $form = new AddOsobaForm();
        $form->onSuccess[] = callback($this, 'add');
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function add(AddOsobaForm $form)
    {
        try {
            $v = $form->getValues();
            try {
                //	vygeneruji heslo v plain textu
                $v->offsetSet('password', UserManager::generateNewPassword());
            } catch (InvalidArgumentException $exc) {
                throw new Exception($exc->getMessage());
            }

            //	pridam datum vytvoreni
            $v->offsetSet('datum_vytvoreni', new DateTime);
            $mail = new SendMailControler;
            $mail->novaOsoba($v);
            $v->offsetSet('password', UserManager::hashPassword($v['password']));
            $this->osobaModel->insert($v);
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
     * @throws \Nette\Application\AbortException
     */
    public function renderEdit($id)
    {
        try {
            $this->setView('../_edit');
            //	nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->osobaModel->fetch($id);

            //	odeberu idecko z pole a heslo
            $v->offsetUnset('id');
            $v->offsetUnset('password');

            //	upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    /**
     * @return EditOsobaForm
     */
    public function createComponentEdit()
    {
        $form = new EditOsobaForm;
        $form->onSuccess[] = callback($this, 'edit');
        return $form;
    }

    public function edit(EditOsobaForm $form)
    {
        try {
            $v = $form->getValues();
            $this->osobaModel->update($v['new'], $v['id']);
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
     * @throws \Nette\Application\AbortException
     */
    public function actionDrop($id)
    {
        try {
            try {
                $this->osobaModel->fetch($id);
                $this->osobaModel->remove($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('Osoba:default'); //	change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('Osoba:default'); //	change it !!!
            }
        } catch (DibiException $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Osoba:default'); //	change it !!!
        }
    }

    /**
     * Funkce slouzi k vygenerovani noveho hesla a odeslani uzivateli
     * @param int $id identifikator uzivatele
     * @throws AbortException
     */
    public function actionGenerujNoveHeslo($id)
    {
        try {
            /** @var DibiRow|FALSE Informace o uzivateli nactene z databaze */
            $item = $this->osobaModel->fetch($id);

            //	necham si vygenerovat nove heslo
            $item->offsetSet('password', UserManager::generateNewPassword());

            $item->offsetSet('datum_zmeny_hesla', new DateTime);
            $item->offsetUnset('id');

            $mail = new SendMailControler;
            $mail->vygenerujNoveHeslo($item);

            //	password encrypt
            $item->offsetSet('password', UserManager::hashPassword($item['password']));

            //	save to db
            $this->osobaModel->update(ArrayHash::from($item), $id);

            $this->flashMessage('Uzivateli ' . $item['jmeno'] . ' ' . $item['prijmeni'] . ' bylo vygenerovano nove heslo');
            $this->redirect('default');
        } catch (BadRequestException $exc) {
            Debugger::log($exc->getMessage());
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }
}