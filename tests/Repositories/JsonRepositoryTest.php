<?php

namespace PatrickRose\Invoices\Repositories;

use PatrickRose\Invoices\Exceptions\LockException;
use PatrickRose\Invoices\Invoice;

class JsonRepositoryTest extends InvoiceRepositoryTestCase
{
    protected $testFiles = [];

    protected function tearDown()
    {
        parent::tearDown();

        foreach ($this->testFiles as $file) {
            unlink($file);
        }

        $this->testFiles = [];
    }

    /**
     * @param Invoice[] $invoices
     * @return InvoiceRepositoryInterface
     */
    protected function getRepositoryUnderTest(array $invoices = []): InvoiceRepositoryInterface
    {
        $file = tempnam(sys_get_temp_dir(), $this->getName());

        $this->testFiles[] = $file;

        $toWrite = array_map(
            function (Invoice $invoice) {
                return $invoice->toArray();
            },
            $invoices
        );

        file_put_contents($file, json_encode($toWrite));
        return new JsonRepository($file);
    }

    public function testTheFileIsLocked()
    {
        $testFile = tempnam(sys_get_temp_dir(), $this->getName());
        $this->testFiles[] = $testFile;
        file_put_contents($testFile, json_encode([]));

        $repo = new JsonRepository($testFile);
        $thrown = false;

        try {
            new JsonRepository($testFile);
        } catch (LockException $ex) {
            $thrown = true;
        }

        $this->assertTrue($thrown, 'Did not throw a exception');

        unset($repo);

        new JsonRepository($testFile);
    }

    public function testItWritesWhenDestructed()
    {
        $testFile = tempnam(sys_get_temp_dir(), $this->getName());
        $this->testFiles[] = $testFile;
        file_put_contents($testFile, json_encode([]));

        $repo = new JsonRepository($testFile);

        $invoice = new Invoice('test', 'test', 'test', [], []);
        $repo->add($invoice);

        $this->assertEquals(json_encode([]), file_get_contents($testFile));

        unset($repo);

        $this->assertEquals(json_encode([$invoice->toArray()]), file_get_contents($testFile));
    }

    /**
     * Get the instantiate configuration
     *
     * @see self::testInstantiate
     */
    protected function getInstantiateConfiguration(): array
    {
        $testFile = tempnam(sys_get_temp_dir(), $this->getName());
        $this->testFiles[] = $testFile;

        return ['filename' => $testFile];
    }
}
