<?php
namespace Morilog\Widgetify\Facades;

use Illuminate\Support\Facades\Facade;

class Widgetify extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'morilog.widgetify';
    }
}