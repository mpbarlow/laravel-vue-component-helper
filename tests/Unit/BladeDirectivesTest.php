<?php

namespace Tests;

use Illuminate\Support\Facades\Blade;
use Mpbarlow\LaravelVueComponentHelper\VueComponentManager;

class BladeDirectivesTest extends TestCase
{
    const BINDING = VueComponentManager::class;

    /** @test */
    public function it_can_inject_the_default_component_with_vue_component()
    {
        $expectedRender = "<?php echo \app('" . self::BINDING . "')->component()->inject(); ?>";

        $this->assertEquals($expectedRender, Blade::compileString('@vue_component'));
    }

    /** @test */
    public function it_can_inject_named_components_with_vue_component()
    {
        $expectedRender =
            "<?php echo \app('" . self::BINDING . "')->component('AComponent')->inject(); ?>";

        $this->assertEquals(
            $expectedRender,
            Blade::compileString("@vue_component('AComponent')")
        );
    }

    /** @test */
    public function it_can_mount_the_default_component_with_vue_mount()
    {
        $expectedRender =
            "<?php echo \app('" . self::BINDING . "')->component()->mount(null, null); ?>";

        $this->assertEquals($expectedRender, Blade::compileString('@vue_mount'));
    }

    /** @test */
    public function it_can_mount_named_components_with_vue_mount()
    {
        $expectedRender =
            "<?php echo \app('" . self::BINDING . "')->component('AComponent')->mount(null, null); ?>";

        $this->assertEquals(
            $expectedRender,
            Blade::compileString("@vue_mount('AComponent')")
        );
    }

    /** @test */
    public function it_can_specify_to_without_var_on_vue_mount()
    {
        $expectedRender =
            "<?php echo \app('" . self::BINDING . "')->component('AComponent')->mount('#app', null); ?>";

        $this->assertEquals(
            $expectedRender,
            Blade::compileString("@vue_mount('AComponent', '#app')")
        );
    }

    /** @test */
    public function it_can_specify_var_without_to_on_vue_mount()
    {
        $expectedRender =
            "<?php echo \app('" . self::BINDING . "')->component('AComponent')->mount(null, 'vm'); ?>";

        $this->assertEquals(
            $expectedRender,
            Blade::compileString("@vue_mount('AComponent', null, 'vm')")
        );
    }

    /** @test */
    public function it_can_specify_both_to_and_var_on_vue_mount()
    {
        $expectedRender =
            "<?php echo \app('" . self::BINDING . "')->component('AComponent')->mount('#app', 'vm'); ?>";

        $this->assertEquals(
            $expectedRender,
            Blade::compileString("@vue_mount('AComponent', '#app', 'vm')")
        );
    }

    /** @test */
    public function it_can_handle_variable_argument_spacing_on_vue_mount()
    {
        $expectedRender =
            "<?php echo \app('" . self::BINDING . "')->component('AComponent')->mount('#app', 'vm'); ?>";

        $this->assertEquals(
            $expectedRender,
            Blade::compileString("@vue_mount('AComponent','#app','vm')")
        );

        $this->assertEquals(
            $expectedRender,
            Blade::compileString("@vue_mount( 'AComponent', '#app', 'vm' )")
        );
    }

    /** @test */
    public function it_can_render_dependencies_with_vue_dependencies()
    {
        $expectedRender = "<?php echo \app('" . self::BINDING . "')->dependencies(); ?>";

        $this->assertEquals($expectedRender, Blade::compileString('@vue_dependencies'));
    }
}