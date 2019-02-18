<?php

namespace Mpbarlow\LaravelVueComponentHelper\Actions;


/**
 * Encapsulates a renderable inline component inject.
 * (i.e. <my-component :props="{ props }"></my-component>)
 *
 * @package Mpbarlow\LaravelVueComponentHelper\Actions
 */
class ComponentInjector extends ComponentAction
{
    /**
     * Render the component in a manner compatible with the Vue runtime compiler.
     *
     * @inheritdoc
     */
    public function render(): string
    {
        $tagName = $this->component->getTagName();

        $output = "<{$tagName}";

        // Skip the v-bind entirely if we have no props to bind.
        if ($this->component->getProps() !== []) {
            $output .= " v-bind=\"{$this->component->getPropsJson(true)}\"";
        }

        $output .= "></{$tagName}>";

        return $output;
    }
}