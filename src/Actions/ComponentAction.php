<?php

namespace Mpbarlow\LaravelVueComponentHelper\Actions;


use Mpbarlow\LaravelVueComponentHelper\Actions\Interfaces\Action;
use Mpbarlow\LaravelVueComponentHelper\Actions\Traits\Renderable;
use Mpbarlow\LaravelVueComponentHelper\Models\VueComponent;

abstract class ComponentAction implements Action
{
    use Renderable;

    /** @var VueComponent */
    protected $component;

    public function __construct(VueComponent $component)
    {
        $this->component = $component;
    }
}