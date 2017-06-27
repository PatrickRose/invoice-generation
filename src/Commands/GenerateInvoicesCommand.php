<?php

namespace PatrickRose\Invoices\Commands;

use PatrickRose\Invoices\Config\ConfigInterface;
use PatrickRose\Invoices\Invoice;
use PatrickRose\Invoices\MasterInvoice;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateInvoicesCommand extends Command
{
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        parent::__construct('generate');

        $this->config = $config;
    }


    protected function configure()
    {
        $this->setDescription('Generate the invoices')
            ->addOption('no-master', null, InputOption::VALUE_NONE, 'Don\'t generate the master template');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        if (!$this->config->has('invoices'))
        {
            throw new \LogicException('No invoices found');
        }

        $invoices = $this->config->get('invoices');

        $tempDir = __DIR__ . '/../../tmp/tex/' . time();

        mkdir($tempDir, 0775, true);

        $masterInvoice = new MasterInvoice();

        foreach($invoices as $reference => $values)
        {
            $invoice = new Invoice(
                $reference,
                $values['payee'],
                $values['date'],
                $values['fees'],
                $values['expenses'] ?? []
            );

            $invoice->generateTexFile($tempDir);
            $masterInvoice->addInvoice($invoice);
        }

        $masterInvoice->generateTexFile($tempDir);

        copy(__DIR__ . '/../../dist/invoice.cls', "$tempDir/invoice.cls");
        $escapeTempDir = escapeshellarg($tempDir);
        exec("cd $escapeTempDir; latexmk -pdf -interaction=nonstopmode");

        mkdir(__DIR__ . '/../../output', 0775);
        exec("cp $escapeTempDir/*.pdf " . escapeshellarg(__DIR__ . '/../../output'));
        exec("rm -rf $escapeTempDir");
    }
}
