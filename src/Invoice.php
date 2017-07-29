<?php

namespace PatrickRose\Invoices;

use Twig\TemplateWrapper;

class Invoice
{

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $payee;

    /**
     * @var string
     */
    private $date;

    /**
     * @var array
     */
    private $fees;

    /**
     * @var array
     */
    private $expenses;

    /**
     * @var Twig_TemplateWrapper
     */
    private $template;

    public function __construct(
        string $reference,
        string $payee,
        string $date,
        array $fees,
        array $expenses = []
    )
    {
        $this->reference = $reference;
        $this->payee = $payee;
        $this->date = $date;
        $this->fees = $fees;
        $this->expenses = $expenses;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function getTotalFees()
    {
        return array_sum($this->fees);
    }

    public function getExpenses()
    {
        return $this->expenses;
    }

    /**
     * @return string
     */
    public function getPayee(): string
    {
        return $this->payee;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return array
     */
    public function getFees(): array
    {
        return $this->fees;
    }

    public function toArray(): array
    {
        return [
            'reference' => $this->getReference(),
            'payee' => $this->getPayee(),
            'date' => $this->getDate(),
            'fees' => $this->getFees(),
            'expenses' => $this->getExpenses()
        ];
    }
}
