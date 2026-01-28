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
        'max_height'  => 125, // maximum height for signatures in a comment (px), will scroll
    ],

    // Allow users to upload their own custom forum post background
    'user_forum_bg_upload' => 0,

];
