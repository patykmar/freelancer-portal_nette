<?php

namespace App\Factory;

use mPDF;

class MPdfFactory
{
    public function create(): mPDF
    {
        $mPdf = new mPDF('utf-8');
        $mPdf->SetDisplayMode('fullpage');
        return $mPdf;
    }
}
