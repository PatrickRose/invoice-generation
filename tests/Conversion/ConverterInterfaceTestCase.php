<?php

namespace PatrickRose\Invoices\Conversion;

use PHPUnit\Framework\TestCase;

abstract class ConverterInterfaceTestCase extends TestCase
{
    /**
     * @var ConverterInterface
     */
    protected $converter;

    protected function setUp()
    {
        parent::setUp();

        $this->converter = $this->getClassUnderTest();

        if (!$this->converter::isAvailable())
        {
            $this->markTestSkipped(get_class($this->converter) . ' is not available');
        }
    }

    protected function tearDown()
    {
        $this->converter = null;

        parent::tearDown();
    }

    abstract protected function getClassUnderTest(): ConverterInterface;

}
