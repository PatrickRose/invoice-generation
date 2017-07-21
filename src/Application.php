<?php

namespace PatrickRose\Invoices;

use PatrickRose\Invoices\Commands\AddInvoiceCommand;
use PatrickRose\Invoices\Commands\GenerateInvoicesCommand;
use PatrickRose\Invoices\Config\ConfigInterface;
use PatrickRose\Invoices\Config\JsonConfig;
use PatrickRose\Invoices\Repositories\InvoiceRepositoryInterface;
use PatrickRose\Invoices\Repositories\JsonRepository;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{

    const VERSION = '0.1.0';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    public function __construct(ConfigInterface $config = null, InvoiceRepositoryInterface $invoiceRepository = null)
    {
        if ($config === null) {
            $config = new JsonConfig(__DIR__ . '/../config.json');
        }

        $this->config = $config;

        parent::__construct('Invoice generator', self::VERSION);
    }

    /**
     * @return InvoiceRepositoryInterface
     */
    protected function getInvoiceRepository(): InvoiceRepositoryInterface
    {
        if ($this->invoiceRepository == null) {
            $invoiceConfig = $this->config->getDefault(
                'invoice-repository',
                [
                    'class' => JsonRepository::class,
                    'filename' => __DIR__ . '/../invoices.json'
                ]
            );
            $invoiceRepositoryClass = $invoiceConfig['class'];

            $this->invoiceRepository = $invoiceRepositoryClass::instantiate($invoiceConfig);
        }

        return $this->invoiceRepository;
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new AddInvoiceCommand($this->getInvoiceRepository());
        $commands[] = new GenerateInvoicesCommand($this->getInvoiceRepository());

        return $commands;
    }
}
