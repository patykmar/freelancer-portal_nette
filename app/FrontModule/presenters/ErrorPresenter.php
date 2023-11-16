<?php

namespace App\FrontModule\Presenters;

use App\Presenters\BasePresenter;
use Exception;
use Nette;
use Nette\Application\AbortException;
use Tracy\Debugger;

/**
 * Error presenter.
 */
class ErrorPresenter extends BasePresenter
{
    /**
     * @param Exception $exception
     * @return void
     * @throws AbortException
     */
    public function renderDefault(Exception $exception)
    {
        if ($this->isAjax()) { // AJAX request? Just note this error in payload.
            $this->getPayload()->error = true;
            $this->terminate();
        } elseif ($exception instanceof Nette\Application\BadRequestException) {
            $code = $exception->getCode();
            // load template 403.latte or 404.latte or ... 4xx.latte
            $this->setView(in_array($code, array(403, 404, 405, 410, 500)) ? $code : '4xx');
            // log to access.log
            Debugger::log("HTTP code $code: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}", 'access');
        } else {
            $this->setView('500'); // load template 500.latte
            Debugger::log($exception, Debugger::ERROR); // and log exception
        }
    }
}
