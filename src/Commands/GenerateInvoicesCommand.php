<?php

namespace PatrickRose\Invoices\Commands;

use PatrickRose\Invoices\Config\ConfigInterface;
use PatrickRose\Invoices\Conversion\ConverterInterface;
use PatrickRose\Invoices\Exceptions\RuntimeException;
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
    /**
     * @var ConverterInterface
     */
    private $converter;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository, ConverterInterface $converter)
    {
        parent::__construct('generate');
        $this->invoiceRepository = $invoiceRepository;
        $this->converter = $converter;
    }


    protected function configure()
    {
        $this->setDescription('Generate the invoices')
            ->addOption('no-master', null, InputOption::VALUE_NONE, 'Don\'t generate the master template');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $invoices = $this->invoiceRepository->getAll();

        if (count($invoices) == 0) {
            throw new \LogicException('No invoices found');
        }

        $masterInvoice = new MasterInvoice();

        if (!is_dir(__DIR__ . '/../../output')) {
            mkdir(__DIR__ . '/../../output', 0775);
        }

        foreach ($invoices as $invoice) {
            if (!$this->converter->convertInvoice(
                $invoice,
                __DIR__ . '/../../output/' . $invoice->getReference() . '.pdf'
            )
            ) {
                throw new RuntimeException('Unable to convert ' . $invoice->getReference());
            }
            $masterInvoice->addInvoice($invoice);
        }

        $this->converter->convertMasterInvoice($masterInvoice, __DIR__ . '/../../output/master.pdf');
    }
}
