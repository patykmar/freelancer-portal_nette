<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use App\Model\ChangeStavModel;
use App\Model\FormatDatumModel;
use App\Model\FrontaModel;
use App\Model\IncidentStavModel;
use App\Model\PrioritaModel;
use App\Model\StavCiModel;
use App\Model\TimeZoneModel;
use App\Model\TypChangeModel;
use App\Model\TypOsobyModel;
use App\Model\ZemeModel;
use Nette\Database\Context;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class SimpleDataGridFactory
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
    private function create(string $tableName): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()
            ->setDataSource($this->context->table($tableName));
        $dataGrid->addColumnText('nazev', 'Název');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }
}