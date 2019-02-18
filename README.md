# Laravel Vue Component Helper
## Configure and return Vue components from your Laravel app

### How does it work?

This package provides helper functions to specify and configure Vue components right in your Laravel controllers. You can then output them directly into your Blade templates with zero configuration.

If you have a common layout that you typically render one component into per page, you can simply return that component from the controller and the template will be rendered with your component in it. No more ugly `json_encode`ing arrays in your templates.

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

The easiest way to start using this package is with the global `vue()` helper function. Calling it with no arguments returns the `VueComponentManager` singleton, which is the primary way to utilise the functionality the package provides. You may also pass a component name and props to the function: when returning this from a controller (much in the same way as Laravel’s own `view()` helper), your specified component will be immediately configured, and the default view will be rendered.

*Example 1: Default component*

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

The package provides several Blade directives for outputting components. Let’s look at the one to inject components into inline-templates:

```html
<!-- layout.blade.php -->

<div id="app">
	@vue_component
</div>
```

Easy! Under the hood, this will be converted to:

```html
<div id="app">
	<user-index v-bind="{ users: [{ email: date }, ...] }"></user-index>
</div>
```

If you’re running the out-of-the-box Laravel configuration, where Vue is already set up with the runtime compiler and mounted to `#app`, you’re good to go — no further configuration necessary.

If you would rather not use the Blade directives you can call the underlying functions directly:

```
{!! vue()->component()->inject() !!}
```

and a Facade is provided if that’s more your style:

```
{!! Vue::component()->inject() !!}
```

*Example 2: Named components*

Of course, you’re not just limited to one component. Returning a component via the `vue()` helper function will register it as the default, but you can also register any number of components and refer back to them by name in your template:

```php
// MyController.php

public function index()
{
	vue()->register('UserIndex', ['users' => $users]);
	vue()->register('AnotherComponent')

	return view('layout');
}
```

```html
<!-- layout.blade.php -->

<div id="app">
	@vue_component(UserIndex)
	@vue_component(AnotherComponent)
</div>
```

::Be careful::: Custom Blade directives may look like PHP function calls, but the contents of the parentheses are parsed as a single unit and sent as a string to the handling function. Long story short: don’t add quotes around your component name!

*Example 3: Component mounting*

What if you’re using precompiled Vue components, and mounting directly to a DOM element via a render function? No problem:

```html
<!-- layout.blade.php -->

<div id="app"></div>
<script src="js/app.js"></script>
@vue_mount
```

This directive will be converted to:

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

Like `@vue_component`, `@vue_mount` can also take a component name if you wish to use a named component rather than the default. However, it also accepts two other arguments: the selector of the element to mount to (a.k.a `$el`), and the variable to assign the root Vue instance too.

For example:

```html
<!-- layout.blade.php -->

<div id="root"></div>
<script src="js/app.js"></script>
@vue_mount(UserIndex, #root, app)
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

::Be careful::: For exactly the same reasons as above, care must be taken when passing “arguments” to custom Blade directives. As well as avoiding quotes, if you wish to pass any argument you must pass them all up to and including the last argument specified, even if some arguments are an empty string. For example, if you wish to specify the variable for the root Vue instance, but wish to use the default component and mount elements, you would use `@vue_mount(, , app)` . Somewhat silly, but something we must put up with when using Blade directives :)

*Example 4: Extra dependencies*

What if the component you want to use is loaded into Vue in a JavaScript file that is not part of your normal app bundle? When using the `register()` method to configure a component, you may pass a string or an array of strings as a third argument. This is a list of JavaScripts that should be included on the page. By default these will be passed through Laravel Mix’s `mix()` function, so versioning is handled for you automatically:

```php
// MyController.php

public function index()
{
	vue()->register('UserIndex', ['users' => $users], 'js/components.js');

	return view('layout');
}
```

Then, simply use the `@vue_dependencies` directive to load the script(s) on the page. Don’t forget to make sure they’re included before Vue is instantiated!

```html
<!-- layout.blade.php -->

<div id="root"></div>
<script src="js/app.js"></script>
@vue_directives
@vue_mount
```

### A Note on Registering Components with `Vue.component()`

Because of the wide variety of ways components can be registered with Vue via `Vue.component()` (objects fetched via `import`s with ES6 modules, `require()` calls, references to objects in the current context, Webpack’s dynamic `import()` statement), and the fact that this is very often done in the build step rather than in the browser, making sure that the components you want to use are correctly registered with Vue is left up to you for now.

If you are using Webpack, setting up code-splitting and registering all of your components asynchronously using dynamic `import()`s is a very straightforward way of handling it, otherwise you can include small scripts that register the component, then include them as dependencies as described above.

### API

### Configuration

