<?php

/**
 * Description of BaseNDbModel
 *
 * @author Martin Patyk
 */

namespace App\Model;

use Nette;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;

abstract class BaseNDbModel extends Nette\Object
{
    /** @var Selection databaze incidentu */
    private $database;

    public function __construct(Selection $database)
    {
        $this->database = $database;
    }

    /**
     * Vraci jeden zaznam ze zvolene tabulky.
     * @param int $id
     * @return ActiveRow
     */
    public function fetch($id)
    {
        return $this->database->select('*')
            ->wherePrimary($id)
            ->limit(1)
            ->fetch();
    }
}