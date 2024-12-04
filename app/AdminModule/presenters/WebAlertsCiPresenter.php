<?php

/**
 * Description of WebAlertsCiPresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Factory\Forms\EmailLinkToCiFormFactory;
use App\Factory\Grids\WebAlertsCiDataGridFactory;
use App\Model\OdCiModel;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Nette\InvalidArgumentException;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class WebAlertsCiPresenter extends AdminbasePresenter
{
    private OdCiModel $odCiModel;
    private EmailLinkToCiFormFactory $emailLinkToCiFormFactory;
    private WebAlertsCiDataGridFactory $gridFactory;

    public function __construct(
        OdCiModel                  $odCiModel,
        EmailLinkToCiFormFactory   $emailLinkToCiFormFactory,
        WebAlertsCiDataGridFactory $gridFactory
    )
    {
        parent::__construct();
        $this->odCiModel = $odCiModel;
        $this->emailLinkToCiFormFactory = $emailLinkToCiFormFactory;
        $this->gridFactory = $gridFactory;
    }

    /*************************************** PART DEFINE GRIDS *************************************
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

    /*************************************** PART ADD **************************************/

    public function renderAdd()
    {
        $this->setView('../_add');
    }

    public function createComponentAdd(): Form
    {
        $form = $this->emailLinkToCiFormFactory->create();
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
            $this->odCiModel->insertNewItem($v);
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
            // upravene hodnoty odeslu do formulare
            $this['edit']->setDefaults($this->odCiModel->fetchById($id));
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('default');
        }
    }

    public function createComponentEdit(): Form
    {
        $form = $this->emailLinkToCiFormFactory->create();
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
            $this->odCiModel->updateItem($v, $v['id']);
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
            $this->odCiModel->fetchById($id);
            $this->odCiModel->removeItem($id);
            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána
            $this->redirect('WebAlertsCi:default');    // change it !!!
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            $this->redirect('WebAlertsCi:default');    // change it !!!
        } catch (Exception $exc) {
            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');
            $this->redirect('WebAlertsCi:default');    // change it !!!
        }
    }
}
