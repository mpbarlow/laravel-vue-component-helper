# Laravel Vue Component Helper
## Configure and return Vue components from your Laravel app

### How does it work?

This package provides helper functions to configure Vue components right in your Laravel controllers. You can then output them directly into your Blade templates with zero configuration.

If you have a common layout that you typically render one component into per page, you can even just return that component from the controller and the template will be rendered with your component in it. 

Alternatively, if you just want an easier way to configure and render your Vue components, this package still has you covered. No more `json_encode`ing arrays in your templates.

### Installation

You can install this package via Composer. It requires Laravel 5.5 or above, PHP 7.0 or above, and has no additional dependencies.
```
composer require mpbarlow/laravel-vue-component-helper
```

The package uses Laravel auto-discovery, so no further configuration is needed. However, you will almost certainly want to publish the config file:
```
php artisan vendor:publish --provider="Mpbarlow\LaravelVueComponentHelper\ServiceProvider"
```

This will copy the config file to `config/vue_helper.php`. More on that later.

### Getting Started

The easiest way to start using this package is with the global `vue()` helper function. Calling it with no arguments returns the `VueComponentManager` instance, which is the primary way to make use of the functionality the package provides. You may also pass a component name and props to the function when returning this from a controller (much in the same way as Laravel’s own `view()` helper) &mdash; your specified component will be immediately registered with the package, and the default view will be rendered.

**Example 1: Default component**

As a basic example, let’s assume you have a Vue component called UserIndex, that displays the email addresses and account creation dates of your users.

```php
// MyController.php

public function index()
{
  $users = User::all()->map(function ($user) {
    return [$user->email => $user->created_at];
  })->toArray();

  return vue('UserIndex', ['users' => $users]);
}
```

This will set the `UserIndex` component as the default component, set its props to the supplied array, and render the default view specified in the config. But, how do we actually display our component in that view?

The package provides several Blade directives for outputting components. Let’s look at the one to inject components into inline templates:

```html
<!-- layout.blade.php -->

<div id="app">
  @vue_component
</div>
```

Easy! Under the hood, this will be converted to:

```vue
<div id="app">
  <user-index v-bind="{ users: [{ email: date }, ...] }"></user-index>
</div>
```

If you’re running the out-of-the-box Laravel configuration, where Vue is already set up with the runtime template compiler and mounted to `#app`, you’re good to go — no further configuration necessary.

If you would rather not use the Blade directives you can call the underlying functions directly:

```
{!! vue()->component()->inject() !!}
```

and a Facade is provided if that’s more your style:

```
{!! Vue::component()->inject() !!}
```

**Example 2: Named components**

Of course, you’re not just limited to one component. Returning a component via the `vue()` helper function will register it as the default, but you can also register any number of components and refer back to them by name in your template:

```php
// MyController.php

public function index()
{
  vue()
    ->register('UserIndex', ['users' => $users]);
    ->register('AnotherComponent')

  return view('layout');
}
```

```html
<!-- layout.blade.php -->

<div id="app">
  @vue_component('UserIndex')
  @vue_component('AnotherComponent')
</div>
```

**Example 3: Component mounting**

What if you’re using precompiled Vue components, and mounting directly to a DOM node via a render function? No problem, just use the `@vue_mount` directive:

```html
<!-- layout.blade.php -->

<div id="app"></div>
<script src="js/app.js"></script>
@vue_mount
```

This will be converted to:

```html
<!-- layout.blade.php -->

<div id="app"></div>
<script src="js/app.js"></script>
<script>
  new Vue({
    render: function(h) {
      return h('UserIndex', { props: { users: [{ email: date }, ...] } })
    }
  }).$mount('#app')
</script>
```

All you need to remember is to make sure that Vue is loaded before you try to mount, and that it isn’t already instantiated by another script.

Like `@vue_component`, `@vue_mount` can also take a component name if you wish to use a named component rather than the default. However, it also accepts two other arguments: the selector of the element to mount to (a.k.a `$el`), and the variable to assign the root Vue instance to.

For example:

```html
<!-- layout.blade.php -->

<div id="root"></div>
<script src="js/app.js"></script>
@vue_mount('UserIndex', '#root', 'app')
```

would result in:

```html
<!-- layout.blade.php -->

<div id="root"></div>
<script src="js/app.js"></script>
<script>
  var app = new Vue({
    render: function(h) {
      return h('UserIndex', { props: { users: [{ email: date }, ...] } })
    }
  }).$mount('#root')
</script>
```

**Example 4: Extra dependencies**

What if the component you want to use is loaded into Vue in a JavaScript file that is not part of your normal app bundle? When using the `register()` method to configure a component, you may pass a string or an array of strings as a third argument. This is a list of JavaScripts that should be included on the page. By default these will be passed through Laravel Mix’s `mix()` function, so versioning is handled for you automatically:

```php
// MyController.php

public function index()
{
	vue()->register('UserIndex', ['users' => $users], 'js/components.js');

	return view('layout');
}
```

Then, simply use the `@vue_dependencies` directive to load the script(s) on the page. 

```html
<!-- layout.blade.php -->

<div id="root"></div>
<script src="js/app.js"></script>
@vue_dependencies
@vue_mount
```

### A note on registering components with Vue itself

Because of the wide variety of ways components can be registered with Vue via `Vue.component()` (objects fetched via `import`s with ES6 modules, `require()` calls, references to objects in the current context, Webpack’s dynamic `import()` statement, inline template strings), and the fact that this is very often done prior to the build step rather than in the browser, making sure that the components you want to use are known to Vue is left up to you for now.

If you are using Webpack, setting up code-splitting and registering all of your components asynchronously using dynamic `import()`s is a very straightforward way of handling it, otherwise you can include small scripts that register the component, then include them as dependencies as described above.

### API

**Helper function**

`vue() : VueComponentManager`

Call the helper function with no arguments to get the core service instance that the package provides.

Of course, you may also access its methods via the facade `Vue::` if you’d prefer, or type hint it in your controller methods to auto-inject from the container, or grab it by name with `app(‘Mpbarlow\LaravelVueComponentHelper\VueComponentManager’)`. And, if you really don’t like singletons, there is nothing stopping you instantiating `VueComponentManager` yourself — you just won’t be able to use the Blade directives as they hook back into the instance already in the container.
- - - -

`vue(string $componentName, array $props = [], ?string $template = null, array $templateData = []) : \Illuminate\View\View`

As previously demonstrated, the helper function can also accept a number of arguments. Calling the function with any number of parameters greater than zero will result in a Blade template being rendered, so you should typically return it from your controllers if using it in this way.

`string $componentName`: The name of the component that is rendered when no name is provided to the directive/render methods.

`array $props = []`: The props to pass to the default component.

`?string $template = null`: The name of the Blade template to render. This value is passed directly to Laravel’s `view()` function. If no value is provided, the default template specified in the config is used.

`array $templateData = []`: The data to be passed to the Blade template. Again, this is passed directly through to Laravel’s `view()` function.
- - - -

**VueComponentManager**

The bulk of the functionality this package offers can be found in this class. It has the following public API:

`register(string $componentName, array $props = [], array|string $from = []) : self`

Register a Vue component with the manager so that it may be rendered into a view.

`string $componentName`: This should match exactly the name used to register the component with Vue in JavaScript. It will be automatically kebab-cased if rendered into a template.

`array $props = []`: The props to pass to the component.

`array|string $from = []`: A string or array of paths to JavaScript files required by the component. Unless otherwise specified in the config, these are passed through Laravel Mix’s `mix()` function to resolve version hashes etc.
- - - -

`registerDefault(string $componentName, array $props = [], array|string $from = []) : self`

Identical to `register`, but also sets the component as the default.

- - - -

`prepareTemplate(?string $template = null, array $templateData = []) : self`

Set the template path and data to pass through to Laravel’s `view()` function if using `render()`.
- - - -

`render(string $componentName, array $props = []) : \Illuminate\View\View`

Register the provided component as the default, then render the default or a previously configured Blade template.
- - - -

`component(string $componentName = '') : VueComponent`

Returns the instance of `VueComponent` matching the provided name. If no name is supplied, the default component is returned.
- - - -

`dependencies() : DependencyRenderer`

Returns an instance of `DependencyRenderer`, which can be used to output the JavaScript dependencies as `<script>` tags.
- - - -

**VueComponent**

This class encapsulates a registered component, and can be used to render or mount the component in your HTML template.

`getName() : string`

Return the component name.
- - - -

`getTagName() : string`

Return the component name in kebab-case, suitable for use as a custom HTML element.
- - - -

`getProps() : ?array`

Return the component’s props.
- - - -

`getPropsJson(bool $escape = false) : string`

Get the component’s props as a JSON string.

`bool $escape = false`: If true, the string is passed through `htmlentities()` before being returned. This is required if you want to output the string within a HTML element.
- - - -

`inject() : ComponentInjector`

Return this component’s injector, which can be used to insert the component as a custom HTML tag. This is the method used by the `@vue_component` directive.
- - - -

`mount(?string $to = null , ?string $var = null) : ComponentMounter`

Return this component’s mounter, which can be used to insert the component via a render function with the Vue constructor. This is the method used by the `@vue_mount` directive.

`?string $to = null`:  The selector for the DOM element that the root Vue instance should mount to. If this is not supplied, the default value in the config is used.

`?string $var = null`:  The JavaScript variable name that the root Vue instance should be assigned to. If this is not supplied, the default value in the config is used. If `null` is used, the root instance is not assigned to a variable.
- - - -

**ComponentInjector**, **ComponentMounter**, and **DependencyRenderer**

These classes each implement only one method: `render()`. `__toString()` also calls this function, so outputting the object directly in a template is sufficient to perform the render.

### Configuration

The configuration file offers some options, as well as default values to streamline your controller code:

```php
[
    /*
    |-----------------------------------------------------
    | Default Blade template
    |-----------------------------------------------------
    |
    | Specify the Blade template to render (as you would pass to the view()
    | helper or View::make()) when no explicit template is supplied.
    | e.g. 'layout' would refer to 'resources/views/layout.blade.php'
    |
    */
    'default_template' => 'layout',

    /*
    |-----------------------------------------------------
    | Default $mount element
    |-----------------------------------------------------
    |
    | The selector for the DOM element that a component should be mounted to
    | if none is supplied.
    |
    */
    'default_mount_el' => '#app',

    /*
    |-----------------------------------------------------
    | Default variable assignment
    |-----------------------------------------------------
    |
    | The default JavaScript variable that the new Vue instance is assigned to.
    | Leave null if you don't want to assign a variable.
    |
    */
    'default_variable' => null,

    /*
    |-----------------------------------------------------
    | Use Laravel Mix for dependencies
    |-----------------------------------------------------
    |
    | If true, any registered dependencies for a component will be wrapped in
    | mix() calls.
    |
    */
    'use_mix' => true,

    /*
    |-----------------------------------------------------
    | Vue global
    |-----------------------------------------------------
    |
    | The name of the global Vue object (i.e. 'new Vue(...)')
    |
    */
    'vue_global' => 'Vue',

    /*
    |-----------------------------------------------------
    | Additional configuration
    |-----------------------------------------------------
    |
    | Additional configuration data to pass into the Vue constructor when
    | mounting components. If you need to register a Vuex store or Vue router,
    | this is the place to specify it.
    | e.g. to specify a Vuex store called myStore, set this to
    | ['store' => 'myStore']
    |
    | This only supports one-dimensional arrays for now, so if you need nested
    | objects or functions, you'll need to type them out as a string.
    |
    */
    'additional_config' => [],
];
```

### Running Unit Tests

`./vendor/bin/phpunit`
