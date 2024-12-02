<?php

namespace App\Factory\Grids;

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
            ->setConfirm("Do you really want to delete?");
    }

}