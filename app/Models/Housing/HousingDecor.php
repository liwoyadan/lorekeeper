<?php

namespace App\Models\Housing;

use App\Models\Model;

class HousingDecor extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'sort', 'kind', 'render_mode', 'layer',
        'description', 'parsed_description', 'default_scale',
        'has_image', 'hash', 'is_visible',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'housing_decors';

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'          => 'required|unique:housing_decors|between:3,100',
        'kind'          => 'required',
        'render_mode'   => 'required',
        'layer'         => 'nullable',
        'default_scale' => 'nullable|numeric|min:0.1|max:10',
        'description'   => 'nullable',
        'image'         => 'mimes:png',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'          => 'required|between:3,100',
        'kind'          => 'required',
        'render_mode'   => 'required',
        'layer'         => 'nullable',
        'default_scale' => 'nullable|numeric|min:0.1|max:10',
        'description'   => 'nullable',
        'image'         => 'mimes:png',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the zones belonging to this decor.
     */
    public function zones() {
        return $this->hasMany(HousingZone::class, 'decor_id')->orderBy('sort');
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
        return 'images/data/housing';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getDecorImageFileNameAttribute() {
        return $this->hash.$this->id.'-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     *
     * @return string
     */
    public function getDecorImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getDecorImageUrlAttribute() {
        if (!$this->has_image) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->decorImageFileName);
    }

    /**
     * Gets the human-readable label for the decor's kind.
     *
     * @return string
     */
    public function getKindLabelAttribute() {
        return config('lorekeeper.housing.kinds')[$this->kind] ?? $this->kind;
    }

    /**
     * Gets the human-readable label for the decor's layer.
     *
     * @return string
     */
    public function getLayerLabelAttribute() {
        if (!$this->layer) {
            return null;
        }

        return config('lorekeeper.housing.layers')[$this->layer] ?? $this->layer;
    }

    /**
     * Gets the admin edit URL.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/data/housing/edit/'.$this->id);
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
