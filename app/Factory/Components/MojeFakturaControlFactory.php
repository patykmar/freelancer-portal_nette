<?php

namespace App\Factory\Components;

use App\Components\MojeFaktura\MojeFakturaControl;

interface MojeFakturaControlFactory
{
    function create(): MojeFakturaControl;
}
