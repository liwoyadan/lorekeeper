<?php

return [
    'kinds' => [
        'furniture' => 'Furniture',
        'wall'      => 'Wall',
        'floor'     => 'Floor',
    ],

    'render_modes' => [
        'mask' => 'Image mask (PNG or SVG mask)',
        'svg'  => 'Native SVG fill',
    ],

    'layers' => [
        'back'  => 'Back (behind furniture)',
        'mid'   => 'Mid (furniture)',
        'front' => 'Front (foreground)',
    ],

    'stage_ratio' => '3 / 2',

    'reference_width' => 960,

    'min_scale' => 3,

    'max_scale' => 100,

    'backdrop_z' => [
        'wall'  => 1,
        'floor' => 2,
    ],
];
