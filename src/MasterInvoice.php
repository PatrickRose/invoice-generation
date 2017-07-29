<?php


namespace PatrickRose\Invoices;


class MasterInvoice
{

    private $fees;
    private $expenses;
    private $template;

    public function addInvoice(Invoice $invoice)
    {
        $this->fees[$invoice->getReference()] = $invoice->getTotalFees();
        $this->expenses[$invoice->getReference()] = $invoice->getExpenses();
    }

    /**
     * @return array
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * @return array
     */
    public function getExpenses()
    {
        return $this->expenses;
    }

}
