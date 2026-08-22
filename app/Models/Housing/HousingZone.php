<?php

namespace App\Models\Housing;

use App\Models\Model;

class HousingZone extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'decor_id', 'name', 'sort', 'has_mask', 'hash', 'svg_selector', 'allow_free_color',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'housing_zones';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the decor this zone belongs to.
     */
    public function decor() {
        return $this->belongsTo(HousingDecor::class, 'decor_id');
    }

    /**
     * Get the patterns available for this zone.
     */
    public function patterns() {
        return $this->belongsToMany(HousingPattern::class, 'housing_zone_pattern', 'zone_id', 'pattern_id');
    }

    /**
     * Get the preset colors for this zone.
     */
    public function colors() {
        return $this->hasMany(HousingZoneColor::class, 'zone_id')->orderBy('sort');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the file directory containing the model's image.
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/housing/zones';
    }

    /**
     * Gets the file name of the model's mask image.
     */
    public function getMaskFileNameAttribute() {
        return $this->hash.$this->id.'-mask.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     */
    public function getMaskPathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's mask image.
     */
    public function getMaskUrlAttribute() {
        if (!$this->has_mask) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->maskFileName);
    }
}
