<?php

namespace App\Form\Klient\Add;

/**
 * Description of IncidentForm
 *
 * @author Martin Patyk
 */

use Nette\ComponentModel\IContainer;
use App\Form\Admin\Add;

class IncidentForm extends Add\IncidentForm
{
    public function __construct(IContainer $parent = NULL, $name = NULL)
    {
        parent::__construct($parent, $name);
        dump($this->presenter->userId);
        exit;
        return $this;
    }

    public function setOsoba()
    {
    }
}