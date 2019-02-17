<?php


namespace Mpbarlow\LaravelVueComponentHelper;


class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'VueComponentManager';
    }
}