<?php

namespace App\Models\Raid;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class RaidBoss extends Model {
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'raid_id', 'description', 'parsed_description', 'data',
        'health', 'damage', 'is_visible', 'sort',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'raid_bosses';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for raid creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'               => 'required|between:3,100',
    ];

    /**
     * Validation rules for raid updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'               => 'required|between:3,100',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the raid this boss is attached to.
     */
    public function raid() {
        return $this->belongsTo(Raid::class, 'raid_id');
    }

    /**
     * Get the images that belong to this boss.
     */
    public function images() {
        return $this->hasMany(RaidBossImage::class, 'raid_boss_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include visible bosses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, $user = null) {
        if ($user && $user->isStaff) {
            return $query;
        }

        return $query->where('is_visible', 1);
    }

    /**
     * Scope a query to sort bosses in alphabetical order.
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
     * Scope a query to sort bosses by newest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query, $reverse = false) {
        return $query->orderBy('id', $reverse ? 'ASC' : 'DESC');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Displays the model's name, linked to its page.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return '<a href="'.$this->url.'" class="display-raid">'.$this->name.'</a>';
    }

    /**
     * Gets the URL of the model's page.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('raids/bosses/'.$this->id);
    }

    /**
     * Gets the boss's HP thresholds.
     *
     * @return array
     */
    public function getThresholdsAttribute() {
        if (!isset($this->data) || !isset($this->data['thresholds'])) {
            return null;
        }

        return $this->data['thresholds'];
    }

    /**
     * Gets the raid's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute() {
        return 'bosses';
    }

    /**
     * Gets the admin edit URL.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/data/raids/bosses/edit/'.$this->id);
    }

    /**
     * Gets the power required to edit this model.
     *
     * @return string
     */
    public function getAdminPowerAttribute() {
        return 'edit_data';
    }
}
