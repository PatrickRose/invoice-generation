<?php


namespace PatrickRose\Invoices\Conversion;

use Twig\Environment;
use Twig\TemplateWrapper;

abstract class AbstractTwigConverter implements ConverterInterface
{

    /**
     * @var Environment
     */
    protected $environment;
    /**
     * @var string
     */
    private $invoiceTemplate;
    /**
     * @var string
     */
    private $masterTemplate;

    public function __construct(Environment $environment, string $invoiceTemplate, string $masterTemplate)
    {
        $this->environment = $environment;
        $this->invoiceTemplate = $invoiceTemplate;
        $this->masterTemplate = $masterTemplate;
    }

    protected function getInvoiceTemplate(): TemplateWrapper
    {
        return $this->environment->load($this->invoiceTemplate);
    }

    protected function getMasterTemplate(): TemplateWrapper
    {
        return $this->environment->load($this->masterTemplate);
    }

}
