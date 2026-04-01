<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Forums Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains some configuration options for forums overall.
    |
    */
    
    // Allow signatures
    // If enabled, users can set a custom forum signature
    'allow_signatures' => [
        'enabled'     => 1,
        'max_height'  => 55, // maximum height for signatures in a comment (px), will scroll
    ],

    // Allow users to upload their own custom forum decors
    'user_uploads' => [
        'background' => [
            'enabled'         => 1,
            'max_dimension'   => 1000, // This is in px, either side
            'default_opacity' => 15,   // Default opacity applied to user-uploaded backgrounds (0–100)
            'max_opacity'     => 50,   // Maximum opacity a user can set for their uploaded background (0–100)
        ],
    ],

    'decor_types' => [
        'background' => 'Post Background',
        'border'     => 'Post Border',
    ],

    // Forum Posts Editable by Author - Wych
    'forum_author_edit' => 1,
];
