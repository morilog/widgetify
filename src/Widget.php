<?php
namespace Morilog\Widgetify;

abstract class Widget
{
    protected $configs = [];

    /**
     * @return string
     */
    abstract public function handle();

    public function withConfig(array $configs = [])
    {
        $this->configs = array_merge($this->configs, $configs);

        return $this;
    }

    protected function getConfig($key, $default = null)
    {
        return array_key_exists($key, $this->configs) ? $this->configs[$key] : $default;
    }
}