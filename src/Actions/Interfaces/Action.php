<?php


namespace Mpbarlow\LaravelVueComponentHelper\Actions\Interfaces;


interface Action
{
    public function render(): string;

    public function __toString(): string;
}