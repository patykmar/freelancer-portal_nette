<?phpnamespace App\Model;use dibi;use DibiException;/** * Description of ChangeStavModel * * @author Martin Patyk */final class ChangeStavModel extends BaseModel{    /** @var string nazev tabulky */    protected $tableName = 'change_stav';    /**     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari     * @return string     * @throws DibiException     */    public static function fetchPairs()    {        return dibi::fetchPairs('SELECT [id], [nazev] FROM [change_stav] ORDER BY [nazev]');    }}