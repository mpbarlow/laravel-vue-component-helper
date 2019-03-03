<?php


namespace Mpbarlow\LaravelVueComponentHelper\Actions;


use Mpbarlow\LaravelVueComponentHelper\Models\VueComponent;

/**
 * Encapsulates mounting to an element via the Vue constructor.
 *
 * @package Mpbarlow\LaravelVueComponentHelper\Actions
 */
class ComponentMounter extends ComponentAction
{
    /** @var string The selector to mount the Vue application to. */
    protected $to;

    /** @var string The JS variable to assign the root Vue instance to. */
    protected $var;

    /** @var string The name of the global Vue object. */
    protected $vue;

    /** @var array Additional configuration to pass to the Vue constructor. */
    protected $additional;

    /**
     * @param VueComponent $component
     * @param null         $to
     * @param null         $var
     */
    public function __construct(VueComponent $component, $to = null, $var = null)
    {
        if ($to === null) {
            $to = \config('vue_helper.default_mount_el');
        }

        if ($var === null) {
            $var = \config('vue_helper.default_variable');
        }

        $this->to = $to;
        $this->var = $var;
        $this->vue = \config('vue_helper.vue_global');
        $this->additional = \config('vue_helper.additional_config');

        parent::__construct($component);
    }

    public function render(): string
    {
        $output = "<script>\n";

        // Bind the root Vue instance to a global variable if required
        if ($this->var !== null) {
            $output .= "var {$this->var} = ";
        }

        $propString = '';

        if ($this->component->getProps() !== []) {
            $propString = ", { props: {$this->component->getPropsJson()} }";
        }

        $output .= "new {$this->vue}({ ";
        $output .= $this->getAdditionalEntryPairs();
        $output .= "render: function (h) { return h('{$this->component->getName()}'{$propString}) } ";
        $output .= "}).\$mount('{$this->to}')\n";
        $output .= "</script>\n";

        return $output;
    }

    /**
     * Render additional configuration entries as key-value pairs to be merged into the
     * constructor object.
     *
     * @return string
     */
    protected function getAdditionalEntryPairs(): string
    {
        $output = '';

        foreach ($this->additional as $key => $value) {
            $output .= "{$key}: {$value}, ";
        }

        return $output;
    }
}