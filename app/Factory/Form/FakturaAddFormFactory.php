<?php

namespace App\Factory\Form;

use App\Forms\Admin\Add\FakturaAddForm;

interface FakturaAddFormFactory
{
    function create(): FakturaAddForm;
}
