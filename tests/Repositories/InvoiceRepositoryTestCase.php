<?php

namespace PatrickRose\Invoices\Repositories;

use PatrickRose\Invoices\Invoice;
use PHPUnit\Framework\TestCase;

abstract class InvoiceRepositoryTestCase extends TestCase
{

    public function testCanGetAllInvoices()
    {
        $invoices = [
            new Invoice('1234', 'Test payee', 'Some date', ['fee' => 123], ['expense' => 123]),
            new Invoice('4321', 'Test payee', 'Some date', ['fee' => 123], ['expense' => 123]),
        ];

        $repository = $this->getRepositoryUnderTest($invoices);

        $this->assertEquals($invoices, $repository->getAll());
    }

    public function testCanAddANewInvoice()
    {
        $invoice = new Invoice('1234', 'Test payee', 'Some date', ['fee' => 123], ['expense' => 123]);

        $repository = $this->getRepositoryUnderTest();

        $this->assertTrue($repository->add($invoice));
        $this->assertEquals([$invoice], $repository->getAll());
    }

    /**
     * @param Invoice[] $invoices
     * @return InvoiceRepositoryInterface
     */
    abstract protected function getRepositoryUnderTest(array $invoices = []): InvoiceRepositoryInterface;

    public function testInstantiate()
    {
        $class = get_class($this->getRepositoryUnderTest([]));

        $returnedClass = $class::instantiate($this->getInstantiateConfiguration());

        static::assertInstanceOf($class, $returnedClass, 'Did not get the correct class');
    }

    /**
     * Get the instantiate configuration
     *
     * @see self::testInstantiate
     */
    abstract protected function getInstantiateConfiguration(): array;

}
