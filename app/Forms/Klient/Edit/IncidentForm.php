<?php

namespace App\Forms\Klient\Edit;

/**
 * Jedna se jednodussi verzi formulare pro klienty
 *
 * @author Martin Patyk
 */

use Nette\ComponentModel\IContainer;
use App\Forms\Admin\Edit;

/** @deprecated */
class IncidentForm extends Edit\IncidentForm
{
    public function __construct(IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        // vymenim selectbox za inpust text
        $this['new']->offsetUnset('incident_stav');
        $this['new']->addText('incident_stav', 'Stav incidentu:')
            ->setAttribute('readonly', 'readonly');
        $this['new']->offsetUnset('osoba_prirazen');
        $this['new']->addText('osoba_prirazen', 'Přiřazeno:')
            ->setAttribute('readonly', 'readonly');

        $this['new']->offsetUnset('fronta');
        $this['new']->addText('fronta', 'Fronta:')
            ->setAttribute('readonly', 'readonly');
        $this['new']->offsetUnset('ci');
        $this['new']->addText('ci', 'Produkt:')
            ->setAttribute('readonly', 'readonly');
        $this['new']->offsetUnset('osoba_vytvoril');
        $this['new']->addText('osoba_vytvoril', 'Vytvořil:')
            ->setAttribute('readonly', 'readonly');
        $this['new']->offsetUnset('zpusob_uzavreni');
        $this['new']->offsetUnset('obsah_uzavreni');
        return $this;
    }
}
