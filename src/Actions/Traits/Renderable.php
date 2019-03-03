<?php


namespace Mpbarlow\LaravelVueComponentHelper\Actions\Traits;


trait Renderable
{
    abstract function render(): string;

    public function __toString(): string
    {
        return $this->render();
    }
}