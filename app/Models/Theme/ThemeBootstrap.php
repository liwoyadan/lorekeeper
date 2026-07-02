<?php

namespace App\Models\Theme;

use App\Models\Model;
use Illuminate\Support\Facades\Cache;

class ThemeBootstrap extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'color_data', 'theme_color_data', 'style_data', 'custom_scss_data',
        'custom_prepend', 'custom_append', 'is_default', 'hash',
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
        'is_default'        => 'boolean',
    ];

    /**
     * Validation rules. Create & update have the same rules.
     *
     * @var array
     */
    public static $rules = [
        'name'           => 'required|string|max:255',
        'custom_prepend' => 'nullable',
        'custom_append'  => 'nullable',
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
     * Gets the file name of the model's compiled css.
     * Includes the hash for cache-busting; null until first compile.
     *
     * @return string|null
     */
    public function getCompiledFileNameAttribute() {
        return $this->hash ? $this->id.'-'.$this->hash.'.css' : null;
    }

    /**
     * Gets the URL of the model's compiled css.
     *
     * @return string|null
     */
    public function getCompiledUrlAttribute() {
        return $this->compiledFileName ? $this->imageDirectory.'/'.$this->compiledFileName : null;
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort bootstrap themes in alphabetical order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool                                  $reverse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortAlphabetical($query, $reverse = false) {
        return $query->orderBy('name', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort bootstrap themes by newest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $reverse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query, $reverse = false) {
        return $query->orderBy('id', $reverse ? 'ASC' : 'DESC');
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Gets the compiled CSS URL of the default Bootstrap.
     * (cached so it doesn't query on every page)
     *
     * @return string
     */
    public static function defaultCompiledUrl() {
        return Cache::rememberForever('default_bootstrap_css', function () {
            $default = self::where('is_default', 1)->first();

            return $default && $default->compiledFileName ? $default->compiledUrl : '';
        });
    }

    /**
     * Clears the cached default bootstrap CSS URL.
     */
    public static function clearDefaultCache() {
        Cache::forget('default_bootstrap_css');
    }
}
