# Widgetify
Laravel widget package for Laravel >= 5.1

## Installation
```
composer require morilog/widgetify
```
#### Register in Laravel
- Add `Morilog\Widgetify\WidgetifyServiceProvider` to `config/app.php` providers array

- If you need to Facade for rendering widgets, add bellow in `config/app.php` aliases array :
```php
	'Widgetify' => Morilog\Widgetify\Facades\Widgetify::class
```

- For publish Widgetify config file, run this command:
```
php artisan vendor:publish --provider="Morilog\Widgetify\WidgetifyServiceProvider"
```

---
## Usage
#### Create new widget
For creating new widget you must create a class that extended `Morilog\Widgetify\Widget` and implement `handle()` method.
```php
<?php
namespace App\MyWidgets;

use Morilog\Widgetify\Widget;

class SimpleWidget extends Widget
{
	public function handle()
	{
		$latestPosts = Post::take(10)->get();

		return view('path.to.view.file', compact('latestPosts'));
	}
}

```

#### Registering Widget
- Add your widget to `widgets` array in `config/widgetify.php` file:

```php
	'widgets' => [
		'simple_widget' => App\MyWidgets\SimpleWidget::class
	]
```

#### Rendering Widgets
With blade `@widgetify` directive:
```php
// views/sidebar.blade.php
<div class="col-sm-3">
	@widgetify('simple_widget')
</div>
```

OR with configs:
```php
// views/sidebar.blade.php
<div class="col-sm-3">
	@widgetify('simple_widget', ['key' => 'value', 'key2' => 'value'])
</div>
```

OR with `Widgetify` Facade:
```php
// views/sidebar.blade.php
<div class="col-sm-3">
	{!! Widgetify::render('simple_widgets') !!}
</div>
```

#### Using cache
```php
// views/default.blade.php
<div class="col-sm-4">
    {!! Widgetify::remember('my_widget', 15, [CONFIGS]); !!}
</div>
<div class="col-sm-4">
    @cached_widgetify('my_widget', 15, [CONFIGS]);
</div>

```
