<?php

namespace Mpbarlow\LaravelVueComponentHelper;


use Mpbarlow\LaravelVueComponentHelper\Actions\DependencyRenderer;
use Mpbarlow\LaravelVueComponentHelper\Exceptions\ComponentNotRegisteredException;
use Mpbarlow\LaravelVueComponentHelper\Models\VueComponent;

class VueComponentManager
{
    /** @var string The component to render when no name is supplied. */
    protected $defaultComponent = null;

    /** @var array The list of available components and their props. */
    protected $registeredComponents = [];

    /** @var string|null The Blade template to render. */
    protected $template = null;

    /** @var array Any data to passthrough into the ViewFactory. */
    protected $templateData = [];

    /** @var array The JavaScripts that need to be loaded to render components. */
    protected $dependencies = [];

    /**
     * Register a Vue component, its props, and any dependencies with the manager.
     *
     * @param string            $componentName The Vue component name.
     * @param array             $props         The Vue component's props.
     * @param string|array|null $from          Any JavaScript dependencies for the component.
     * @return VueComponentManager
     */
    public function register(string $componentName, array $props = [], $from = []): self
    {
        $this->registeredComponents[$componentName] = new VueComponent($componentName, $props);

        foreach ((array)$from as $dependency) {
            $this->dependencies[] = $dependency;
        }

        return $this;
    }

    /**
     * Register a component as the default.
     *
     * @param string $componentName
     * @param array  $props
     * @param array  $from
     * @return VueComponentManager
     */
    public function registerDefault(string $componentName, array $props = [], $from = []): self
    {
        $this->register($componentName, $props, $from);
        $this->defaultComponent = $componentName;

        return $this;
    }

    /**
     * Set up the Blade template to be rendered.
     *
     * @param string|null $template     The path to the Blade template to be rendered.
     * @param array       $templateData Data to pass to the ViewFactory.
     * @return $this
     */
    public function prepareTemplate($template = null, array $templateData = []): self
    {
        $this->template = $template;
        $this->templateData = $templateData;

        return $this;
    }

    /**
     * Register the Vue component with the manager and render the Blade template that contains the
     * directives to mount or inject the components.
     *
     * @param string $componentName
     * @param array  $props
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render(string $componentName, array $props = [])
    {
        if ($this->template === null) {
            $this->template = \config('vue_helper.default_template');
        }

        $this->registerDefault($componentName, $props);

        return \view($this->template, $this->templateData);
    }

    /**
     * Fetch the specified component.
     *
     * @param string $componentName
     * @return VueComponent
     * @throws ComponentNotRegisteredException
     */
    public function component(string $componentName = ''): VueComponent
    {
        // Select the default component if no name is provided.
        if ($componentName === '') {
            if ($this->defaultComponent === null) {
                throw new ComponentNotRegisteredException('default');
            }

            $componentName = $this->defaultComponent;
        }

        if (! \array_key_exists($componentName, $this->registeredComponents)) {
            throw new ComponentNotRegisteredException($componentName);
        }

        return $this->registeredComponents[$componentName];
    }

    public function dependencies(): DependencyRenderer
    {
        return new DependencyRenderer($this->dependencies);
    }
}