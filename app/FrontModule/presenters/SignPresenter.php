<?php

namespace App\FrontModule;

use App\BasePresenter;
use Nette;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;

/**
 * Sign in/out presenters.
 */
class SignPresenter extends BasePresenter
{
	/**
	 * Sign-in form factory.
	 * @return Form
	 */
	protected function createComponentSignInForm()
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
			$this->getUser()->setExpiration('14 days', FALSE);
		} else {
			$this->getUser()->setExpiration('20 minutes', TRUE);
		}

		try {
			$this->getUser()->login($values->username, $values->password);
			$this->redirect(':Admin:Homepage:');
		} catch (Nette\Security\AuthenticationException $e) {
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
