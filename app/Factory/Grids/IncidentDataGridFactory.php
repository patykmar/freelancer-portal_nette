<?php

namespace App\Factory\Grids;

use App\Factory\DataGridFactory;
use App\Model\IncidentModel;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class IncidentDataGridFactory
{
    use DataGridFactoryTrait;

    private Explorer $explorer;
    private DataGridFactory $dataGridFactory;

    /**
     * @param Explorer $explorer
     * @param DataGridFactory $dataGridFactory
     */
    public function __construct(Explorer $explorer, DataGridFactory $dataGridFactory)
    {
        $this->explorer = $explorer;
        $this->dataGridFactory = $dataGridFactory;
    }

    /**
     * @throws DataGridException
     */
    public function create(): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()
            ->setDataSource($this->provideSelectionDataSource());

        $dataGrid->addColumnLink('idTxt', 'ID', 'edit');
        $dataGrid->addColumnText('maly_popis', 'Obsah');
        $dataGrid->addColumnText('datum_ukonceni', 'Datum dokončení');
        $dataGrid->addColumnText('osoba_prirazen', 'Specialista');
        $dataGrid->addColumnText('priorita', 'Priorita');
        $dataGrid->addColumnText('stav', 'Stav');
        $dataGrid->addColumnText('faktura', 'FA', 'faktura.vs');
        $dataGrid->addColumnText('nazevFirmy', 'Firma');

        $this->addEditButton($dataGrid);
        $this->addDeleteButton($dataGrid);

        return $dataGrid;
    }

    public function createTicketChildTask(int $parentId): DataGrid
    {
        $dataGrid = $this->dataGridFactory->create()->setDataSource(
            $this->provideSelectionDataSource()->where('incident = ?', $parentId)
        );

        $dataGrid->addColumnText('maly_popis', 'Popis', '220px');
        $dataGrid->addColumnText('datum_ukonceni', 'Datum dokončení', '150px');
        $dataGrid->addColumnText('osoba_prirazen', 'Přiřazeno');
        $dataGrid->addColumnText('priorita', 'Priorita');
        $dataGrid->addColumnText('incident_stav', 'Stav');
        $dataGrid->addColumnText('osoba_vytvoril', 'Vytvořil');

        return $dataGrid;
    }

    private function provideSelectionDataSource(): Selection
    {
        return $this->explorer->table(IncidentModel::TABLE_NAME)
            ->order('id DESC')
            ->select('incident.id AS id')
            ->select('typ_incident.zkratka AS incidentZkratka')
            ->select('CONCAT(typ_incident.zkratka, incident.id) AS idTxt')
            ->select('ci.firma.nazev AS nazevFirmy')
            ->select('incident_stav.nazev AS stav')
            ->select('datum_ukonceni')
            ->select('maly_popis')
            ->select('priorita.nazev AS priorita')
            ->select('faktura')
            ->select('CONCAT(fronta_osoba.osoba.jmeno, " ", fronta_osoba.osoba.prijmeni) AS osoba_prirazen');
    }
}