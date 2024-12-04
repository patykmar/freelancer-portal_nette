<?php

namespace App\Forms\Klient\Edit;

/**
 * Jedna se jednodussi verzi formulare pro klienty
 *
 * @author Martin Patyk
 */

use App\Forms\Admin;
use Nette\Forms\Container;

/** @deprecated */
class IncidentForm extends Admin\Edit\IncidentForm
{
    public function __construct()
    {
        parent::__construct();
        // vymenim selectbox za inpust text
        /** @var Container $container */
        $container = $this['new'];
        $container->offsetUnset('incident_stav');
        $container->addText('incident_stav', 'Stav incidentu:')
            ->setHtmlAttribute('readonly', 'readonly');
        $container->offsetUnset('osoba_prirazen');
        $container->addText('osoba_prirazen', 'Přiřazeno:')
            ->setHtmlAttribute('readonly', 'readonly');

        $container->offsetUnset('fronta');
        $container->addText('fronta', 'Fronta:')
            ->setHtmlAttribute('readonly', 'readonly');
        $container->offsetUnset('ci');
        $container->addText('ci', 'Produkt:')
            ->setHtmlAttribute('readonly', 'readonly');
        $container->offsetUnset('osoba_vytvoril');
        $container->addText('osoba_vytvoril', 'Vytvořil:')
            ->setHtmlAttribute('readonly', 'readonly');
        $container->offsetUnset('zpusob_uzavreni');
        $container->offsetUnset('obsah_uzavreni');
        return $this;
    }
}
