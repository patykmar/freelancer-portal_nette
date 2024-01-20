<?php

namespace App\Forms\Admin\Edit;

/**
 * Description of IncidentForm
 *
 * @author Martin Patyk
 */

use Nette\Application\UI\Form as UIForm;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Form;

class IncidentForm extends UIForm
{
    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $this->addHidden('id');
        $new = $this->addContainer('new');
        $new->addText('idTxt', 'Incident:');
        $new->addText('firma_nazev', 'Firma:');
        $new->addText('maly_popis', 'Popis:');
        $new->addSelect('typ_incident', 'Typ incidentu');
        $new->addSelect('priorita', 'Priorita:');
        $new->addSelect('incident_stav', 'Stav incidentu:');
        $new->addSelect('fronta_osoba', 'Přiřazeno:');
        $new->addSelect('ukon', 'Služba:');
        $new->addSelect('ovlivneni', 'Ovlivnění:');
        $new->addSelect('ci', 'Produkt:');
        $new->addSelect('osoba_vytvoril', 'Vytvořil:');
        $new->addSelect('zpusob_uzavreni', 'Způsob uzavření:');
        $new->addText('fronta', 'Fronta:')
            ->setAttribute('readonly', 'readonly');
        $new->addTextArea('obsah', 'Popis požadavku:')
            ->addRule(Form::FILLED);
        $new->addText('datum_vytvoreni', 'Vytvořeno:')
            ->setAttribute('readonly', 'readonly');
        $new->addText('datum_ukonceni', 'Dokončení:')
            ->setAttribute('readonly', 'readonly');
        $new->addText('datum_reakce', 'Reakce:')
            ->setAttribute('readonly', 'readonly');
        $new->addTextArea('wl', 'Záznam práce:');
        $new->addTextArea('obsah_uzavreni', 'Odůvodnění:');
        // Obrana před Cross-Site Request Forgery (CSRF)
        $this->addProtection(IForm::CSRF_PROTECTION_ERROR_MESSAGE);
        // Tlacitko odeslat
        $this->addSubmit('btSbmt', 'Ulož');
        return $this;
    }
}
