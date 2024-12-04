<?php

namespace App\Factory\Grids;

use App\AdminModule\Presenters\FakturaPresenter;
use App\Factory\DataGridFactory;
use App\Model\FakturaModel;
use App\Model\FakturaPolozkaModel;
use Nette\Database\Context;
use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class FakturaDataGridFactory
{
    use DataGridFactoryTrait;

    private Context $context;
    private DataGridFactory $dataGridFactory;

    /**
     * @param Context $context
     * @param DataGridFactory $dataGridFactory
     */
    public function __construct(Context $context, DataGridFactory $dataGridFactory)
    {
        $this->context = $context;
        $this->dataGridFactory = $dataGridFactory;
    }

    /**
     * @throws DataGridException
     */
    public function create(FakturaPresenter $presenter): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()->setDataSource(
            $this->context->table(FakturaModel::TABLE_NAME)
                ->select('faktura.id')
                ->select('vs')
                ->select('dodavatel_nazev')
                ->select('odberatel_nazev')
                ->select('splatnost')
                ->select('datum_vystaveni')
                ->select('datum_splatnosti')
                ->select('pdf_soubor')
                ->select('IFNULL(datum_zaplaceni,"Nezaplaceno") AS datum_zaplaceni')
                ->select('CONCAT(vytvoril.jmeno," ",vytvoril.prijmeni) AS vytvoril')
                ->select('IFNULL(pdf_soubor,"- - -") AS faktura_pdf')
        );

        $dataGrid->addColumnLink('vs', 'VS', 'edit');
        $dataGrid->addColumnText('faktura_pdf', 'Faktura PDF')
            ->setRenderer(function ($row) use ($presenter) {
                if ($row['faktura_pdf'] !== '- - -') {
                    return Html::el('a')->setText($row['faktura_pdf'])
                        #->href($presenter->link());
                        ->href($presenter->getAppParameterService()->getBasePath() . '/facka/' . $row['faktura_pdf']);

                } else {
                    return $row['faktura_pdf'];
                }
            });
        $dataGrid->addColumnText('dodavatel_nazev', 'Dodavatel');
        $dataGrid->addColumnText('odberatel_nazev', 'Odberatel');
        $this->addColumnDateTime($dataGrid, 'datum_vystaveni', 'Datum vystaveni');
        $this->addColumnDateTime($dataGrid, 'datum_splatnosti', 'Datum splatnosti');
        $this->addColumnDateTime($dataGrid, 'datum_zaplaceni', 'Datum zaplaceni');
        $dataGrid->addColumnText('vytvoril', 'Vytvoril');
        $dataGrid->addAction("PDF", "PDF", 'generatePdf')
            ->setTitle('Vygeneruj PDF fakturu')
            ->setClass('btn btn-info btn-sm');
        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }

    /**
     * @throws DataGridException
     */
    public function createPolozkyFaktury(int $invoiceId): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()->setDataSource(
            $this->context->table(FakturaPolozkaModel::TABLE_NAME)
                ->select('faktura_polozka.id AS id')
                ->select('cssclass')
                ->select('faktura_polozka.nazev')
                ->select('CONCAT(dph.procent,"%") AS procent')
                ->select('CONCAT(pocet_polozek,jednotka.zkratka) AS pocet_polozek')
                ->select('cena')
                ->select('koeficient_cena')
                ->select('sleva')
                ->select('ROUND(pocet_polozek*cena*dph.koeficient*koeficient_cena*(1-(sleva*0.01)),2) AS cena_celkem')
                ->order("faktura_polozka.id ASC")
        );

        $dataGrid->addColumnText('id', 'ID');
        $dataGrid->addColumnText('nazev', 'Název');
        $dataGrid->addColumnText('cssclass', 'cssclass');
        $dataGrid->addColumnText('procent', 'DPH');
        $dataGrid->addColumnText('pocet_polozek', 'Pocet');
        $dataGrid->addColumnText('koeficient_cena', 'Koeficient');
        $dataGrid->addColumnText('sleva', 'Sleva');
        $dataGrid->addColumnText('cena', 'Cena/jednotka');
        $dataGrid->addColumnText('cena_celkem', 'Cena celkem');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);
        return $dataGrid;
    }
}