<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Blade template
    |--------------------------------------------------------------------------
    |
    | Specify the Blade template to render (as you would pass to the view()
    | helper or View::make()) when no explicit template is supplied.
    | e.g. 'layout' would refer to 'resources/views/layout.blade.php'
    |
    */
    'default_template' => 'layout',

    /*
    |--------------------------------------------------------------------------
    | Default $mount element
    |--------------------------------------------------------------------------
    |
    | The selector for the DOM element that a component should be mounted to
    | if none is supplied.
    |
    */
    'default_mount_el' => '#app',

    /*
    |--------------------------------------------------------------------------
    | Default variable assignment
    |--------------------------------------------------------------------------
    |
    | The default JavaScript variable that the new Vue instance is assigned to.
    | Leave null if you don't want to assign a variable.
    |
    */
    'default_variable' => null,

    /*
    |--------------------------------------------------------------------------
    | Use Laravel Mix for dependencies
    |--------------------------------------------------------------------------
    |
    | If true, any registered dependencies for a component will be wrapped in
    | mix() calls.
    |
    */
    'use_mix' => true,

    /*
    |--------------------------------------------------------------------------
    | Vue global
    |--------------------------------------------------------------------------
    |
    | The name of the global Vue object (i.e. 'new Vue(...)')
    |
    */
    'vue_global' => 'Vue',

    /*
    |--------------------------------------------------------------------------
    | Additional configuration
    |--------------------------------------------------------------------------
    |
    | Additional configuration data to pass into the Vue constructor when
    | mounting components. If you need to register a Vuex store or Vue router,
    | this is the place to specify it.
    | e.g. to specify a Vuex store called myStore, set this to
    | ['store' => 'myStore']
    |
    */
    'additional_config' => [],
];