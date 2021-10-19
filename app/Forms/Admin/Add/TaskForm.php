<?php

namespace App\Form\Admin\Add;

/**
 * Description of AddItaskForm
 *
 * @author Martin Patyk
 */

use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class TaskForm extends IncidentForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        //	rodic I tasku
        $this->addHidden('incident')
            ->addRule(Form::FILLED);
        //	neni treba vybirat typ incidentu
        $this->offsetUnset('typ_incident');
    }
}