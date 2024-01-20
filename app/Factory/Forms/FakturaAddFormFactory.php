<?php

namespace App\Factory\Forms;

use App\Forms\Admin\Add\FakturaAddForm;

interface FakturaAddFormFactory
{
    function create(): FakturaAddForm;
}
