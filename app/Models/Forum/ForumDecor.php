<?php

namespace App\Models\Forum;

use App\Models\Model;
use Illuminate\Support\Facades\Auth;

class ForumDecor extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'description', 'parsed_description', 'data',
        'has_image', 'extension', 'hash', 'staff_only', 'is_default', 'is_visible',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forum_decors';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'              => 'required|unique:forum_decors|between:2,100',
        'description'       => 'nullable',
        'image'             => 'nullable|mimes:png,gif,jpeg,jpg,webp|max:2048',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'              => 'required|between:2,100',
        'description'       => 'nullable',
        'image'             => 'mimes:png,gif,jpeg,jpg,webp|max:2048',
    ];

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to sort forum flairs in alphabetical order.
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
     * Scope a query to sort forum flairs by newest first or reverse.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortNewest($query, $reverse = false) {
        return $query->orderBy('id', $reverse ? 'ASC' : 'DESC');
    }

    /**
     * Scope a query to show only visible forum flairs.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed|null                            $user
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query, $user = null) {
        if ($user && $user->hasPower('edit_data')) {
            return $query;
        }

        return $query->where('is_visible', 1);
    }

    /**
     * Scope forum flairs that are/aren't default.
     *
     * @param mixed $query
     * @param mixed $isDefault
     */
    public function scopeDefault($query, $isDefault = true) {
        return $query->where('is_default', $isDefault);
    }

    /**
     * Scope forum flairs that are/aren't staff only.
     *
     * @param mixed $query
     * @param boolean $isStaff
     */
    public function scopeIsStaff($query, $isStaff = true) {
        return $query->where('staff_only', $isStaff);
    }

    /**********************************************************************************************

        ATTRIBUTES

    **********************************************************************************************/

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/forums-decors';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute() {
        return $this->id.$this->hash.'-image.'.($this->extension ?? 'png');
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

    /**
     * Gets the name and type of the decor.
     *
     * @return string
     */
    public function getFullNameAttribute() {
        return $this->name.' ('.ucwords($this->type).')';
    }

    /**
     * Gets the decor's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute() {
        return 'forum_decors';
    }

    /**
     * Gets the inline CSS style string for this decor's data properties.
     *
     * @return string|null
     */
    public function getCssStyleAttribute() {
        if (!$this->imageUrl) {
            return null;
        }

        $imageUrl = 'url('.$this->imageUrl.')';
    
        if ($this->type == 'border') {
            $styles = ['border-image-source' => $imageUrl];
            foreach ($this->data ?? [] as $key => $value) {
                if ($value != null) {
                    $styles[str_replace('_', '-', $key)] = $value;
                }
            }
        } elseif ($this->type == 'background') {
            $styles = ['background-image' => $imageUrl];
            foreach ($this->data ?? [] as $key => $value) {
                if ($key == 'opacity') {
                    $styles[str_replace('_', '-', $key)] = $value.'%';
                } elseif ($key == 'background_repeat') {
                    $styles[str_replace('_', '-', $key)] = $value ? 'repeat' : 'no-repeat';
                } else {
                    $styles[str_replace('_', '-', $key)] = $value;
                }
            }
        }

        return implode('; ', array_map(fn($property, $value) => $property.': '.$value, array_keys($styles), $styles));
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/
}
