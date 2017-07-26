<?php


namespace PatrickRose\Invoices\Repositories;


use PatrickRose\Invoices\Exceptions\LockException;
use PatrickRose\Invoices\Invoice;

class JsonRepository implements InvoiceRepositoryInterface
{

    /**
     * @var Invoice[]
     */
    private $invoices;

    /**
     * @var resource
     */
    private $stream;

    public function __construct($filename)
    {
        if (!file_exists($filename)) {
            touch($filename);
        }

        $this->stream = fopen($filename, 'r+');

        if (!flock($this->stream, LOCK_EX | LOCK_NB)) {
            throw new LockException("Unable to lock $filename");
        }

        $invoices = json_decode(stream_get_contents($this->stream), true);

        if ($invoices == null) {
            $this->invoices = [];
        } else {
            foreach ($invoices as $invoice)
            {
                $this->invoices[] = new Invoice(
                    $invoice['reference'],
                    $invoice['payee'],
                    $invoice['date'],
                    $invoice['fees'],
                    $invoice['expenses']
                );
            }
        }
    }

    /**
     * Add an invoice to the repository
     *
     * @param Invoice $invoice The invoice the add
     * @return bool True if add was successful
     */
    public function add(Invoice $invoice): bool
    {
        $this->invoices[] = $invoice;

        return true;
    }

    /**
     * Get all invoices from this repository
     *
     * @return Invoice[]
     */
    public function getAll(): array
    {
        return $this->invoices;
    }

    public function __destruct()
    {
        // Truncate the file, in case it had prettified json
        ftruncate($this->stream, 0);
        fseek($this->stream, 0);
        $toWrite = [];

        foreach($this->invoices as $invoice)
        {
            $toWrite[] = $invoice->toArray();
        }

        fwrite($this->stream, json_encode($toWrite));

        flock($this->stream, LOCK_UN);
        fclose($this->stream);
    }

    /**
     * Instantiate this repository based on the given
     *
     * @param array $config
     * @return InvoiceRepositoryInterface
     */
    public static function instantiate(array $config): InvoiceRepositoryInterface
    {
        return new JsonRepository($config['filename']);
    }
}
