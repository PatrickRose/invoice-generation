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

    /**
     * Instantiate this repository based on the given
     *
     * @param array $config
     * @return InvoiceRepositoryInterface
     */
    public static function instantiate(array $config): InvoiceRepositoryInterface;

}
