<?php

namespace PatrickRose\Invoices\Repositories;

use PatrickRose\Invoices\Invoice;

interface InvoiceRepositoryInterface
{

    /**
     * Add an invoice to the repository
     *
     * @param Invoice $invoice The invoice the add
     * @return bool True if add was successful
     */
    public function add(Invoice $invoice): bool;

    /**
     * Get all invoices from this repository
     *
     * @return Invoice[]
     */
    public function getAll(): array;

}
