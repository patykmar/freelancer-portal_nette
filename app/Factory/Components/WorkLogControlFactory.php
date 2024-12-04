<?php

namespace App\Factory\Components;

use App\Components\WorkLog\WorkLogControl;
use Nette\Database\Explorer;

class WorkLogControlFactory
{
    private Explorer $explorer;

    /**
     * @param Explorer $explorer
     */
    public function __construct(Explorer $explorer)
    {
        $this->explorer = $explorer;
    }

    public function create(): WorkLogControl{
        return new WorkLogControl($this->explorer);
    }
}