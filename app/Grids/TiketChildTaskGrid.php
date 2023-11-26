<?phpnamespace App\Grids;use Nette\Database\Table\Selection;use Nette\Utils\Html;use NiftyGrid\DataSource\NDataSource;use NiftyGrid\DuplicateColumnException;use NiftyGrid\Grid;/** * Description of TiketTaskGrid * * @author Martin Patyk */class TiketChildTaskGrid extends Grid{    private $model;    public function __construct(Selection $db)    {        parent::__construct();        $this->model = $db;    }    /**     * @throws DuplicateColumnException     */    protected function configure($presenter)    {        //Vytvoříme si zdroj dat pro Grid        //Při výběru dat vždy vybereme id        $source = new NDataSource(            $this->model                ->select('incident.id')                ->select('CONCAT(typ_incident.zkratka, incident.id) AS idTxt')                ->select('CONCAT(fronta_osoba.osoba.jmeno," ",fronta_osoba.osoba.prijmeni) AS osoba_prirazen')                ->select('DATE_FORMAT(datum_ukonceni,GET_FORMAT(DATETIME,"EUR")) AS datum_ukonceni')                ->select('priorita.nazev AS priorita')                ->select('incident_stav.nazev AS incident_stav')                ->select('maly_popis')                ->select('CONCAT(osoba_vytvoril.jmeno," ",osoba_vytvoril.prijmeni) AS osoba_vytvoril'));        $this->addColumn('idTxt', 'Název')            ->setRenderer(function ($row) use ($presenter) {                return Html::el('a')->setText($row['idTxt'])->href($presenter->link("Tickets:edit", $row['id']));            });        $this->addColumn('maly_popis', 'Popis', '220px');        $this->addColumn('datum_ukonceni', 'Datum dokončení', '150px');        $this->addColumn('osoba_prirazen', 'Přiřazeno');        $this->addColumn('priorita', 'Priorita');        $this->addColumn('incident_stav', 'Stav');        $this->addColumn('osoba_vytvoril', 'Vytvořil');        //Předáme zdroj        $this->setDataSource($source);    }}