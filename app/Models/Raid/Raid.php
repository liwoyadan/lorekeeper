<?php

namespace App\Models\Raid;

use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

class Raid extends Model {
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'parsed_description', 'data', 'start_at', 'end_at',
        'has_background', 'background_hash', 'background_extension', 'is_visible',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'raids';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
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
        'name'               => 'required|unique:raids|between:3,100',
        'image'              => 'mimes:png,gif,webp',
    ];

    /**
     * Validation rules for raid updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'               => 'required|between:3,100',
        'image'              => 'mimes:png,gif,webp',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the bosses attached to this raid.
     */
    public function bosses() {
        return $this->hasMany(RaidBoss::class, 'raid_id');
    }

    /**
     * Get the logs that belong to the raid.
     */
    public function logs() {
        return $this->hasMany(RaidLog::class, 'raid_id');
    }

    /**
     * Get the rewards attached to this raid.
     */
    public function rewards() {
        return $this->hasMany(RaidReward::class, 'raid_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to only include active raids.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('is_visible', 1)
            ->where(function ($query) {
                $query->whereNull('start_at')->orWhere('start_at', '<', Carbon::now())->orWhere(function ($query) {
                    $query->where('start_at', '>=', Carbon::now());
                });
            })->where(function ($query) {
                $query->whereNull('end_at')->orWhere('end_at', '>', Carbon::now())->orWhere(function ($query) {
                    $query->where('end_at', '<=', Carbon::now());
                });
            });
    }

    /**
     * Scope a query to sort raids in alphabetical order.
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
     * Scope a query to sort features by newest first.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query, $reverse = false) {
        return $query->orderBy('id', $reverse ? 'ASC' : 'DESC');
    }

    /**
     * Scope a query to only include posts that are scheduled to be posted and are ready to post.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShouldBeVisible($query) {
        return $query->whereNotNull('start_at')->where('start_at', '<', Carbon::now())->where('is_visible', 0);
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
        return '<a href="'.$this->idUrl.'" class="display-raid">'.$this->name.'</a>';
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/raids';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute() {
        return $this->background_hash.$this->id.'-image.'.($this->background_extension ?? 'png');
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
        if (!$this->has_background) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->imageFileName);
    }

    /**
     * Gets the URL of the model's encyclopedia page.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('raids/raid-index?name='.$this->name);
    }

    /**
     * Gets the URL of the individual raid's page, by ID.
     *
     * @return string
     */
    public function getIdUrlAttribute() {
        return url('raids/'.$this->id);
    }

    /**
     * Gets the raid's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute() {
        return 'raids';
    }

    /**
     * Gets the admin edit URL.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/data/raids/edit/'.$this->id);
    }

    /**
     * Gets the power required to edit this model.
     *
     * @return string
     */
    public function getAdminPowerAttribute() {
        return 'edit_raids';
    }

    /**
     * Gets whether or not the raid is currently active.
     *
     * @return string
     */
    public function getIsActiveAttribute() {
        if ($this->start_at && ($this->start_at > Carbon::now())) {
            return false;
        }
        if ($this->end_at && ($this->end_at < Carbon::now())) {
            return false;
        }

        return true;
    }

    /**
     * Gets the damage array from the raid's data.
     *
     * @return array
     */
    public function getDamageAttribute() {
        if (!$this->data || !isset($this->data['damage'])) {
            return null;
        }

        return $this->data['damage'];
    }

    /**
     * Calculates the damage dealt.
     *
     * @return int
     */
    public function getDamageDealtAttribute() {
        if (!$this->data || !isset($this->data['damage']['base'])) {
            return 0;
        }
        $damageData = $this->data['damage'];
        if (!isset($damageData['max']) || isset($damageData['max']) && ($damageData['base'] == $damageData['max'])) {
            return $damageData['base'];
        }
        if (isset($damageData['max']) && ($damageData['base'] > $damageData['max'])) {
            $roll = rand($damageData['max'], $damageData['base']);
        } else {
            $roll = rand($damageData['base'], $damageData['max']);
        }

        return $roll;
    }

    /**
     * Get the required asset to make an attack on this raid.
     *
     * @return array
     */
    public function attackAsset() {
        if ($this->data && isset($this->data['damage'])) {
            $damageData = $this->damage;
            $attack = [];
            switch ($damageData['type']) {
                case 'Item':
                    $class = getAssetModelString('items');
                    $asset = $class::find($damageData['id']);
                    if (!$asset) {
                        break;
                    }

                    $attack['items'] = [
                        'asset' => $asset,
                        'quantity' => $class::find($damageData['quantity']),
                    ];
                    break;
                case 'Currency':
                    $class = getAssetModelString('currencies');
                    $asset = $class::find($damageData['id']);
                    if (!$asset) {
                        break;
                    }

                    $attack['currencies'] = [
                        'asset' => $asset,
                        'quantity' => $class::find($damageData['quantity']),
                    ];
                    break;
            }
            if (!count($attack)) {
                return null;
            }

            return $attack;
        }

        return null;
    }

    /**
     * Gets the current boss of raid.
     *
     * @return mixed
     */
    public function currentBoss() {
        if (!$this->bosses || !$this->bosses->count()) {
            return null;
        } elseif ($this->bosses->count() == 1) {
            return $this->bosses->first();
        }

        return null;
    }
}
