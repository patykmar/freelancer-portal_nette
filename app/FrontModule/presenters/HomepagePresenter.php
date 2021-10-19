<?php

namespace App\FrontModule;

use App\BasePresenter;
use Nette\Application\AbortException;

/**
 * Homepage presenter.
 */
class HomepagePresenter extends BasePresenter
{
    /**
     * nadefinuji nacteni prihlasovaciho formulare
     * @throws AbortException
     */
    public function actionDefault()
    {
        $this->forward('Sign:in');
    }
}