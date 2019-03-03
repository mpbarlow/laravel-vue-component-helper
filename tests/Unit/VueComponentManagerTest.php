<?php


namespace Tests;

use Illuminate\Support\Facades\View;
use Mpbarlow\LaravelVueComponentHelper\Exceptions\ComponentNotRegisteredException;
use Mpbarlow\LaravelVueComponentHelper\Models\VueComponent;
use Mpbarlow\LaravelVueComponentHelper\VueComponentManager;

class VueComponentManagerTest extends TestCase
{
    const COMPONENT = 'AComponent';

    /** @var VueComponentManager */
    protected $manager;

    protected function setUp()
    {
        parent::setUp();

        $this->manager = \app(VueComponentManager::class);
    }

    /**
     * @test
     * @throws \Mpbarlow\LaravelVueComponentHelper\Exceptions\ComponentNotRegisteredException
     */
    public function it_can_register_and_recall_its_registered_components()
    {
        $this->manager->register(self::COMPONENT);

        $component = $this->manager->component(self::COMPONENT);

        $this->assertInstanceOf(VueComponent::class, $component);
    }

    /**
     * @test
     * @throws ComponentNotRegisteredException
     */
    public function it_throws_an_exception_when_an_unregistered_named_template_is_requested()
    {
        $this->expectException(ComponentNotRegisteredException::class);

        $this->manager->component(self::COMPONENT);
    }

    /**
     * @test
     * @throws ComponentNotRegisteredException
     */
    public function it_throws_an_exception_when_no_component_name_is_supplied_and_no_default_is_registered()
    {
        $this->expectException(ComponentNotRegisteredException::class);

        $this->manager->register(self::COMPONENT);

        $this->manager->component();
    }

    /**
     * @test
     * @throws ComponentNotRegisteredException
     */
    public function it_will_return_the_default_component_when_a_name_is_not_specified()
    {
        View::shouldReceive('make');

        $this->manager->registerDefault(self::COMPONENT);

        $default = $this->manager->component();

        $this->assertEquals(self::COMPONENT, $default->getName());
    }

    /** @test */
    public function it_can_specify_the_template_and_data()
    {
        $template = 'template';
        $data = ['key' => 'value'];

        $this->manager->prepareTemplate($template, $data);

        View::shouldReceive('make')
            ->once()
            ->with($template, $data, \Mockery::any());

        $this->manager->render(self::COMPONENT);
    }

    /** @test */
    public function it_can_render_single_dependencies()
    {
        \config()->set('vue_helper.use_mix', false);

        $this->manager->register(self::COMPONENT, [], 'my-dependency.js');

        $deps = $this->manager->dependencies();

        $this->assertEquals((string)$deps, $deps->render());

        $expectedOutput = <<<JSC
<script src="my-dependency.js"></script>

JSC;

        $this->assertEquals($expectedOutput, $deps->render());
    }

    /** @test */
    public function it_can_render_multiple_dependencies()
    {
        \config()->set('vue_helper.use_mix', false);

        $this->manager->register(self::COMPONENT, [], ['my-dep-1.js', 'my-dep-2.js']);

        $deps = $this->manager->dependencies();

        $expectedOutput = <<<JSC
<script src="my-dep-1.js"></script>
<script src="my-dep-2.js"></script>

JSC;

        $this->assertEquals($expectedOutput, $deps->render());
    }

    /** @test */
    public function it_uses_mix_for_dependencies_by_default()
    {
        $this->manager->register(self::COMPONENT, [], 'my-dependency.js');

        $this->expectExceptionMessage('The Mix manifest does not exist');

        $this->manager->dependencies()->render();
    }
}