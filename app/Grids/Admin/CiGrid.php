<?phpnamespace App\Grids\Admin;use NiftyGrid\DataSource\NDataSource;use NiftyGrid\DuplicateButtonException;use NiftyGrid\DuplicateColumnException;use NiftyGrid\Grid;/** * Description of CiGrid * * @author Martin Patyk */class CiGrid extends Grid{    private $model;    public function __construct($db)    {        parent::__construct();        $this->model = $db;    }    /**     * @throws DuplicateButtonException     * @throws DuplicateColumnException     */    protected function configure($presenter)    {        //Vytvoříme si zdroj dat pro Grid        //Při výběru dat vždy vybereme id        $source = new NDataSource($this->model->select('ci.id, ci.nazev, fronta_tier_1.nazev AS fronta_tier_1, fronta_tier_2.nazev AS fronta_tier_2, fronta_tier_3.nazev AS fronta_tier_3, stav_ci.nazev AS stav_ci, tarif.nazev AS tarif, firma.nazev AS firma'));        $this->addColumn('nazev', 'Název');        $this->addColumn('fronta_tier_1', 'Fronta T1', '100px');        $this->addColumn('fronta_tier_2', 'Fronta T2');        $this->addColumn('fronta_tier_3', 'Fronta T3');        $this->addColumn('stav_ci', 'Stav');        $this->addColumn('tarif', 'Tarif');        $this->addColumn('firma', 'Firma');        $this->addButton("edit", "Upravit")            ->setClass("edit")            ->setLink(function ($row) use ($presenter) {                return $presenter->link("edit", $row['id']);            })            ->setAjax(FALSE);        $this->addButton("delete", "Smazat")            ->setClass("delete")            ->setLink(function ($row) use ($presenter) {                return $presenter->link("drop", $row['id']);            })            ->setConfirmationDialog(function ($row) {                return "Opravdu chcete smazat $row[nazev] ?";            })            ->setAjax(FALSE);        //Předáme zdroj        $this->setDataSource($source);    }}