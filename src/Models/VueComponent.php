<?php


namespace Mpbarlow\LaravelVueComponentHelper\Models;


use Mpbarlow\LaravelVueComponentHelper\Actions\ComponentInjector;
use Mpbarlow\LaravelVueComponentHelper\Actions\ComponentMounter;

class VueComponent
{
    /** @var string The name of the component. This will be kebab-cased automatically. */
    protected $name;

    /** @var array The component's prop set. */
    protected $props;

    /**
     * @param string $name
     * @param array  $props
     */
    public function __construct(string $name, array $props = [])
    {
        $this->name = $name;
        $this->props = $props;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the component name in kebab-case, suitable for use as an inline custom tag.
     *
     * @return string
     */
    public function getTagName(): string
    {
        return \kebab_case($this->name);
    }

    /**
     * @return array|null
     */
    public function getProps()
    {
        return $this->props;
    }

    /**
     * @param bool $escape To include JSON inline in templates, we need to HTML encode it.
     * @return string
     */
    public function getPropsJson(bool $escape = false): string
    {
        $json = \json_encode($this->props);

        return $escape ? \e($json) : $json;
    }

    /**
     * @return ComponentInjector
     */
    public function inject(): ComponentInjector
    {
        return new ComponentInjector($this);
    }

    /**
     * @param string|null $to
     * @param string|null $var
     * @return ComponentMounter
     */
    public function mount($to = null, $var = null): ComponentMounter
    {
        return new ComponentMounter($this, $to, $var);
    }
}