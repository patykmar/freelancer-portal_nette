<?php

namespace App\Forms\Admin\Edit;

/**
 * Description of FirmaForm
 *
 * @author Martin Patyk
 */

use App\Model\ZemeModel;
use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

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
