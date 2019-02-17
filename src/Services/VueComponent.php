<?php


namespace Mpbarlow\LaravelVueComponentHelper\Services;


class VueComponent
{
    /** @var string */
    protected $name;

    /** @var array|null */
    protected $props;

    /**
     * VueComponent constructor.
     *
     * @param string $name
     * @param array  $props
     */
    public function __construct(string $name, array $props = [])
    {
        $this->name = $name;
        $this->props = $props === [] ? null : $props;
    }

    /**
     * Render the component in a manner compatible with the Vue runtime compiler.
     *
     * @return string
     */
    public function render()
    {
        $tagName = \kebab_case($this->name);

        $output = "<{$tagName}";

        if ($this->props !== null) {
            $output .= " v-bind=\"{$this->escape(\json_encode($this->props))}\"";
        }

        $output .= "></{$tagName}>";

        return $output;
    }

    /**
     * Escape any double quotes present in the props JSON.
     *
     * @param string $props
     * @return mixed
     */
    protected function escape(string $props)
    {
        return \str_replace('"', '&quot;', $props);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}