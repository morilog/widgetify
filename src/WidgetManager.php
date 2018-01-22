<?php

namespace Morilog\Widgetify;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\Container;

class WidgetManager
{
    const WIDGET_CONTAINER_PREFIX = 'morilog_widgets.widget.';
    const CACHE_PREFIX = 'morilog_widgetify_';

    protected $container;

    protected static $widgets = [];

    /**
     * @var Repository
     */
    private $cache;

    public function __construct(Container $container, Repository $cache)
    {
        $this->container = $container;
        $this->cache = $cache;
    }

    public function render($widget)
    {
        $args = func_get_args();
        $configs = count($args) > 1 && is_array($args[1]) ? $args[1] : [];

        return $this->buildWidget($widget)->withConfig($configs)->handle();
    }

    public function remember($widget, $minutes)
    {
        $args = func_get_args();
        $configs = count($args) > 2 && is_array($args[2]) ? $args[2] : [];

        $cacheKey = self::CACHE_PREFIX . sha1($widget . json_encode($configs));

        if (($result = $this->cache->get($cacheKey)) === false) {
            $result = $this->buildWidget($widget)->withConfig($configs)->handle();
            $this->cache->put($cacheKey, (string)$result, $minutes);
        }

        return $result;
    }

    /**
     * @param $widget
     * @return Widget
     */
    protected function buildWidget($widget)
    {
        if ($widget instanceof Widget) {
            return $widget;
        }

        if (is_string($widget) === false) {
            throw new \InvalidArgumentException(sprintf('Widget name must be string'));
        }

        $widget = class_exists($widget) ? $widget : $this->generateContainerTag($widget);

        if (isset(static::$widgets[$widget]) === false) {
            throw new \RuntimeException(sprintf('Widget %s is not exists', $widget));
        }

        return $this->container->make(static::$widgets[$widget]);
    }

    /**
     * @param $abstract
     * @param null $instance
     * @return $this
     */
    public function registerWidget($abstract, $instance = null)
    {
        if (is_string($abstract) === false) {
            throw new \InvalidArgumentException(sprintf('Widget name must be string'));
        }

        if (class_exists($abstract)) {
            $instance = $instance !== null ? $instance : $abstract;

            static::$widgets[$abstract] = $instance;

            return $this;
        }

        if ($instance === null || class_exists($instance) === false) {
            throw new \InvalidArgumentException('%s Could not resolved');
        }

        static::$widgets[$this->generateContainerTag($abstract)] = $instance;

        return $this;
    }


    protected function generateContainerTag($widget)
    {
        return static::WIDGET_CONTAINER_PREFIX . snake_case($widget);
    }

}