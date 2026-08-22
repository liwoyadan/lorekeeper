<?php

namespace App\Models\Housing;

use App\Models\Model;

class HousingPattern extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'has_image', 'hash', 'is_visible',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'housing_patterns';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'  => 'required|unique:housing_patterns|between:3,100',
        'image' => 'mimes:png',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'  => 'required|between:3,100',
        'image' => 'mimes:png',
    ];

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the file directory containing the model's image.
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/housing-patterns';
    }

    /**
     * Gets the file name of the model's image.
     */
    public function getPatternImageFileNameAttribute() {
        return $this->hash.$this->id.'-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     */
    public function getPatternImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     */
    public function getPatternImageUrlAttribute() {
        if (!$this->has_image) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->patternImageFileName);
    }

    /**
     * Gets the admin edit URL.
     */
    public function getAdminUrlAttribute() {
        return url('admin/data/housing-patterns/edit/'.$this->id);
    }

    /**
     * Gets the power required to edit this model.
     */
    public function getAdminPowerAttribute() {
        return 'edit_data';
    }
}
