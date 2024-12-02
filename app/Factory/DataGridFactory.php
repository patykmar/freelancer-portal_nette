<?php

namespace App\Factory;

use Ublaboo\DataGrid\DataGrid;

class DataGridFactory
{
    public function create(): DataGrid
    {
        return new DataGrid();
    }
}