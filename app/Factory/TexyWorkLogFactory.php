<?php

namespace App\Factory;

use Texy;

class TexyWorkLogFactory
{
    public function create(): Texy
    {
        $texy = new Texy();
        $texy->allowed['heading'] = false;
        $texy->allowed['paragraph'] = false;
        return $texy;
    }
}