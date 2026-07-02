<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Color Step Default
    |--------------------------------------------------------------------------
    |
    | The percentage the "Default"/"Set all" step buttons apply in the Bootstrap
    | theme editor. Step is % how much darker/lighter a colour becomes up and down
    | from ${colour}-500.
    |
    */

    'theme_color_step_default' => 5,

    /*
    |--------------------------------------------------------------------------
    | Theme Color Palettes
    |--------------------------------------------------------------------------
    |
    | Default Bootstrap colour groups: base, the greys, and theme colours.
    | Keys are Bootstrap 4 colour identifiers; the defaults for colours
    | were what shipped with Lorekeeper default. The rest are Bootstrap default.
    |
    */

    'base_colors' => [
        'blue'   => ['label' => 'Blue',   'default' => '#3490dc'],
        'indigo' => ['label' => 'Indigo', 'default' => '#6574cd'],
        'purple' => ['label' => 'Purple', 'default' => '#9561e2'],
        'pink'   => ['label' => 'Pink',   'default' => '#f66d9b'],
        'red'    => ['label' => 'Red',    'default' => '#e3342f'],
        'orange' => ['label' => 'Orange', 'default' => '#f6993f'],
        'yellow' => ['label' => 'Yellow', 'default' => '#ffed4a'],
        'green'  => ['label' => 'Green',  'default' => '#38c172'],
        'teal'   => ['label' => 'Teal',   'default' => '#4dc0b5'],
        'cyan'   => ['label' => 'Cyan',   'default' => '#6cb2eb'],
    ],

    'grays' => [
        'white'    => ['label' => 'White',    'default' => '#ffffff'],
        'gray-100' => ['label' => 'Gray 100', 'default' => '#f8f9fa'],
        'gray-200' => ['label' => 'Gray 200', 'default' => '#e9ecef'],
        'gray-300' => ['label' => 'Gray 300', 'default' => '#dee2e6'],
        'gray-400' => ['label' => 'Gray 400', 'default' => '#ced4da'],
        'gray-500' => ['label' => 'Gray 500', 'default' => '#adb5bd'],
        'gray-600' => ['label' => 'Gray 600', 'default' => '#6c757d'],
        'gray-700' => ['label' => 'Gray 700', 'default' => '#495057'],
        'gray-800' => ['label' => 'Gray 800', 'default' => '#343a40'],
        'gray-900' => ['label' => 'Gray 900', 'default' => '#212529'],
        'black'    => ['label' => 'Black',    'default' => '#000000'],
    ],

    'theme_colors' => [
        'primary'   => ['label' => 'Primary',   'default' => '#3490dc'],
        'secondary' => ['label' => 'Secondary', 'default' => '#6c757d'],
        'success'   => ['label' => 'Success',   'default' => '#38c172'],
        'info'      => ['label' => 'Info',      'default' => '#6cb2eb'],
        'warning'   => ['label' => 'Warning',   'default' => '#ffed4a'],
        'danger'    => ['label' => 'Danger',    'default' => '#e3342f'],
        'light'     => ['label' => 'Light',     'default' => '#f8f9fa'],
        'dark'      => ['label' => 'Dark',      'default' => '#343a40'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Non-color Variable Groups
    |--------------------------------------------------------------------------
    |
    | Other variable groups that aren't colour palette related.
    | The type value (where applicable) is used to help with units
    | for compilation (i.e. appending 'px' to a bare width number).
    |
    */

    'styles' => [
        'border-radius' => [
            'label' => 'Border Radius',
            'default' => '0.25rem'
        ],
        'border-width'  => [
            'label' => 'Border Width',
            'default' => '1',
            'type' => 'width'
        ],
        'border-color'  => [
            'label' => 'Border Color',
            'default' => '#dee2e6'
        ],
        'border-style'  => [
            'label' => 'Border Style',
            'default' => 'solid'
        ],
    ],

    'typography' => [
        'body-color'     => [
            'label' => 'Body Text Color',
            'default' => '#212529'
        ],
        'headings-color' => [
            'label' => 'Headings Color',
            'default' => 'inherit'
        ],
    ],

    'extras' => [
        'text-muted'      => [
            'label' => 'Muted Text',
            'default' => '#6c757d',
            'type' => 'color'
        ],
        'hr-border-color' => [
            'label' => 'Divider Color',
            'default' => 'rgba(0,0,0,.1)',
            'type' => 'color'
        ],
        'hr-border-width' => [
            'label' => 'Divider Width',
            'default' => '1',
            'type' => 'width'
        ],
    ],

    'tooltips' => [
        'tooltip-bg'            => [
            'label' => 'Background',
            'default' => '#000',
            'type' => 'color'
        ],
        'tooltip-color'         => [
            'label' => 'Text Color',
            'default' => '#fff',
            'type' => 'color'
        ],
        'tooltip-border-radius' => [
            'label' => 'Border Radius',
            'default' => '0.25rem',
            'type' => 'text'
        ],
        'tooltip-opacity'       => [
            'label' => 'Opacity',
            'default' => '0.9',
            'type' => 'opacity'
        ],
    ],

    'thumbnails' => [
        'thumbnail-bg'            => [
            'label' => 'Background',
            'default' => '#fff',
            'type' => 'color'
        ],
        'thumbnail-border-width'  => [
            'label' => 'Border Width',
            'default' => '1',
            'type' => 'width'
        ],
        'thumbnail-border-color'  => [
            'label' => 'Border Color',
            'default' => '#dee2e6',
            'type' => 'color'
        ],
        'thumbnail-border-radius' => [
            'label' => 'Border Radius',
            'default' => '0.25rem',
            'type' => 'text'
        ],
    ],

    'toggles' => [
        'enable-rounded'               => [
            'label' => 'Rounded Corners',       
            'default' => true,  
            'help' => 'Rounds corners on buttons, cards, inputs, and other components.'
        ],
        'enable-shadows'               => [
            'label' => 'Shadows',               
            'default' => false, 
            'help' => 'Adds subtle box-shadows to components like buttons, cards, and dropdowns for depth.'
        ],
        'enable-gradients'             => [
            'label' => 'Gradients',             
            'default' => false, 
            'help' => 'Applies subtle background gradients to components such as buttons and the navbar.'
        ],
        'enable-responsive-font-sizes' => [
            'label' => 'Responsive Font Sizes', 
            'default' => false, 
            'help' => 'Scales font sizes fluidly with the viewport width instead of fixed breakpoints (RFS).'
        ],
    ],

    'border_styles' => ['solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset', 'none', 'hidden'],

    /*
    |--------------------------------------------------------------------------
    | Custom Variable Suggestions
    |--------------------------------------------------------------------------
    |
    | Specific Bootstrap variables for the custom variables dropdown that 
    | site owners would probably like ease of retheming; grouped by component. The 
    | selectize allows just typing a value, though, so users aren't limited to these.
    |
    */

    'common_variables' => [
        'Cards' => [
            'card-bg', 'card-cap-bg', 'card-color', 'card-border-color', 'card-border-radius',
        ],
        'Buttons' => [
            'btn-border-radius', 'btn-padding-y', 'btn-padding-x', 'btn-font-size',
        ],
        'Alerts & Badges' => [
            'alert-border-radius', 'alert-padding-y', 'alert-padding-x',
            'badge-border-radius', 'badge-font-size', 'badge-padding-y', 'badge-padding-x',
        ],
        'Modals' => [
            'modal-content-bg', 'modal-content-color', 'modal-content-border-color', 'modal-content-border-radius',
        ],
        'Dropdowns' => [
            'dropdown-bg', 'dropdown-color', 'dropdown-border-color', 'dropdown-border-radius', 'dropdown-link-color',
        ],
    ],

];
