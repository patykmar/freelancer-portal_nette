<?php

namespace App\Factory\Grids;

use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

trait DataGridFactoryTrait
{
    /**
     * @throws DataGridException
     */
    public function addEditButton(DataGrid $dataGrid): void
    {
        $dataGrid->addAction('edit', 'Edit', 'edit')
            ->setIcon('pencil')
            ->setTitle('Edit row')
            ->setClass('btn btn-secondary btn-sm');
    }

    /**
     * @throws DataGridException
     */
    public function addDeleteButton(DataGrid $dataGrid): void
    {
        $dataGrid->addAction('delete', 'Delete', 'drop')
            ->setClass('btn btn-danger btn-sm')
            ->setTitle('Delete row')
            ->setConfirmation(new StringConfirmation("Do you really want to delete?"));
    }

    public function addColumnDateTime(DataGrid $dataGrid, string $key, string $name): void
    {
        $dataGrid->addColumnDateTime($key, $name)
            ->setFormat('j.n.Y H:i:s');
    }

}