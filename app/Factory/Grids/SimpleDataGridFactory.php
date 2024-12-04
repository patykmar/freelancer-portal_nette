<?php

namespace App\Factory\Grids;

use App\Model\ChangeStavModel;
use App\Model\FirmaModel;
use App\Model\FormatDatumModel;
use App\Model\FrontaModel;
use App\Model\IncidentStavModel;
use App\Model\PrioritaModel;
use App\Model\StavCiModel;
use App\Model\TarifModel;
use App\Model\TimeZoneModel;
use App\Model\TypChangeModel;
use App\Model\TypOsobyModel;
use App\Model\ZemeModel;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class SimpleDataGridFactory
{
    private EmptyDataGridFactory $emptyDataGridFactory;

    /**
     * @param EmptyDataGridFactory $emptyDataGridFactory
     */
    public function __construct(EmptyDataGridFactory $emptyDataGridFactory)
    {
        $this->emptyDataGridFactory = $emptyDataGridFactory;
    }

    /**
     * @throws DataGridException
     */
    public function createChangeStav(): DataGrid
    {
        return $this->create(ChangeStavModel::TABLE_NAME);
    }

    /**
     * @throws DataGridException
     */
    public function createFronta(): DataGrid
    {
        return $this->create(FrontaModel::TABLE_NAME);
    }

    /**
     * @throws DataGridException
     */
    public function createIncidentStav(): DataGrid
    {
        return $this->create(IncidentStavModel::TABLE_NAME);
    }

    /**
     * @throws DataGridException
     */
    public function createPriorita(): DataGrid
    {
        return $this->create(PrioritaModel::TABLE_NAME);
    }

    /**
     * @throws DataGridException
     */
    public function createTypChange(): DataGrid
    {
        return $this->create(TypChangeModel::TABLE_NAME);
    }

    /**
     * @throws DataGridException
     */
    public function createTypOsoby(): DataGrid
    {
        return $this->create(TypOsobyModel::TABLE_NAME);
    }

    /**
     * @throws DataGridException
     */
    public function createStavCi(): DataGrid
    {
        return $this->create(StavCiModel::TABLE_NAME);
    }

    /**
     * @throws DataGridException
     */
    public function createZeme(): DataGrid
    {
        return $this->create(ZemeModel::TABLE_NAME);
    }

    /**
     * @throws DataGridException
     */
    public function createTimeZoneDataGrid(): DataGrid
    {
        $dataGrid = $this->create(TimeZoneModel::TABLE_NAME);
        $dataGrid->addColumnText('cas', 'Časový posun');
        return $dataGrid;
    }

    /**
     * @throws DataGridException
     */
    public function createDateFormat(): DataGrid
    {
        $dataGrid = $this->create(FormatDatumModel::TABLE_NAME);
        $dataGrid->addColumnText('format', 'Format data a času');
        return $dataGrid;
    }

    /**
     * @throws DataGridException
     */
    public function createCompanyDataGrid(): DataGrid
    {
        $dataGrid = $this->create(FirmaModel::TABLE_NAME);
        $dataGrid->addColumnText('ico', 'IČO');
        $dataGrid->addColumnText('dic', 'DIČ');
        $dataGrid->addColumnText('ulice', 'Ulice');
        $dataGrid->addColumnText('obec', 'Obec');

        $dataGrid->addAction('fakturka', 'Nova faktura', 'newInvoice')
            ->setTitle('New invoice')
            ->setClass('btn btn-success btn-sm');
        return $dataGrid;
    }

    /**
     * @throws DataGridException
     */
    public function createTariffDataGrid(): DataGrid
    {
        $dataGrid = $this->create(TarifModel::TABLE_NAME);
        $dataGrid->addColumnText('cena', 'Cana tarifu');
        $dataGrid->addAction('goToSla', 'Go to SLA', 'Sla:')
            ->setTitle('Go to SLA')
            ->setClass('btn btn-info btn-sm');
        return $dataGrid;
    }

    /**
     * @throws DataGridException
     */
    private function create(string $tableName): DataGrid
    {
        $dataGrid = $this->emptyDataGridFactory->create($tableName);
        $dataGrid->addColumnText('nazev', 'Název');
        return $dataGrid;
    }
}