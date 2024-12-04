<?php

/**
 * Description of OsobaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Components\SmtpController\SendMailService;
use App\Factory\Forms\PersonAddFormFactory;
use App\Factory\Forms\PersonEditFormFactory;
use App\Factory\Grids\OsobaDataGridFactory;
use App\Model\UserManager;
use Exception;
use App\Model\OsobaModel;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class OsobaPresenter extends AdminbasePresenter
{
    private OsobaModel $osobaModel;
    private SendMailService $sendMailService;
    private PersonAddFormFactory $personAddFormFactory;
    private PersonEditFormFactory $personEditFormFactory;
    private OsobaDataGridFactory $gridFactory;

    public function __construct(
        OsobaModel            $osobyModel,
        SendMailService       $sendMailService,
        PersonAddFormFactory  $personAddFormFactory,
        PersonEditFormFactory $personEditFormFactory,
        OsobaDataGridFactory  $gridFactory
    )
    {
        parent::__construct();
        $this->osobaModel = $osobyModel;
        $this->sendMailService = $sendMailService;
        $this->personAddFormFactory = $personAddFormFactory;
        $this->personEditFormFactory = $personEditFormFactory;
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
     */
    public function renderAdd()
    {
        $this->setView('../_add');
    }

    public function createComponentAdd(): Form
    {
        $form = $this->personAddFormFactory->create();
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
            try {
                //vygeneruji heslo v plain textu
                $v->offsetSet('password', UserManager::generateNewPassword());
            } catch (InvalidArgumentException $exc) {
                throw new Exception($exc->getMessage());
            }

            //pridam datum vytvoreni
            $v->offsetSet('datum_vytvoreni', new DateTime);

            $this->sendMailService->novaOsoba($v);
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
     * @throws AbortException
     */
    public function renderEdit(int $id)
    {
        try {
            $this->setView('../_edit');
            //nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji
            $v = $this->osobaModel->fetchById($id);

            //odeberu idecko z pole a heslo
//            $v->offsetUnset('id');
//            $v->offsetUnset('password');

            //upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    /**
     * @return Form
     */
    public function createComponentEdit(): Form
    {
        $form = $this->personEditFormFactory->create();
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
            $this->osobaModel->updateItem($v['new'], $v['id']);
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
                $this->osobaModel->fetchById($id);
                $this->osobaModel->remove($id);
                $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
                $this->redirect('Osoba:default'); //change it !!!
            } catch (InvalidArgumentException $exc) {
                $this->flashMessage($exc->getMessage());
                $this->redirect('Osoba:default'); //change it !!!
            }
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('Osoba:default'); //change it !!!
        }
    }

    /**
     * Funkce slouzi k vygenerovani noveho hesla a odeslani uzivateli
     * @param int $id identifikator uzivatele
     * @throws AbortException
     */
    public function actionGenerujNoveHeslo(int $id)
    {
        try {
            // Informace o uzivateli nactene z databaze
            $item = $this->osobaModel->fetchById($id);

            //necham si vygenerovat nove heslo
            $item->offsetSet('password', UserManager::generateNewPassword());

            $item->offsetSet('datum_zmeny_hesla', new DateTime);
            $item->offsetUnset('id');

            $this->sendMailService->vygenerujNoveHeslo($item);

            //password encrypt
            $item->offsetSet('password', UserManager::hashPassword($item['password']));

            //save to db
            $this->osobaModel->updateItem(ArrayHash::from($item), $id);

            $flashMessage = sprintf('Uzivateli %s %s bylo vygenerovano nove heslo', $item['jmeno'], $item['prijmeni']);

            $this->flashMessage($flashMessage);
            $this->redirect('default');
        } catch (BadRequestException $exc) {
            Debugger::log($exc->getMessage());
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }
}
