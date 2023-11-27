<?php

/**
 * Description of AdminbasePresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Presenters\BasePresenter;
use Nette\Security\Identity;
use Nette\Application\AbortException;
use Nette\Security\IUserStorage;

abstract class AdminbasePresenter extends BasePresenter
{
    /** @var int identifikator prave prihlaseneho uzivatele */
    protected $userId;

    /** @var Identity identita prihlaseneho uzivatele */
    protected $identity;

    /**
     * @throws AbortException
     */
    protected function startup()
    {
        parent::startup();
        $user = $this->getUser();
        if (!$user->isLoggedIn()) {
            if ($user->getLogoutReason() === IUserStorage::INACTIVITY) {
                $this->flashMessage('Byl jste odhlašen z důvodu nečinnosti');
            }
            $this->redirect(':Front:Sign:in');
        }

        $this->userId = $this->getUser()->getId();
        $this->identity = $this->getUser()->getIdentity();

        //pokud nejsi admin, nemas tu co pohledavat
        if (!$this->identity->data['je_admin']) {
            $this->redirect(':Klient:Homepage:');
        }

    }

}
