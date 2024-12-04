<?php

namespace App\Factory;

use Mpdf\Mpdf;
use Mpdf\MpdfException;

class MPdfFactory
{
    /**
     * @throws MpdfException
     */
    public function create(): mPDF
    {
        $mPdf = new mPDF();
        $mPdf->SetDisplayMode('fullpage');
        return $mPdf;
    }
}
