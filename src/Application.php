<?php

namespace PatrickRose\Invoices;

use PatrickRose\Invoices\Commands\AddInvoiceCommand;
use PatrickRose\Invoices\Commands\GenerateInvoicesCommand;
use PatrickRose\Invoices\Config\ConfigInterface;
use PatrickRose\Invoices\Config\JsonConfig;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{

    const VERSION = '0.0.1';
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config = null)
    {
        if ($config === null)
        {
            $config = new JsonConfig(__DIR__ . '/../config.json');
        }

        $this->config = $config;

        parent::__construct('Invoice generator', self::VERSION);
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new AddInvoiceCommand($this->config);
        $commands[] = new GenerateInvoicesCommand($this->config);

        return $commands;
    }
}
