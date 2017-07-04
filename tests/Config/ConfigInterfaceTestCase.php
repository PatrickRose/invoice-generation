<?php

namespace PatrickRose\Invoices\Config;

use PatrickRose\Invoices\Exceptions\UnknownConfigKeyException;
use PHPUnit\Framework\TestCase;

abstract class ConfigInterfaceTestCase extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
    }

    public function testGet()
    {
        $config = $this->getConfigUnderTest([
            'existing-key' => 'some-value'
        ]);

        $this->assertEquals('some-value', $config->get('existing-key'));
    }

    /**
     * @expectedException \PatrickRose\Invoices\Exceptions\UnknownConfigKeyException
     */
    public function testGetNonExistingKey()
    {
        $config = $this->getConfigUnderTest([]);

        $config->get('12345');
    }

    public function testGetDefault()
    {
        $config = $this->getConfigUnderTest([
            'existing-key' => 'some-value',
        ]);

        $this->assertEquals('non-existing-key', $config->getDefault('non-existing-key', 'non-existing-key'));
        $this->assertEquals('some-value', $config->getDefault('existing-key', 'different'));
    }

    public function testHas()
    {
        $config = $this->getConfigUnderTest([
            'existing-key' => 'some-value',
        ]);

        $this->assertTrue($config->has('existing-key'));
        $this->assertFalse($config->has('non-existing-key'));
    }

    public function testSet()
    {
        $config = $this->getConfigUnderTest();

        $this->assertFalse($config->has('key-to-set'));
        $config->set('key-to-set', 'value');
        $this->assertTrue($config->has('key-to-set'));
        $this->assertEquals('value', $config->get('key-to-set'));
    }

    /**
     * Get the config implementation under test
     *
     * @param array $existingConfig The existing configuration
     * @return ConfigInterface
     */
    abstract protected function getConfigUnderTest(array $existingConfig = []): ConfigInterface;

}
