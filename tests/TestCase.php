<?php

namespace Tests;

use Mpbarlow\LaravelVueComponentHelper\Facade;
use Mpbarlow\LaravelVueComponentHelper\ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return ['Vue' => Facade::class];
    }
}