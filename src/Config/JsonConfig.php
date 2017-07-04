<?php

namespace PatrickRose\Invoices\Config;

use PatrickRose\Invoices\Exceptions\LockException;
use PatrickRose\Invoices\Exceptions\UnknownConfigKeyException;

class JsonConfig implements ConfigInterface
{
    private $config;
    private $stream;

    public function __construct($filename)
    {
        if (!file_exists($filename))
        {
            touch($filename);
        }

        $this->stream = fopen($filename, 'r+');

        if (!flock($this->stream, LOCK_EX | LOCK_NB))
        {
            throw new LockException("Unable to lock $filename");
        }

        $this->config = json_decode(stream_get_contents($this->stream), true);

        if ($this->config == null)
        {
            $this->config = [];
        }
    }

    public function has(string $key) : bool
    {
        return array_key_exists($key, $this->config);
    }

    public function get(string $key)
    {
        if (!array_key_exists($key, $this->config))
        {
            throw new UnknownConfigKeyException("Unknown key $key");
        }

        return $this->config[$key];
    }

    public function set(string $key, $value) : void
    {
        $this->config[$key] = $value;
    }

    public function getDefault(string $key, $default)
    {
        return $this->has($key) ? $this->get($key) : $default;
    }

    public function __destruct()
    {
        fseek($this->stream, 0);
        fwrite($this->stream, json_encode($this->config));

        flock($this->stream, LOCK_UN);
        fclose($this->stream);
    }

}
