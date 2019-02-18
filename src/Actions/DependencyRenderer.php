<?php


namespace Mpbarlow\LaravelVueComponentHelper\Actions;


use Mpbarlow\LaravelVueComponentHelper\Actions\Interfaces\Action;
use Mpbarlow\LaravelVueComponentHelper\Actions\Traits\Renderable;

/**
 * Renders any registered JavaScript dependencies as <script> tags.
 *
 * @package Mpbarlow\LaravelVueComponentHelper\Actions
 */
class DependencyRenderer implements Action
{
    use Renderable;

    /** @var array */
    protected $dependencies;

    /** @var bool Whether JS dependencies should be ran through Laravel Mix. */
    protected $useMix;

    public function __construct(array $dependencies = [])
    {
        $this->dependencies = $dependencies;
        $this->useMix = \config('vue_helper.use_mix');
    }

    public function render(): string
    {
        return \collect($this->dependencies)
            ->reduce(function ($carry, $dependency) {
                return $carry . $this->renderDependency($dependency);
            }, '');
    }

    /**
     * @param string $dependency
     * @return string
     * @throws \Exception
     */
    protected function renderDependency(string $dependency): string
    {
        $body = $this->useMix ? \mix($dependency) : \e($dependency);

        return "<script src=\"{$body}\"></script>\n";
    }
}