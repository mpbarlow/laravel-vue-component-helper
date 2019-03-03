<?php


namespace Tests;

use Mpbarlow\LaravelVueComponentHelper\Facade as Vue;
use Mpbarlow\LaravelVueComponentHelper\VueComponentManager;

class FacadeTest extends TestCase
{
    /** @test */
    public function it_can_resolve_the_singleton_via_the_facade()
    {
        $facade = Vue::register('Component');
        $rootNamespace = \Vue::register('AnotherComponent');

        $this->assertInstanceOf(VueComponentManager::class, $facade);

        $this->assertInstanceOf(VueComponentManager::class, $rootNamespace);

        $this->assertSame($facade, $rootNamespace);
    }
}