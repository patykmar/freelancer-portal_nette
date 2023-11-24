<?php

namespace App\Model;

use dibi;
use DibiFluent;
use DibiException;
use DibiDataSource;
use DibiRow;
use Nette\Database\Context;
use Nette\Database\IRow;
use Nette\Object;
use Nette\Utils\ArrayHash;

/**
 * Description of BaseModel
 *
 * @author Martin Patyk
 * @deprecated use {@link BaseNDbModel}
 */
abstract class BaseModel extends Object
{
    /* * ******************* Model behaviour ******************** */

    /** @var string table name */
    protected $tableName;

    /** @var string primary key name */
    protected $primary = 'id';

    /** @var Context $explorer */
    protected $explorer;

    public function __construct(Context $database)
    {
        $this->explorer = $database;
    }

    /**
     * Return all rows from database table
     * @param array $order
     * @return DibiRow[]
     * @throws DibiException
     */
    public function fetchAll(array $order = array()): array
    {
        return dibi::fetchAll('SELECT * FROM %n', $this->tableName,
            '%ex',
            (!empty($order) ? array('ORDER BY %by', $order) : null));
    }

    /**
     * Prepare query to db, rest of query can by modify in presenter.
     * @return DibiFluent
     */
    public function fetchFactory(): DibiFluent
    {
        return dibi::select('%n.[id]', $this->tableName)
            ->from('%n', $this->tableName);
    }

    /**
     * @param ArrayHash $newItem
     * @return void
     */
    public function insert(ArrayHash $newItem)
    {
        $this->explorer->table($this->tableName)->insert($newItem);
    }

    /**
     * @param ArrayHash $arr
     * @param int $id
     */
    public function update(ArrayHash $arr, $id)
    {
        $this->explorer->table($this->tableName)
            ->where($this->primary, $id)
            ->update($arr);
    }

    /**
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * !!!!!!!!! Very dangerous method, use very carefully. !!!!!!!!!
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * Umozni modifikaci hodnotu(y) v ramci cele tabulky.
     * @param ArrayHash $arr hodnoty urcene k uprave
     * @throws DibiException
     */
    public function updateAllRows(ArrayHash $arr)
    {
        dibi::query('UPDATE %n ', $this->tableName, ' SET ', $arr);
    }

    /**
     * @param int $id
     * @return IRow|bool
     */
    public function fetch(int $id)
    {
        return $this->explorer
            ->table($this->tableName)
            ->where($this->primary, $id)
            ->fetch();
    }

    /**
     * @param int $id
     * @throws DibiException
     */
    public function remove($id)
    {
        dibi::query('DELETE FROM %n WHERE %n=%i LIMIT 1', $this->tableName, $this->primary, $id);
    }

    /**
     * @param $name
     * @return DibiDataSource
     */
    public function getDataSource($name)
    {
        return dibi::dataSource('SELECT * FROM %n ', $name);
    }

    /**
     * Vrati primarni klic prave vlozeneho zaznamu do databaze
     * @return int primary key
     * @throws DibiException
     */
    public function getLastId()
    {
        return dibi::insertId();
    }

    /**
     * Nactu posledni polozku co byla pridana. Tato funkce doplnuje funkci getLastId(),
     * ktera se neda pouzit vsude.
     */
    public function fetchLastItem()
    {
        return dibi::select('id')
            ->from('%n', $this->tableName)
            ->orderBy('id')
            ->desc()
            ->fetchSingle();
    }

    /**
     * @return string
     * @throws DibiException
     */
    public function fetchAllIdUriPair()
    {
        return dibi::fetchPairs('SELECT [id], [uri] FROM %n ', $this->tableName);
    }

    /**
     * @return string
     * @throws DibiException
     */
    public function fetchAllUriIdPair()
    {
        return dibi::fetchPairs('SELECT [uri], [id] FROM %n ', $this->tableName);
    }

    /**
     * Vrati identifikator vychoziho prvku    v tabulce
     * @return string identifikator vyhoziho prvku
     * @throws DibiException
     */
    public function fetchDefault()
    {
        return dibi::fetchSingle('SELECT [id] FROM ', $this->tableName, ' WHERE [vychozi] = %b', TRUE);
    }

    /**
     * Vrati pocet webovych stranek
     * @return string pocet zaznamu
     * @throws DibiException
     */
    public function fetchCount()
    {
        return dibi::fetchSingle('SELECT count(id) FROM %n', $this->tableName);
    }
}
