<?php

namespace App\Factory\Components;

use App\Components\WorkLog\WorkLogControl;
use Nette\Database\Context;

class WorkLogControlFactory
{
    private Context $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function create(): WorkLogControl{
        return new WorkLogControl($this->context);
    }
}