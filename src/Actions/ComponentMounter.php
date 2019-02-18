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

    public function __construct(VueComponent $component, string $to = '', string $var = '')
    {
        if ($to === '') {
            $to = \config('vue_helper.default_mount_el');
        }

        if ($var === '') {
            $var = \config('vue_helper.default_variable');
        }

        $this->to = $to;
        $this->var = $var;
        $this->vue = \config('vue_helper.vue_global');

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

        $output .= <<<SCRIPT
new {$this->vue}({
  render: function(h) {
    return h('{$this->component->getName()}'{$propString})
  }
}).\$mount('{$this->to}')\n
SCRIPT;

        $output .= "</script>\n";

        return $output;
    }
}