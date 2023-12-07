<?php

namespace App\Forms\Klient\Add;

/**
 * Description of IncidentForm
 *
 * @author Martin Patyk
 */

use Nette\ComponentModel\IContainer;
use App\Forms\Admin\Add;

class IncidentForm extends Add\IncidentForm
{
    public function __construct(IContainer $parent = null, $name = null)
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