<?php

namespace Mpbarlow\LaravelVueComponentHelper;


use Illuminate\Support\Facades\Blade;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const BINDING = VueComponentManager::class;

    public $singletons = [
        self::BINDING => VueComponentManager::class
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
            return "<?php echo \app('" . self::BINDING . "')->component({$component})->inject(); ?>";
        });

        Blade::directive('vue_mount', function (string $mountDirective) {
            $args = \collect(\explode(',', $mountDirective))
                ->map(function (string $arg) {
                    return \trim($arg);
                });

            // The DOM selector to mount to
            $to = $args->get(1, 'null');

            // The JS variable to assign the root instance to
            $var = $args->get(2, 'null');

            return "<?php echo \app('" . self::BINDING . "')->component({$args[0]})->mount({$to}, {$var}); ?>";
        });

        Blade::directive('vue_dependencies', function () {
            return "<?php echo \app('" . self::BINDING . "')->dependencies(); ?>";
        });
    }
}