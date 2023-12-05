<?php

namespace App\Grids\Admin;

use http\Encoding\Stream\Inflate;
use NiftyGrid\DuplicateButtonException;

trait AdminGridButtonTrait
{
    /**
     * @throws DuplicateButtonException
     */
    public function editButton($presenter)
    {
        return $this->addButton("edit", "Upravit")
            ->setClass("btn btn-primary btn-sm")
            ->setText("Edit")
            ->setLink(function ($row) use ($presenter) {
                return $presenter->link("edit", $row['id']);
            })
            ->setAjax(false);

    }

    /**
     * @throws DuplicateButtonException
     */
    public function deleteButton($presenter)
    {
        return $this->addButton("delete", "Smazat")
            ->setClass("btn btn-danger btn-sm")
            ->setText("Delete")
            ->setLink(function ($row) use ($presenter) {
                return $presenter->link("drop", $row['id']);
            })
            ->setConfirmationDialog(function ($row) {
                return "Opravdu chcete smazat $row[id] ?";
            })
            ->setAjax(false);
    }

    /**
     * @throws DuplicateButtonException
     */
    public function pdfButton($presenter)
    {
        return $this->addButton("PDF", "PDF")
            ->setClass("btn btn-success btn-sm")
            ->setText("PDF")
            ->setLink(function ($row) use ($presenter) {
                return $presenter->link("generatePdf", $row['id']);
            })
            ->setAjax(false);
    }

    /**
     * @throws DuplicateButtonException
     */
    public function newInvoiceButton($presenter)
    {
        $this->addButton("fakturka", "Nova faktura")
            ->setText("New invoice")
            ->setClass("btn btn-success btn-sm")
            ->setLink(function ($row) use ($presenter) {
                return $presenter->link("Faktura:add", $row['id']);
            })
            ->setAjax(false);
    }

    /**
     * @throws DuplicateButtonException
     */
    public function newPasswordButton($presenter)
    {
        $this->addButton('nove_heslo', 'Vygeneruj nové heslo')
            ->setText("New password")
            ->setClass("btn btn-info btn-sm")
            ->setLink(function ($row) use ($presenter) {
                return $presenter->link("generujNoveHeslo", $row['id']);
            });
    }
}
