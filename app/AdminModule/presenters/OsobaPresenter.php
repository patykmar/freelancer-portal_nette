<?php

/**
 * Description of OsobaPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Components\SmtpController\SendMailService;
use App\Grids\Admin\OsobaGrid;
use App\Model\FirmaModel;
use App\Model\FormatDatumModel;
use App\Model\TimeZoneModel;
use App\Model\TypOsobyModel;
use App\Model\UserManager;
use Exception;
use App\Forms\Admin\Add\OsobaForm as AddOsobaForm;
use App\Forms\Admin\Edit\OsobaForm as EditOsobaForm;
use App\Model\OsobaModel;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Database\Context;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Tracy\Debugger;
use Nette\InvalidArgumentException;

class OsobaPresenter extends AdminbasePresenter
{
    private OsobaModel $osobaModel;
    private Context $osobaContext;
    private TypOsobyModel $typOsobyModel;
    private FirmaModel $firmaModel;
    private TimeZoneModel $timeZoneModel;
    private FormatDatumModel $formatDatumModel;
    private SendMailService $sendMailService;

    public function __construct(
        OsobaModel       $osobyModel,
        Context          $osobaContext,
        TypOsobyModel    $typOsobyModel,
        FirmaModel       $firmaModel,
        TimeZoneModel    $timeZoneModel,
        FormatDatumModel $formatDatumModel,
        SendMailService  $sendMailService
    )
    {
        parent::__construct();
        $this->osobaModel = $osobyModel;
        $this->osobaContext = $osobaContext;
        $this->typOsobyModel = $typOsobyModel;
        $this->firmaModel = $firmaModel;
        $this->timeZoneModel = $timeZoneModel;
        $this->formatDatumModel = $formatDatumModel;
        $this->sendMailService = $sendMailService;
    }

    /**
     * Cast DEFAULT, definice Gridu
     */
    protected function createComponentGrid(): OsobaGrid
    {
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

    public function createComponentAdd(): AddOsobaForm
    {
        $form = new AddOsobaForm(
            $this->typOsobyModel,
            $this->firmaModel,
            $this->timeZoneModel,
            $this->formatDatumModel
        );
        $form->onSuccess[] = [$this, 'add'];
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
            $v = $this->osobaModel->fetch($id);

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
     * @return EditOsobaForm
     */
    public function createComponentEdit(): EditOsobaForm
    {
        $form = new EditOsobaForm(
            $this->typOsobyModel,
            $this->firmaModel,
            $this->timeZoneModel,
            $this->formatDatumModel
        );
        $form->onSuccess[] = [$this, 'edit'];
        return $form;
    }

    /**
     * @throws AbortException
     */
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
     * @throws AbortException
     */
    public function actionDrop(int $id)
    {
        try {
            try {
                $this->osobaModel->fetch($id);
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
            $item = $this->osobaModel->fetch($id);

            //necham si vygenerovat nove heslo
            $item->offsetSet('password', UserManager::generateNewPassword());

            $item->offsetSet('datum_zmeny_hesla', new DateTime);
            $item->offsetUnset('id');

            $this->sendMailService->vygenerujNoveHeslo($item);

            //password encrypt
            $item->offsetSet('password', UserManager::hashPassword($item['password']));

            //save to db
            $this->osobaModel->update(ArrayHash::from($item), $id);

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
