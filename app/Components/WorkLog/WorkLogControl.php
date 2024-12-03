<?php/** * Control for show WL in tickets * * @author Martin Patyk */namespace App\Components\WorkLog;use App\Model\IncidentLogModel;use App\Model\IncidentModel;use Nette\Application\UI\Control;use Nette\ComponentModel\IContainer;use Nette\Database\Context;use Texy\Texy;class WorkLogControl extends Control{    /** @var IncidentModel $model */    private $model;    /** @var Texy */    private $texy;    public function __construct(Context $context, IContainer $parent = null, $name = null)    {        parent::__construct($parent, $name);        $this->model = new IncidentLogModel($context);        $this->texy = new Texy;        $this->texy->encoding = 'utf-8';        $this->texy->setOutputMode(Texy::XHTML1_TRANSITIONAL);        $this->texy->allowed['heading'] = false;        $this->texy->allowed['paragraph'] = false;    }    public function render($id)    {        $template = $this->template;        $template->setFile(__DIR__ . '/wl.latte');        $template->getLatte()->addFilter('texyWl', [$this->texy, 'process']);        $this->template->items = $this->model->fetchAllByIncidentId($id);        $template->render();    }}