<?php

use \Mpbarlow\LaravelVueComponentHelper\VueComponentManager;

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
        $template = null,
        array $templateData = []
    ) {
        /** @var VueComponentManager $manager */
        $manager = \app(VueComponentManager::class);

        // If we call the function with no arguments, return the manager singleton to chain further
        // calls onto.
        if (\func_num_args() === 0) {
            return $manager;
        }

        return $manager
            ->prepareTemplate($template, $templateData)
            ->render($component, $props);
    }
}