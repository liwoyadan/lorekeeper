<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Taggable Models
    |--------------------------------------------------------------------------
    |
    | Registry of models that participate in the tag system. Keys are URL
    | slugs used in admin tag routes; values are fully-qualified class names.
    | The controller and service resolve a model by looking up its slug here.
    |
    | Adding a new taggable model requires:
    |   1) Add an entry here (slug => class).
    |   2) Add a matching entry to 'types' below keyed by the class basename.
    |   3) Use the App\Traits\ConfiguredTags trait on the model.
    |
    */

    'models' => [
        'species'  => \App\Models\Species\Species::class,
        'feature'  => \App\Models\Feature\Feature::class,
        'prompt'   => \App\Models\Prompt\Prompt::class,
        'shop'     => \App\Models\Shop\Shop::class,
        'item'     => \App\Models\Item\Item::class,
        'currency' => \App\Models\Currency\Currency::class,
        'sitepage' => \App\Models\SitePage::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pool Membership
    |--------------------------------------------------------------------------
    |
    | Controls how each model's tags are grouped for autocomplete and search.
    | Keys are model class basenames; values declare pool membership:
    |
    |   true            Share the global tag pool with every other `true`
    |                   entry. One big autocomplete across all of them.
    |
    |   false           Exclusive pool. This model's tags are not visible
    |                   to any other model.
    |
    |   string          Named group. All entries with the same string share
    |                   a pool, isolated from the global pool and from other
    |                   named groups. Example: 'Item' => 'marketplace',
    |                   'Shop' => 'marketplace'.
    |
    */

    'types' => [
        'Species'  => false,
        'Feature'  => false,
        'Prompt'   => false,
        'Shop'     => false,
        'Item'     => false,
        'Currency' => false,
        'SitePage' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Power Requirements
    |--------------------------------------------------------------------------
    |
    | Per-slug admin power required to sync tags on that model. Slugs without
    | an entry fall back to 'edit_data'.
    |
    */

    'powers' => [
        'sitepage' => 'edit_pages',
    ],

];
