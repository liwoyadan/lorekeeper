<?php

namespace App\Models\Theme;

use App\Models\Model;

class ThemeBootstrap extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'color_data', 'theme_color_data', 'style_data', 'custom_scss_data', 'has_scss',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'theme_bootstraps';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'color_data'        => 'array',
        'theme_color_data'  => 'array',
        'style_data'        => 'array',
        'custom_scss_data'  => 'array',
    ];
    
    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the themes this Bootstrap is attached to.
     */
    public function themes() {
        return $this->hasMany(Theme::class, 'theme_bootstrap_id');
    }
    
    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/
    
    /**
     * Gets the file directory containing the model's files.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'themes/compiled';
    }

    /**
     * Gets the path to the file directory containing the model's files.
     *
     * @return string
     */
    public function getImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the file name of the model's css.
     *
     * @return string
     */
    public function getCompiledFileNameAttribute() {
        return $this->id.'-bootstrap.css';
    }

    /**
     * Gets the URL of the model's css.
     *
     * @return string
     */
    public function getCompiledUrlAttribute() {
        return $this->imageDirectory.'/'.$this->compiledFileName;
    }
    
    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/
        
    /**
     * Gets file name of the model's scss.
     *
     * @return string
     */
    public function scssFileName($name = null) {
        if (!$name) {
            $name = 'app';
        }

        return $this->id.'_'.$name.'.scss';
    }
}
