<?php
/*

 * To change this template, choose Tools | Templates

 * and open the template in the editor.

 */

namespace App\Model;

use dibi;
use DibiFluent;
use DibiException;
use DibiDataSource;
use DibiRow;
use Nette\Object;
use Nette\Utils\ArrayHash;

/**
 * Description of BaseModel
 *
 * @author Martin Patyk
 */
abstract class BaseModel extends Object
{
    /* * ******************* Model behaviour ******************** */

    /** @var string table name */
    protected $name;

    /** @var string primary key name */
    protected $primary = 'id';

    /**
     * Return all rows from database table
     * @param array $order
     * @return DibiRow[]
     * @throws DibiException
     */
    public function fetchAll(array $order = array()): array
    {
        return dibi::fetchAll('SELECT * FROM %n', $this->name,
            '%ex',
            (!empty($order) ? array('ORDER BY %by', $order) : null));
    }

    /**
     * Prepare query to db, rest of query can by modify in presenter.
     * @return DibiFluent
     */
    public function fetchFactory(): DibiFluent
    {
        return dibi::select('%n.[id]', $this->name)
            ->from('%n', $this->name);
    }

    /**
     * @param ArrayHash $newItem
     * @return void
     * @throws DibiException
     */
    public function insert(ArrayHash $newItem)
    {
        dibi::query('INSERT INTO %n', $this->name, '%v', $newItem);
    }

    /**
     * @throws DibiException
     */
    public function update(ArrayHash $arr, $id)
    {
        dibi::query('UPDATE %n ', $this->name, ' SET ', $arr, ' WHERE %n=%i LIMIT 1', $this->primary, $id);
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
        dibi::query('UPDATE %n ', $this->name, ' SET ', $arr);
    }

    /**
     * @param int $id
     * @return DibiRow|FALSE
     */
    public function fetch($id)
    {
        return dibi::select('*')
            ->from('%n', $this->name)
            ->where('%n = %i', $this->primary, $id)
            ->fetch();
    }

    /**
     * @param int $id
     * @throws DibiException
     */
    public function remove($id)
    {
        dibi::query('DELETE FROM %n WHERE %n=%i LIMIT 1', $this->name, $this->primary, $id);
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
            ->from('%n', $this->name)
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
        return dibi::fetchPairs('SELECT [id], [uri] FROM %n ', $this->name);
    }

    /**
     * @return string
     * @throws DibiException
     */
    public function fetchAllUriIdPair()
    {
        return dibi::fetchPairs('SELECT [uri], [id] FROM %n ', $this->name);
    }

    /**
     * Vrati identifikator vychoziho prvku    v tabulce
     * @return string identifikator vyhoziho prvku
     * @throws DibiException
     */
    public function fetchDefault()
    {
        return dibi::fetchSingle('SELECT [id] FROM ', $this->name, ' WHERE [vychozi] = %b', TRUE);
    }

    /**
     * Vrati pocet webovych stranek
     * @return string pocet zaznamu
     * @throws DibiException
     */
    public function fetchCount()
    {
        return dibi::fetchSingle('SELECT count(id) FROM %n', $this->name);
    }
}
