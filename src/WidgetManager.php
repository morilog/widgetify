<?php
namespace Morilog\Widgetify;

use Illuminate\Contracts\Container\Container;

class WidgetManager
{
    const WIDGET_CONTAINER_PREFIX = 'morilog_widgets.widget.';

    protected $container;

    protected static $widgets = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function render($widget)
    {
        $args = func_get_args();
        $configs = count($args) > 1 && is_array($args[1] ) ? $args[1] : [];

        return $this->buildWidget($widget)->withConfig($configs)->handle();
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