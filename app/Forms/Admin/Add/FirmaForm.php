<?php

namespace App\Forms\Admin\Add;

use App\Model\ZemeModel;
use Nette\Application\UI\Form as UIForm;
use Nette\Forms\Form as NetteForm;
use Nette\ComponentModel\IContainer;

/**
 * @deprecated
 */
class FirmaForm extends UIForm
{
    public function __construct(ZemeModel $zemeModel, IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);

    }
}
