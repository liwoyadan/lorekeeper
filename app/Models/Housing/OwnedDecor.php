<?php

namespace App\Models\Housing;

use App\Models\Model;
use App\Models\User\User;

class OwnedDecor extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'decor_id', 'customization', 'count',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'owned_decors';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var bool
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who owns this decor stack.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the decor catalog entry this stack is an instance of.
     */
    public function decor() {
        return $this->belongsTo(HousingDecor::class, 'decor_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the locked customization as a decoded array of zoneId => [type, value].
     *
     * @return array
     */
    public function getCustomizationDataAttribute() {
        return $this->customization ? json_decode($this->customization, true) : [];
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Builds the CSS background declaration for a zone's locked fill (color or
     * pattern), or an empty string when the zone has no applicable fill.
     *
     * @param \App\Models\Housing\HousingZone $zone
     *
     * @return string
     */
    public function zoneFill($zone) {
        $cust = $this->customizationData;
        if (!isset($cust[$zone->id]) || !$zone->has_mask) {
            return '';
        }

        $fill = $cust[$zone->id];
        if ($fill['type'] == 'pattern') {
            $pattern = $zone->patterns->find($fill['value']);

            return $pattern && $pattern->patternImageUrl ? 'background-image: url('.$pattern->patternImageUrl.'); background-repeat: repeat;' : '';
        }

        return 'background-color: #'.$fill['value'].';';
    }
}
