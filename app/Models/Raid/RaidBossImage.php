<?php

namespace App\Models\Raid;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class RaidBossImage extends Model {
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'raid_boss_id', 'health_threshold', 'hash', 'extension', 'has_image',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'raid_boss_images';

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
        'image'              => 'mimes:png,gif,webp',
    ];

    /**
     * Validation rules for raid updating.
     *
     * @var array
     */
    public static $updateRules = [
        'image'              => 'mimes:png,gif,webp',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the boss this image is attached to.
     */
    public function boss() {
        return $this->belongsTo(RaidBoss::class, 'raid_boss_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort raids in alphabetical order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool                                  $reverse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHealth($query, $reverse = false) {
        return $query->orderBy('health_threshold', $reverse ? 'DESC' : 'ASC');
    }

    /**
     * Scope a query to sort features by newest first.
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
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/raid-bosses';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute() {
        return $this->hash.$this->id.'-image.'.($this->extension ?? 'png');
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getImageUrlAttribute() {
        if (!$this->has_image) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->imageFileName);
    }
}
