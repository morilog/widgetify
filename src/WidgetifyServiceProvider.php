<?php
namespace Morilog\Widgetify;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class WidgetifyServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/configs/widgetify.php' => config_path('widgetify.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('morilog.widgetify', function ($app) {
            return new WidgetManager($app);
        });

        $this->registerWidgets($this->app['config']->get('widgetify.widgets', []));

        $this->registerBladeDirectives();
    }

    protected function registerWidgets(array $widgets = [])
    {
        foreach ($widgets as $key => $value) {
            $name = is_numeric($key) ? $value : $key;

            $this->app['morilog.widgetify']->registerWidget($name, $value);
        }
    }

    protected function registerBladeDirectives()
    {
        $hasParaenthesis = version_compare($this->app->version(), '5.3', '<');

        Blade::directive('widgetify', function ($expression) use ($hasParaenthesis) {
            $expression = $hasParaenthesis ? $expression : "($expression)";

            return "<?php echo app('morilog.widgetify')->render{$expression}; ?>";
        });

        Blade::directive('cached_widgetify', function ($expression) use ($hasParaenthesis) {
            $expression = $hasParaenthesis ? $expression : "($expression)";

            return "<?php echo app('morilog.widgetify')->remember{$expression}; ?>";
        });
    }

    public function provides()
    {
        return ['morilog.widgetify'];
    }

}
