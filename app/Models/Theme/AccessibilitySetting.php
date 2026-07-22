<?php

namespace App\Models\Theme;

use App\Models\Model;

class AccessibilitySetting extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting_key', 'name', 'description', 'input_type', 'panel_key',
        'options_data', 'default_value', 'sort_order', 'is_constrained', 'is_active',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accessibility_settings';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options_data'   => 'array',
        'is_constrained' => 'boolean',
        'is_active'      => 'boolean',
    ];

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $rules = [
        'setting_key' => 'required|string',
        'name'        => 'required|string|max:255',
        'input_type'  => 'required|string',
        'panel_key'   => 'required|string',
    ];

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Show only active settings.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('is_active', 1);
    }

    /**
     * Order settings for display: grouped and ordered by sort.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSorted($query) {
        return $query->orderBy('panel_key')->orderBy('sort_order')->orderBy('id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * What this setting targets, with any per-theme overrides pulled in.
     */
    public function getTargetAttribute() {
        $catalog = config('lorekeeper.themes.accessibility.settings.'.$this->setting_key);
        if (!$catalog) {
            return null;
        }

        $override = AccessibilityOverride::mapped()->get($this->setting_key);
        if ($override) {
            if (isset($override['selector']) && $override['selector'] != '') {
                $catalog['selector'] = $override['selector'];
            }
            if (isset($override['property']) && $override['property'] != '') {
                $catalog['property'] = $override['property'];
            }
        }

        return $catalog;
    }
}
