<?php

namespace PatrickRose\Invoices\Commands;

use PatrickRose\Invoices\Config\ConfigInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class AddInvoiceCommand extends Command
{
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        parent::__construct('add');
        $this->config = $config;
    }

    protected function configure()
    {
        $this->setDescription('Add new invoice');
    }

    public function run(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $reference = $questionHelper->ask($input, $output, new Question('<question>Reference for invoice</question>: '));
        $payee = $questionHelper->ask($input, $output, new Question('<question>Payee</question>: '));
        $date = $questionHelper->ask($input, $output, new Question('<question>Date</question>: '));

        $fees = [];
        do
        {
            $feeDescription = $questionHelper->ask($input, $output, new Question('<question>Fee description</question>: '));
            $feeAmount = $questionHelper->ask($input, $output, new Question('<question>Amount</question>: '));
            $fees[$feeDescription] = $feeAmount;
        } while (!$questionHelper->ask($input, $output, new ConfirmationQuestion('<question>Done?</question>: ')));

        $expenses = [];
        while ($questionHelper->ask($input, $output, new ConfirmationQuestion('<question>Add expense?</question>: ', false)))
        {
            $expenseDescription = $questionHelper->ask($input, $output, new Question('<question>Expense description</question>: '));
            $expenseAmount = $questionHelper->ask($input, $output, new Question('<question>Amount</question>: '));
            $expenses[$expenseDescription] = $expenseAmount;
        }

        $invoices = $this->config->getDefault('invoices', []);

        $invoices[$reference] = compact('payee', 'date', 'fees', 'expenses');

        $this->config->set('invoices', $invoices);
    }


}
