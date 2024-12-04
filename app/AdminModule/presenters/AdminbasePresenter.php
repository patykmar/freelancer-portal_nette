<?php

/**
 * Description of AdminbasePresenter
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;

use App\Presenters\BasePresenter;
use Nette\Application\AbortException;
use Nette\Security\User;
use Nette\Security\SimpleIdentity;

abstract class AdminbasePresenter extends BasePresenter
{
    protected int $userId;
    protected ?SimpleIdentity $identity;

    /**
     * @throws AbortException
     */
    protected function startup(): void
    {
        parent::startup();
        $user = $this->getUser();
        if (!$user->isLoggedIn()) {
            if ($user->getLogoutReason() === User::LogoutInactivity) {
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
