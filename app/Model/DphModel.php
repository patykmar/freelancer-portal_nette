<?phpnamespace App\Model;use dibi;/** * Description of DphModel * * @author Martin Patyk */final class DphModel extends BaseModel{    /** @var string nazev tabulky */    protected $tableName = 'dph';    /**     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari     * @return array id, zazev     */    public static function fetchPairs()    {        return dibi::select('[id]')            ->select('[nazev]')            ->from('[dph]')            ->fetchPairs();    }}