<?php

namespace PatrickRose\Invoices\Conversion;
use PatrickRose\Invoices\Invoice;
use PatrickRose\Invoices\MasterInvoice;

/**
 * Interface for converting Invoices into PDFs
 */
interface ConverterInterface
{

    /**
     * Convert an invoice into a new format
     *
     * @param Invoice $invoice The invoice to convert to a file
     * @param string $filePath The file path to store the invoice in
     * @return bool Whether the conversion was successful
     */
    public function convertInvoice(Invoice $invoice, string $filePath): bool;

    /**
     * Convert a master invoice into a new form
     *
     * @param MasterInvoice $invoice The invoice to convert to a file
     * @param string $filePath The file path to store the invoice in
     * @return bool Whether the conversion was successful
     * @return string
     */
    public function convertMasterInvoice(MasterInvoice $invoice, string $filePath): bool;

    /**
     * Check if this converter is available
     *
     * @return bool
     */
    public static function isAvailable(): bool;

    /**
     * Instantiate a copy of this class with the given config
     *
     * @return ConverterInterface
     */
    public static function instantiate($config): ConverterInterface;

}
