<?php

namespace App\Presenters;

use ArrayIterator;
use Iterator;
use Nette\Application\UI\Presenter;
use Nette\Forms\Controls\CsrfProtection;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\SubmitButton;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter
{
    public function beforeRender()
    {
        $this->getSession()->isStarted();
        parent::beforeRender();
        if ($this->getSession()->isStarted()) {
            $this->getSession()->start();
        }
    }

    /**
     * Funkce vytvori log string, ktery se pak zapise do databaze
     * @param ArrayIterator $components Komponenty z odeslaneho formulare
     * @return string string pripraveny k ulozeni do databaze
     */
    protected function createLog(Iterator $components): string
    {
        //pripravim si obsah logu pro ulozeni do databaze
        $ci_log = '';

        foreach ($components as $value) {
            //pokud je komponenta tlacitko nebo ochranny token tak to vyrad
            if ($value instanceof SubmitButton || $value instanceof CsrfProtection) {
                continue;
            }
            //pokud je hodnota SelectBox musim nacist hodnoty
            if ($value instanceof SelectBox) {
                //identifikator vybraneho objektu
                $i = $value->value;

                //pokud neni null
                if (!is_null($i)) {
                    $ci_log .= '**' . $value->caption . '**: ' . $value->items[$i] . ' <br />';
                }
            } else {
                $ci_log .= $value->caption . ' ' . $value->value . '<br />';
            }
        }
        return $ci_log;
    }

}
