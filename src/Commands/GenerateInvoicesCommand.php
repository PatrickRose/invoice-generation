<?php

namespace PatrickRose\Invoices\Commands;

use PatrickRose\Invoices\Config\ConfigInterface;
use PatrickRose\Invoices\Invoice;
use PatrickRose\Invoices\MasterInvoice;
use PatrickRose\Invoices\Repositories\InvoiceRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateInvoicesCommand extends Command
{

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        parent::__construct('generate');
        $this->invoiceRepository = $invoiceRepository;
    }


    protected function configure()
    {
        $this->setDescription('Generate the invoices')
            ->addOption('no-master', null, InputOption::VALUE_NONE, 'Don\'t generate the master template');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $invoices = $this->invoiceRepository->getAll();

        if (count($invoices) == 0)
        {
            throw new \LogicException('No invoices found');
        }

        $tempDir = __DIR__ . '/../../tmp/tex/' . time();

        mkdir($tempDir, 0775, true);

        $masterInvoice = new MasterInvoice();

        foreach($invoices as $invoice)
        {
            $invoice->generateTexFile($tempDir);
            $masterInvoice->addInvoice($invoice);
        }

        $masterInvoice->generateTexFile($tempDir);

        copy(__DIR__ . '/../../dist/invoice.cls', "$tempDir/invoice.cls");
        $escapeTempDir = escapeshellarg($tempDir);
        exec("cd $escapeTempDir; latexmk -pdf -interaction=nonstopmode");

        if (!file_exists(__DIR__ . '/../../output'))
        {
            mkdir(__DIR__ . '/../../output', 0775);
        }

        exec("cp $escapeTempDir/*.pdf " . escapeshellarg(__DIR__ . '/../../output'));
        exec("rm -rf $escapeTempDir");
    }
}
