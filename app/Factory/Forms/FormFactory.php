<?php

namespace App\Factory\Forms;

use Nette\Application\UI\Form;

interface FormFactory
{
    function create(): Form;
}
