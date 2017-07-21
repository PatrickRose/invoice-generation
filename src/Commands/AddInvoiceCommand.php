<?php

namespace PatrickRose\Invoices\Commands;

use PatrickRose\Invoices\Exceptions\RuntimeException;
use PatrickRose\Invoices\Invoice;
use PatrickRose\Invoices\Repositories\InvoiceRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class AddInvoiceCommand extends Command
{
    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        parent::__construct('add');
        $this->invoiceRepository = $invoiceRepository;
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

        if (!$this->invoiceRepository->add(new Invoice($reference, $payee, $date, $fees, $expenses)))
        {
            throw new RuntimeException("Unable to add invoice repository");
        }
    }


}
