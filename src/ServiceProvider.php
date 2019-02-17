<?php

namespace Mpbarlow\LaravelVueComponentHelper;


use Illuminate\Support\Facades\Blade;
use Mpbarlow\LaravelVueComponentHelper\Services\VueComponentManager;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public $singletons = [
        'VueComponentManager' => VueComponentManager::class
    ];

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/vue_helper.php' => \config_path('vue_helper.php'),
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/vue_helper.php', 'vue_helper');
        $this->registerBladeDirectives();
    }

    protected function registerBladeDirectives()
    {
        Blade::directive('vue_component', function (string $component) {
            return "<?php echo \app('VueComponentManager')->getComponent({$component}); ?>";
        });
    }
}