<?php

namespace Mpbarlow\LaravelVueComponentHelper\Services;


use Mpbarlow\LaravelVueComponentHelper\Exceptions\ComponentNotRegisteredException;

class VueComponentManager
{
    /** @var array The component and props to render when no name is supplied. */
    protected $defaultComponent;

    /** @var array The list of available components and their props. */
    protected $registeredComponents = [];

    /** @var array The JavaScripts that need to be loaded to render components. */
    protected $dependencies = [];

    /** @var string|null The Blade template to render. */
    protected $template = null;

    /** @var array Any data to passthrough into the ViewFactory. */
    protected $templateData = [];

    /**
     * Register a Vue component, its props, and any dependencies with the manager.
     *
     * @param string            $component The Vue component name.
     * @param array             $data      The Vue component's props.
     * @param string|array|null $from      Any JavaScript dependencies for the component.
     */
    public function register(string $component, array $data, $from = null)
    {
        $this->registeredComponents[$component] = $data;

        foreach ((array)$from as $dependency) {
            $this->dependencies[] = $dependency;
        }
    }

    /**
     * Set up the Blade template to be rendered.
     *
     * @param string $template     The path to the Blade template to be rendered.
     * @param array  $templateData Data to pass to the ViewFactory.
     * @return $this
     */
    public function prepareTemplate(string $template, array $templateData = [])
    {
        $this->template = $template;
        $this->templateData = $templateData;

        return $this;
    }

    /**
     * Register the Vue component with the manager and render the Blade template that contains the
     * directives to mount or inject the components.
     *
     * @param string $component
     * @param array  $props
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render(string $component, array $props = [])
    {
        if ($this->template === null) {
            $this->template = \config('vue_helper.default_template');
        }

        $this->defaultComponent[$component] = $props;

        return \view($this->template, $this->templateData);
    }

    /**
     * Draw the specified component.
     *
     * @param string $component The name of the Vue component to render.
     * @return string
     * @throws ComponentNotRegisteredException
     */
    public function renderComponent(string $component = '')
    {
        $propsList = $this->registeredComponents;

        if ($component === '') {
            // Load the default component if no name is provided.
            $component = \array_key_last($this->defaultComponent);
            $propsList = $this->defaultComponent;
        }

        if (! \array_key_exists($component, $propsList)) {
            throw new ComponentNotRegisteredException($component);
        }

        return (new VueComponent($component, $propsList[$component]))->render();
    }
}