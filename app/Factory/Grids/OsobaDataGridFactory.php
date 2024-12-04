<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use App\Model\FrontaOsobaModel;
use App\Model\OsobaModel;
use Nette\Database\Context;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class OsobaDataGridFactory
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
            $this->context->table(OsobaModel::TABLE_NAME)
                ->select('osoba.id, jmeno, prijmeni')
                ->select('typ_osoby.nazev AS typ_osoby')
                ->select('firma.nazev AS firma')
        );

        $dataGrid->addColumnText('jmeno', 'Jméno');
        $dataGrid->addColumnText('prijmeni', 'Příjmení');
        $dataGrid->addColumnText('typ_osoby', 'Typ osoby');
        $dataGrid->addColumnText('firma', 'Firma');

        $dataGrid->addAction('nove_heslo', 'Vygeneruj nové heslo', 'generujNoveHeslo')
            ->setClass('btn btn-info btn-sm')
            ->setTitle('New password');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }

    /**
     * @throws DataGridException
     */
    public function createFrontaOsobaGrid(): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()->setDataSource(
            $this->context->table(FrontaOsobaModel::TABLE_NAME)
                ->select('fronta_osoba.id')
                ->select('fronta.nazev AS fronta')
                ->select('CONCAT(osoba.jmeno, " ",osoba.prijmeni) AS osoba')
        );

        $dataGrid->addColumnText('id', 'ID');
        $dataGrid->addColumnText('fronta', 'Fronta');
        $dataGrid->addColumnText('osoba', 'Osoba');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }
}