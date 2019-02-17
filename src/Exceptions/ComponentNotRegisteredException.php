<?php


namespace Mpbarlow\LaravelVueComponentHelper\Exceptions;


class ComponentNotRegisteredException extends \Exception
{
    public function __construct(string $component)
    {
        parent::__construct("The component \"{$component}\" has not been registered.");
    }
}