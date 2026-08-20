<?php

namespace App\Models\Housing;

use App\Models\Model;

class HousingZoneColor extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'zone_id', 'hex', 'sort',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'housing_zone_colors';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the zone this color belongs to.
     */
    public function zone() {
        return $this->belongsTo(HousingZone::class, 'zone_id');
    }
}
