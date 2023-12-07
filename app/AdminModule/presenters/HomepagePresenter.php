<?php

/**
 * Homepage presenter.
 *
 * @author Martin Patyk
 */

namespace App\AdminModule\Presenters;


final class HomepagePresenter extends AdminbasePresenter
{
    public function renderDefault()
    {
        $this->template->anyVariable = 'any value';
    }
}
