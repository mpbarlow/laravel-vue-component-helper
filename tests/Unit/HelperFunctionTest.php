<?php

namespace Tests;

use Illuminate\Support\Facades\View;
use Mpbarlow\LaravelVueComponentHelper\VueComponentManager;

class HelperFunctionTest extends TestCase
{
    /** @test */
    public function it_returns_a_manager_instance_when_called_with_no_arguments()
    {
        $this->assertInstanceOf(VueComponentManager::class, \vue());
    }

    /** @test */
    public function it_uses_the_specified_template_name_and_data_when_they_are_passed()
    {
        $template = 'template';
        $data = ['key' => 'value'];

        View::shouldReceive('make')
            ->once()
            ->with($template, $data, \Mockery::any());

        \vue('SomeComponent', [], $template, $data);
    }

    /** @test */
    public function it_uses_the_default_template_when_no_template_name_is_passed()
    {
        View::shouldReceive('make')
            ->once()
            ->with(\config('vue_helper.default_template'), \Mockery::any(), \Mockery::any());

        \vue('SomeComponent', []);
    }

    /** @test */
    public function it_always_renders_a_view_when_called_with_arguments()
    {
        View::shouldReceive('make')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any());

        \vue('SomeComponent');

        View::shouldReceive('make')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any());

        \vue('SomeComponent', []);

        View::shouldReceive('make')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any());

        \vue('SomeComponent', [], 'some-template');

        View::shouldReceive('make')
            ->once()
            ->with(\Mockery::any(), \Mockery::any(), \Mockery::any());

        \vue('SomeComponent', [], 'some-template', []);
    }
}