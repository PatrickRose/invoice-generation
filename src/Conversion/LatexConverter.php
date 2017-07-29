<?php

namespace PatrickRose\Invoices\Conversion;

use PatrickRose\Invoices\Exceptions\RuntimeException;
use PatrickRose\Invoices\Invoice;
use PatrickRose\Invoices\MasterInvoice;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

class LatexConverter extends AbstractTwigConverter
{

    /**
     * @var string Temporary directory
     * @see getTempDir
     */
    private $tempDir = false;

    /**
     * Convert an invoice into a new format
     *
     * @param Invoice $invoice The invoice to convert to a file
     * @param string $filePath The file path to store the invoice in
     * @return bool Whether the conversion was successful
     */
    public function convertInvoice(Invoice $invoice, string $filePath): bool
    {
        return $this->convert(
            $this->getInvoiceTemplate(),
            $invoice->toArray(),
            $invoice->getReference(),
            $filePath
        );
    }

    /**
     * Convert a master invoice into a new form
     *
     * @param MasterInvoice $invoice The invoice to convert to a file
     * @param string $filePath The file path to store the invoice in
     * @return bool Whether the conversion was successful
     * @return string
     */
    public function convertMasterInvoice(MasterInvoice $invoice, string $filePath): bool
    {
        return $this->convert(
            $this->getMasterTemplate(),
            [
                'fees' => $invoice->getFees(),
                'expenses' => $invoice->getExpenses()
            ],
            'master',
            $filePath
        );
    }

    /**
     * Check if this converter is available
     *
     * @return bool
     */
    public static function isAvailable(): bool
    {
        // Don't want this to warn
        @exec('which latexmk', $output, $return);

        return $return === 0;
    }

    /**
     * Get the temporary directory
     *
     * Needed because the latex template in question requires the .cls
     * file in the same directory.
     *
     * Thanks latex. Thatex.
     */
    private function getTempDir()
    {
        if ($this->tempDir === false) {
            exec('mktemp -d', $output, $return);

            if ($return != 0 || count($output) == 0) {
                throw new RuntimeException('Unable to create temporary directory');
            }

            $this->tempDir = $output[0];

            if ($this->tempDir[-1] !== '/') {
                $this->tempDir .= '/';
            }

            // Copy the invoice into that directory
            copy(
                __DIR__ . '/../../dist/invoice.cls',
                $this->tempDir . 'invoice.cls'
            );
        }

        return $this->tempDir;
    }

    private function convert(
        TemplateWrapper $template,
        array $templateData,
        string $prefix,
        string $filePath
    ): bool
    {
        $tempDir = $this->getTempDir();

        file_put_contents(
            $tempDir . $prefix . '.tex',
            $template->render($templateData)
        );

        exec(
            "cd " . escapeshellarg($tempDir) . ";latexmk -pdf -interaction=nonstopmode",
            $output,
            $return
        );

        if ($return != 0) {
            return false;
        }

        return copy($tempDir . $prefix . '.pdf', $filePath);
    }

    /**
     * Instantiate a copy of this class with the given config
     *
     * @return ConverterInterface
     */
    public static function instantiate($config): ConverterInterface
    {
        $loader = new FilesystemLoader($config['template-directory']);
        $environment = new Environment($loader);

        return new static(
            $environment,
            $config['invoice-template'],
            $config['master-template']
        );
    }
}
