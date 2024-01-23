<?php

namespace App\Factory\Forms;

use Nette\Application\UI\Form;
use Nette\Forms\Form as FormAlias;

class TicketTaskFormFactory
{
    private TicketAddFormFactory $ticketAddFormFactory;

    /**
     * @param TicketAddFormFactory $ticketAddFormFactory
     */
    public function __construct(TicketAddFormFactory $ticketAddFormFactory)
    {
        $this->ticketAddFormFactory = $ticketAddFormFactory;
    }

    public function create(int $userId): Form
    {
        $form = $this->ticketAddFormFactory->create($userId);
        // rodic I tasku
        $form->addHidden('incident')
            ->addRule(FormAlias::FILLED);
        // neni treba vybirat typ incidentu
        $form->offsetUnset('typ_incident');
        return $form;
    }

}
