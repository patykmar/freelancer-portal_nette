<?php

namespace App\FrontModule\Presenters;

use App\Form\Front\FeedBackNegativeForm;
use App\Model\IncidentModel;
use App\Presenters\BasePresenter;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\ArrayHash;
use Nette\DI\Container;
use Nette\InvalidArgumentException;
use Nette\NotImplementedException;
use Nette\Utils\Strings;

/**
 * Homepage presenter.
 */
class FeedBackPresenter extends BasePresenter
{

    /** @var IncidentModel */
    private $model;

    public function __construct(Container $context)
    {
        parent::__construct($context);
        $this->model = new IncidentModel();
    }

    /**
     * nadefinuji nacteni prihlasovaciho formulare
     * @throws AbortException
     */
    public function actionDefault()
    {
        $this->forward('Sign:in');
    }

    /**
     * Zpracuje pozitivni feedback
     * @throws BadRequestException
     */
    public function renderPositive($id)
    {
        try {
            $v = $this->model->fetchForFeedBack($id);
            $change = new ArrayHash;
            $change->offsetSet('incident_stav', 5); // nastavim stav uzavreno
            $change->offsetSet('identity', $v['osoba_vytvoril']); // predpoklada se, ze na odkaz klikne prijemce mailu
            $change->offsetSet('odezva_cekam', NULL); // neumoznim odeslat feedback
            $this->model->update($change, $id);
            unset($change, $v); // uvolnim prostredky
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            throw new BadRequestException; // forward to error
        }
    }

    /**
     * Zpracuji negativni feedback.
     */
    public function renderNegative($id)
    {
        $this['negative']->setDefaults(array('id' => $id));
    }

    /**
     * Vychozi stranka po odeslani feedbacku. Stranka bude obsahovat pouze
     * informaci s dekovanim o odeslani feedbacku a sama se zavre.
     */
    public function renderClose()
    {
        throw new NotImplementedException('Method renderClose()');
    }

    /**
     * Formular pro zadani negativniho komentare
     */
    public function createComponentNegative($name): FeedBackNegativeForm
    {
        $form = new FeedBackNegativeForm();
        $form->onSuccess[] = callback($this, 'negative');
        return $form;
    }

    /**
     * Zpracovani negativniho feedbacku
     * @throws BadRequestException|AbortException
     */
    public function negative(FeedBackNegativeForm $form)
    {
        try {
            $v = $form->getValues();
            //uvereni zda-li je v db takovy radek
            $dbVal = $this->model->fetchForFeedBack($v['id']);
            $change = new ArrayHash;
            $change->offsetSet('incident_stav', 7); // znovu otevren
            $change->offsetSet('datum_uzavreni', NULL); // zrusim datum uzavreni
            $change->offsetSet('zpusob_uzavreni', NULL); // zrusim zpusob uzavreni
            $change->offsetSet('obsah_uzavreni', NULL); // zrusim text uzavreni
            $change->offsetSet('obsah_uzavreni', NULL); // zrusim text uzavreni
            $change->offsetSet('odezva_cekam', NULL); // znemoznim pridavani dalsiho feedbacku
            $change->offsetSet('odezva_odeslan_pozadavek', NULL); // nastavim znova moznost odeslani feedbacku
            $change->offsetSet('identity', $dbVal['osoba_vytvoril']); // predpoklada se, ze na odkaz klikne prijemce mailu
            $change->offsetSet('wl', '**Vyjádření zákaznika:** <br />' . Strings::trim($v['wl'])); // do WL zapisu feedback od zakaznika
            $this->model->update($change, $v['id']);
            $this->redirect('close');
            unset($change, $v, $dbVal);
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            throw new BadRequestException;
        }
    }
}
