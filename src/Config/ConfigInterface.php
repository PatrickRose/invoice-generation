<?php

namespace PatrickRose\Invoices\Config;

use PatrickRose\Invoices\Exceptions\UnknownConfigKeyException;

/**
 * Base interface for configuration
 *
 * @package PatrickRose\Invoices
 */
interface ConfigInterface
{

    /**
     * Whether the given key has been set
     *
     * @param string $key The key to check
     * @return bool True if set, false if not
     */
    public function has(string $key) : bool;

    /**
     * Get the value of the given key
     *
     * @param string $key The key to get the value of
     * @return mixed The value of the key
     * @throws UnknownConfigKeyException If the config value is not set
     */
    public function get(string $key);

    /**
     * Set the value of the given key to the given value
     *
     * @param string $key The key to set
     * @param mixed $value The value to set to
     */
    public function set(string $key, $value) : void;

    /**
     * Get the value of the given key, returning the default if not set
     *
     * @param string $key The key to get the value of
     * @param mixed $default The default value
     * @return mixed The value of the key, or the default if not set
     */
    public function getDefault(string $key, $default);

}
