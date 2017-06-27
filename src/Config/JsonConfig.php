<?php

namespace PatrickRose\Invoices\Config;

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

        if (!flock($this->stream, LOCK_EX))
        {
            throw new \RuntimeException("Unable to lock $filename");
        }

        register_shutdown_function(
            function() {$this->shutdown();}
        );

        $this->config = json_decode(stream_get_contents($this->stream), true);

        if ($this->config == null)
        {
            $this->config = [];
        }
    }

    public function has($key)
    {
        return array_key_exists($key, $this->config);
    }

    public function get($key)
    {
        return $this->config[$key];
    }

    public function set($key, $value)
    {
        $this->config[$key] = $value;
    }

    public function getDefault($key, $default)
    {
        return $this->has($key) ? $this->get($key) : $default;
    }

    private function shutdown()
    {
        fseek($this->stream, 0);
        fwrite($this->stream, json_encode($this->config));

        flock($this->stream, LOCK_UN);
        fclose($this->stream);
    }

}
