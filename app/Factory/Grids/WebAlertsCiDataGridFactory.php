<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use App\Model\OdCiModel;
use Nette\Database\Context;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class WebAlertsCiDataGridFactory
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
        $dataGrid = $this->dataGridFactory->create()->setDataSource(
            $this->context->table(OdCiModel::TABLE_NAME)
                ->select('od_ci.id')
                ->select('od')
                ->select('ci.nazev AS ci'));
        $dataGrid->addColumnText('od', 'Odesilatel');
        $dataGrid->addColumnText('ci', 'Nazev CI');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }

}