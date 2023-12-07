<?php/** * Description of MojeFakturaControl * * @author patykmar */namespace App\Components\MojeFaktura;use Nette\Application\UI\Control;use mPDF;use Nette\Database\Table\ActiveRow;class MojeFakturaControl extends Control{    private ActiveRow $fakturaData;    private array $fakturaPolozky;    public function __construct(ActiveRow $fakturaData, array $fakturaPolozky)    {        $this->fakturaData = $fakturaData;        $this->fakturaPolozky = $fakturaPolozky;    }    /**     * Exports Invoice template via passed mPDF.     *     * @param mPDF $mpdf     * @param string $name     * @param string $dest     * @return void     */    public function exportToPdf(mPDF $mpdf, $name = null, $dest = null)    {        $fa_zahlavi = clone $this->template;        $fa_zapati = clone $this->template;        $fa_inicialy = clone $this->template;        $fa_telo = clone $this->template;        $fa_cena_celkem_razitko = clone $this->template;        $fa_zahlavi->setFile(__DIR__ . '/fa_zahlavi.latte');        $fa_zapati->setFile(__DIR__ . '/fa_zapati.latte');        $fa_telo->setFile(__DIR__ . '/fakturaBezDph.latte');        $fa_inicialy->setFile(__DIR__ . '/fa_inicialy.latte');        $fa_cena_celkem_razitko->setFile(__DIR__ . '/fa_cena_celkem_razitko.latte');        $fa_zahlavi->title = $this->fakturaData['title'];        $fa_inicialy->faktura = $this->fakturaData;        $fa_telo->fakturaPolozky = $this->fakturaPolozky;        $fa_cena_celkem_razitko->faktura = $this->fakturaData;        #template->render();        #exit;        #$this->generate($template);        // Define the Header/Footer before writing anything so they appear on the first page        $mpdf->SetHTMLHeader((string)$fa_zahlavi);        $mpdf->SetHTMLFooter((string)$fa_zapati);        $mpdf->WriteHTML((string)$fa_inicialy);        $mpdf->WriteHTML((string)$fa_telo);        $mpdf->WriteHTML((string)$fa_cena_celkem_razitko);        if (($name !== '') && ($dest !== null)) {            $mpdf->Output($name, $dest);        } elseif ($dest !== null) {            $mpdf->Output('', $dest);        } else {            $mpdf->Output($name, $dest);        }    }}