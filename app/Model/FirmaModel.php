<?phpnamespace App\Model;use dibi;/** * Description of FirmaModel * * @author Martin Patyk */final class FirmaModel extends BaseModel{    /** @var string nazev tabulky */    protected $name = 'firma';    /**     * Vrati nazev a primarni klic v paru k pouziti nacteni cizich klicu ve formulari     * @return array     */    public function fetchPairs()    {        return $this->explorer->table($this->name)->order('nazev')->fetchPairs('id', 'nazev');    }    /**     * Nacte inicialy dodavatelske a odberatelske firmy     * @param int $dodavatel identifikator doda     */    public static function fetchDodavatelOdberatel($dodavatel, $odberatel)    {        return dibi::select('dodavatel.nazev')->as('dodavatel_nazev')            ->select('dodavatel.ico')->as('dodavatel_ico')            ->select('dodavatel.dic')->as('dodavatel_dic')            ->select('dodavatel.ulice')->as('dodavatel_ulice')            ->select('dodavatel.obec')->as('dodavatel_obec')            ->select('dodavatel.psc')->as('dodavatel_psc')            ->select('zeme_dodavatel.nazev')->as('dodavatel_zeme')            ->select('dodavatel.cislo_uctu')->as('dodavatel_cislo_uctu')            ->select('odberatel.nazev')->as('odberatel_nazev')            ->select('odberatel.ico')->as('odberatel_ico')            ->select('odberatel.dic')->as('odberatel_dic')            ->select('odberatel.ulice')->as('odberatel_ulice')            ->select('odberatel.obec')->as('odberatel_obec')            ->select('odberatel.psc')->as('odberatel_psc')            ->select('zeme_odberatel.nazev')->as('odberatel_zeme')            ->select('odberatel.cislo_uctu')->as('odberatel_cislo_uctu')            ->select('odberatel.iban')->as('odberatel_iban')            ->select('dodavatel.iban')->as('dodavatel_iban')            //vytvorim si VS            #->select('(SELECT CONCAT(YEAR(NOW()),LPAD(count(id)+1,6,"0")) FROM [faktura] WHERE YEAR(datum_vystaveni) = YEAR(NOW()))')->as('vs')            ->from('[firma]')->as('dodavatel')            ->innerJoin('[firma]')->as('odberatel')->on('[odberatel].[id] = %i', $odberatel)            ->innerJoin('[zeme]')->as('zeme_dodavatel')->on('[dodavatel].[zeme] = [zeme_dodavatel].[id]')            ->innerJoin('[zeme]')->as('zeme_odberatel')->on('[odberatel].[zeme] = [zeme_odberatel].[id]')            ->where('dodavatel.id = %i', $dodavatel)            ->fetch();    }}