<?php

namespace App\Models\Theme;

use App\Models\Model;
use Illuminate\Support\Facades\Cache;

class AccessibilityOverride extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'setting_key', 'selector', 'property',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accessibility_overrides';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $rules = [
        'setting_key' => 'required|string',
        'selector'    => 'nullable|string',
        'property'    => 'nullable|string',
    ];

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Per-theme overrides; these are cached so they don't
     * need to be re-queried every pageview.
     */
    public static function mapped() {
        return Cache::rememberForever('accessibility_overrides_map', function () {
            return self::get()->keyBy('setting_key')->map(function ($row) {
                return ['selector' => $row->selector, 'property' => $row->property];
            });
        });
    }

    /**
     * And just convenience to call to clear cache on updates.
     */
    public static function clearCache() {
        Cache::forget('accessibility_overrides_map');
    }
}
