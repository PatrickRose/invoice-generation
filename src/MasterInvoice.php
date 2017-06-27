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

    public function generateTexFile($directory)
    {
        $template = $this->getTwigTemplate();

        file_put_contents(
            "$directory/master.tex",
            $template->render([
                'fees' => $this->fees,
                'expenses' => $this->expenses
            ])
        );
    }

    private function getTwigTemplate(): \Twig_TemplateWrapper
    {
        if ($this->template === null) {
            $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../templates');
            $template = new \Twig_Environment($loader, array('debug' => true));
            $this->template = $template->load('master.tex');
        }

        return $this->template;
    }

}
