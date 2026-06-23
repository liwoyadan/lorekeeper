<?php

return [

    // Selectable subject types for changelogs.
    // Maps the fully-qualified model class (stored in the 'type' column)
    // to a human-readable display name shown in admin selectors.
    'subject_types' => [
        'General' => 'General',
        'App\Models\Item\Item'           => 'Item',
        'App\Models\Currency\Currency'   => 'Currency',
        'App\Models\Species\Species'     => 'Species',
        'App\Models\Species\Subtype'     => 'Subtype',
        'App\Models\Rarity'              => 'Rarity',
        'App\Models\Feature\Feature'     => 'Trait',
        'App\Models\Prompt\Prompt'       => 'Prompt',
        'App\Models\Loot\LootTable'      => 'Loot Table',
        'App\Models\Raffle\Raffle'       => 'Raffle',
        'App\Models\Sales\Sales'         => 'Sale',
        'App\Models\Shop\Shop'           => 'Shop',
        'App\Models\News'                => 'News',
        'App\Models\SitePage'            => 'Site Page',
    ],

    // Subject types whose human-readable label lives in a 'title' column
    // instead of the default 'name' column.
    'title_columns' => [
        'App\Models\News',
        'App\Models\Sales\Sales',
        'App\Models\SitePage',
    ],

];
