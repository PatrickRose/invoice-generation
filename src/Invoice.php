<?php

namespace PatrickRose\Invoices;

use Twig\TemplateWrapper;

class Invoice
{

    private $reference;
    private $payee;
    private $date;
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

    public function generateTexFile($directory)
    {
        $template = $this->getTwigTemplate();

        file_put_contents(
            "$directory/{$this->reference}.tex",
            $template->render([
                'reference' => $this->reference,
                'payee' => $this->payee,
                'date' => $this->date,
                'fees' => $this->fees,
                'expenses' => $this->expenses,
            ])
        );
    }

    private function getTwigTemplate(): \Twig_TemplateWrapper
    {
        if ($this->template === null) {
            $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../templates');
            $template = new \Twig_Environment($loader);
            $this->template = $template->load('invoice.tex');
        }

        return $this->template;
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

}
