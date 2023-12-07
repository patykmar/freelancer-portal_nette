<?php

namespace App\Presenters;

use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;


/**
 * Sign in/out Presenters.
 * @property mixed $signInFormSucceeded
 */
class SignPresenter extends BasePresenter
{

    /**
     * Sign-in form factory.
     * @return Form
     */
    protected function createComponentSignInForm(): Form
    {
        $form = new Form;
        $form->addText('username', 'Username:')
            ->setRequired('Please enter your username.');

        $form->addPassword('password', 'Password:')
            ->setRequired('Please enter your password.');

        $form->addCheckbox('remember', 'Keep me signed in');

        $form->addSubmit('send', 'Sign in');

        // call method signInFormSucceeded() on success
        $form->onSuccess[] = $this->signInFormSucceeded;
        return $form;
    }

    /**
     * @throws AbortException
     */
    public function signInFormSucceeded($form)
    {
        $values = $form->getValues();

        if ($values->remember) {
            $this->getUser()->setExpiration('14 days', false);
        } else {
            $this->getUser()->setExpiration('20 minutes');
        }

        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Homepage:');

        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

    /**
     * @throws AbortException
     */
    public function actionOut()
    {
        $this->getUser()->logout();
        $this->flashMessage('You have been signed out.');
        $this->redirect('in');
    }

}
