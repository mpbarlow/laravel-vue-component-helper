<?php


namespace Tests;


use Mpbarlow\LaravelVueComponentHelper\Actions\ComponentInjector;
use Mpbarlow\LaravelVueComponentHelper\Actions\ComponentMounter;
use Mpbarlow\LaravelVueComponentHelper\Models\VueComponent;

class VueComponentTest extends TestCase
{
    const COMPONENT = 'AComponent';

    const PROPS = [
        'number'      => 1,
        'array'       => ['a' => 'b'],
        'quoted_text' => 'This has "quotes"',
        'null'        => null
    ];

    /** @var VueComponent */
    protected $component;

    protected function setUp()
    {
        parent::setUp();

        $this->component = new VueComponent(self::COMPONENT, self::PROPS);
    }

    /** @test */
    public function it_can_provide_its_tag_name()
    {
        $this->assertEquals('a-component', $this->component->getTagName());
    }

    /** @test */
    public function it_can_be_injected()
    {
        $injection = $this->component->inject();

        $this->assertInstanceOf(ComponentInjector::class, $injection);

        $expectedProps =
            '{&quot;number&quot;:1,&quot;array&quot;:{&quot;a&quot;:&quot;b&quot;},&quot;quoted_text&quot;:&quot;This has \&quot;quotes\&quot;&quot;,&quot;null&quot;:null}';

        $expectedInjectionRender = "<a-component v-bind=\"{$expectedProps}\"></a-component>";

        $this->assertEquals($expectedInjectionRender, $injection->render());

        $this->assertEquals((string)$injection, $injection->render());
    }

    /** @test */
    public function it_can_be_mounted()
    {
        $mount = $this->component->mount();

        $this->assertInstanceOf(ComponentMounter::class, $mount);

        $expectedComponentName = self::COMPONENT;
        $expectedProps = \json_encode(self::PROPS);

        $expectedMountRender = <<<JSC
<script>
new Vue({ render: function (h) { return h('{$expectedComponentName}', { props: {$expectedProps} }) } }).\$mount('#app')
</script>

JSC;
        $this->assertEquals($expectedMountRender, $mount->render());

        $this->assertEquals((string)$mount, $mount->render());
    }

    /** @test */
    public function it_can_bind_vm_to_a_global_variable()
    {
        $mount = $this->component->mount(null, 'vm');

        $this->assertContains('var vm = new Vue', $mount->render());
    }

    /** @test */
    public function it_can_mount_to_different_selectors()
    {
        $mount = $this->component->mount('#main');

        $this->assertContains("\$mount('#main')", $mount->render());
    }

    /** @test */
    public function it_can_use_different_vue_constructors()
    {
        \config()->set('vue_helper.vue_global', 'window.Vue');

        $mount = $this->component->mount();

        $this->assertContains('new window.Vue', $mount->render());
    }

    /** @test */
    public function it_can_mix_in_additional_parameters()
    {
        \config()->set('vue_helper.additional_config', ['router' => 'router', 'store' => 'vuex']);

        $render = $this->component->mount()->render();

        $this->assertContains('router: router', $render);

        $this->assertContains('store: vuex', $render);
    }
}