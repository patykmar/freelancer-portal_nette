<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use App\Model\TypIncidentModel;
use Nette\Database\Context;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class TypIncidentDataGridFactory
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
    public function create(): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()
            ->setDataSource($this->context->table(TypIncidentModel::TABLE_NAME));

        $dataGrid->addColumnText('nazev', 'Název');
        $dataGrid->addColumnText('zkratka', 'Zkratka');
        $dataGrid->addColumnText('koeficient_cena', 'Koeficient cena');
        $dataGrid->addColumnText('koeficient_cas', 'Koeficient čas');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }
}