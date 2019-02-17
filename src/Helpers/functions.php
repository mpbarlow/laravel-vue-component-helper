<?php

use \Mpbarlow\LaravelVueComponentHelper\Services\VueComponentManager;

if (! \function_exists('vue')) {
    /**
     * Obtain the VueComponentManager singleton or render the component with the provided data.
     *
     * @param string|null $component    The name of the component to render.
     * @param array       $props        The data to convert to props.
     * @param string|null $template     The template to render the component into.
     * @param array       $templateData The data to pass through to the blade template.
     * @return VueComponentManager|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function vue(
        string $component = null,
        array $props = [],
        string $template = null,
        array $templateData = []
    ) {
        /** @var VueComponentManager $manager */
        $manager = \app('VueComponentManager');

        // If we call the function with no arguments, return the manager single to chain further
        // calls onto.
        if (\func_num_args() === 0) {
            return $manager;
        }

        if ($template !== null) {
            $manager->prepareTemplate($template, $templateData);
        }

        // Otherwise render the component with provided data.
        return $manager->render($component, $props);
    }
}