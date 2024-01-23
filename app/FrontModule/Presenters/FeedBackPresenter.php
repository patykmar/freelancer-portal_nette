<?php

namespace App\FrontModule\Presenters;

use App\Form\Front\FeedBackNegativeForm;
use App\Model\IncidentModel;
use App\Presenters\BasePresenter;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\InvalidArgumentException;
use Nette\NotImplementedException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Homepage presenter.
 */
class FeedBackPresenter extends BasePresenter
{
    private IncidentModel $incidentModel;

    public function __construct(IncidentModel $incidentModel)
    {
        parent::__construct();
        $this->incidentModel = $incidentModel;
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
    public function renderPositive(int $id)
    {
        try {
            $v = $this->incidentModel->fetchForFeedBack($id);
            $change = new ArrayHash;
            $change->offsetSet('incident_stav', 5); // nastavim stav uzavreno
            $change->offsetSet('identity', $v['osoba_vytvoril']); // predpoklada se, ze na odkaz klikne prijemce mailu
            $change->offsetSet('odezva_cekam', null); // neumoznim odeslat feedback
            $this->incidentModel->updateItem($change, $id);
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
        $form->onSuccess[] = [$this, 'negative'];
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
            $dbVal = $this->incidentModel->fetchForFeedBack($v['id']);
            $change = new ArrayHash;
            $change->offsetSet('incident_stav', 7); // znovu otevren
            $change->offsetSet('datum_uzavreni', null); // zrusim datum uzavreni
            $change->offsetSet('zpusob_uzavreni', null); // zrusim zpusob uzavreni
            $change->offsetSet('obsah_uzavreni', null); // zrusim text uzavreni
            $change->offsetSet('obsah_uzavreni', null); // zrusim text uzavreni
            $change->offsetSet('odezva_cekam', null); // znemoznim pridavani dalsiho feedbacku
            $change->offsetSet('odezva_odeslan_pozadavek', null); // nastavim znova moznost odeslani feedbacku
            $change->offsetSet('identity', $dbVal['osoba_vytvoril']); // predpoklada se, ze na odkaz klikne prijemce mailu
            $change->offsetSet('wl', '**Vyjádření zákaznika:** <br />' . Strings::trim($v['wl'])); // do WL zapisu feedback od zakaznika
            $this->incidentModel->updateItem($change, $v['id']);
            $this->redirect('close');
        } catch (InvalidArgumentException $exc) {
            $this->flashMessage($exc->getMessage());
            throw new BadRequestException;
        }
    }
}
