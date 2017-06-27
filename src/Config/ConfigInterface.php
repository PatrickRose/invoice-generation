<?php

namespace PatrickRose\Invoices\Config;

interface ConfigInterface
{

    public function has($key);

    public function get($key);

    public function set($key, $value);

    public function getDefault($key, $default);

}
