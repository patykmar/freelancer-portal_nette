<?php/** * Description of ZpusobUzavreniPresenter * * @author Martin Patyk */namespace App\AdminModule\Presenters;use App\Grids\Admin\ZpusobUzavreniGrid;use App\Model\ZpusobUzavreniModel;use Exception;use Nette\Application\AbortException;use Nette\Database\Context;use Nette\Forms\Form as FormAlias;use Tracy\Debugger;use Nette\InvalidArgumentException;use App\Forms\Admin\Add\ZpusobUzavreniForm as AddZpusobUzavreniForm;use App\Forms\Admin\Edit\ZpusobUzavreniForm as EditZpusobUzavreniForm;class ZpusobUzavreniPresenter extends AdminbasePresenter{    private ZpusobUzavreniModel $zpusobUzavreniModel;    private Context $netteModel;    public function __construct(Context $context, ZpusobUzavreniModel $zpusobUzavreniModel)    {        parent::__construct();        $this->zpusobUzavreniModel = $zpusobUzavreniModel;        $this->netteModel = $context;    }    /**     * Cast DEFAULT, definice Gridu     */    protected function createComponentGrid(): ZpusobUzavreniGrid    {        return new ZpusobUzavreniGrid($this->netteModel->table('zpusob_uzavreni'));    }    public function renderDefault()    {        $this->setView('../_default');    }    /**     * Cast ADD     */    public function actionAdd()    {        // nastavim rule pro formular        $this['add']['nazev']            ->addRule(FormAlias::FILLED);        $this['add']['koeficient_cena']            ->setType('number')            ->addRule(FormAlias::FLOAT);        $this->setView('../_add');    }    public function createComponentAdd(): AddZpusobUzavreniForm    {        $form = new AddZpusobUzavreniForm;        $form->onSuccess[] = [$this, 'add'];        return $form;    }    /**     * @throws AbortException     */    public function add(AddZpusobUzavreniForm $form)    {        try {            $v = $form->getValues();            $this->zpusobUzavreniModel->insert($v);        } catch (Exception $exc) {            Debugger::log($exc->getMessage());            $form->addError('Nový záznam nebyl přidán');        }        $this->flashMessage('Nový záznam byl přidán');        $this->redirect('default');    }    /**     * Cast EDIT     * @param int $id Identifikator polozky     * @throws AbortException     */    public function renderEdit(int $id)    {        try {            $this->setView('../_edit');            // nactu hodnoty pro editaci, pritom overim jestli hodnoty existuji            $v = $this->zpusobUzavreniModel->fetch($id);            // odeberu idecko z pole            // $v->offsetUnset('id');            // nastavim rule pro formular            $this['edit']['new']['nazev']                ->addRule(FormAlias::FILLED);            $this['edit']['new']['koeficient_cena']                ->setType('number')                ->addRule(FormAlias::FLOAT);            // upravene hodnoty odeslu do formulare            $this['edit']->setDefaults(array('id' => $id, 'new' => $v));        } catch (InvalidArgumentException $exc) {            $this->flashMessage($exc->getMessage());            $this->redirect('default');        }    }    public function createComponentEdit(): EditZpusobUzavreniForm    {        $form = new EditZpusobUzavreniForm();        $form->onSuccess[] = [$this, 'edit'];        return $form;    }    /**     * @throws AbortException     */    public function edit(EditZpusobUzavreniForm $form)    {        try {            $v = $form->getValues();            $this->zpusobUzavreniModel->update($v['new'], $v['id']);        } catch (Exception $exc) {            Debugger::log($exc->getMessage());            $form->addError('Záznam nebyl změněn');        }        $this->flashMessage('Záznam byl úspěšně změněn');        $this->redirect('default');    }    /**     * Cast DROP     * @param int $id Identifikator polozky     * @throws AbortException     */    public function actionDrop(int $id)    {        try {            $this->zpusobUzavreniModel->fetch($id);            $this->zpusobUzavreniModel->removeItem($id);            $this->flashMessage('Položka byla odebrána'); // Položka byla odebrána            $this->redirect('ZpusobUzavreni:default'); // change it !!!        } catch (InvalidArgumentException $exc) {            $this->flashMessage($exc->getMessage());            $this->redirect('ZpusobUzavreni:default'); // change it !!!        } catch (Exception $exc) {            $this->flashMessage('Položka nebyla odabrána, zkontrolujte závislosti na položku');            $this->redirect('ZpusobUzavreni:default'); // change it !!!        }    }}