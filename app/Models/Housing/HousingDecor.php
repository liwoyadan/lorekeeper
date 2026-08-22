<?php

namespace App\Models\Housing;

use App\Models\Item\Item;
use App\Models\Item\ItemTag;
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
        'svg_file'      => 'nullable|mimetypes:image/svg+xml,text/xml,text/plain,application/xml',
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
        'svg_file'      => 'nullable|mimetypes:image/svg+xml,text/xml,text/plain,application/xml',
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

    /**
     * Items whose Housing decor tag grants this decor.
     */
    public function grantingItems() {
        $itemIds = ItemTag::active()->type('decor')->get()
            ->filter(function ($tag) {
                return ($tag->data['decor_id'] ?? null) == $this->id;
            })
            ->pluck('item_id')->all();

        return Item::without('tags')->whereIn('id', $itemIds);
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to decor visible to the given user (staff see all).
     *
     * @param mixed      $query
     * @param mixed|null $user
     */
    public function scopeVisible($query, $user = null) {
        if ($user && $user->hasPower('edit_data')) {
            return $query;
        }

        return $query->where('is_visible', 1);
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the file directory containing the model's image.
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/housing';
    }

    /**
     * Gets the file name of the model's image.
     */
    public function getDecorImageFileNameAttribute() {
        return $this->hash.$this->id.'-image.png';
    }

    /**
     * Gets the path to the file directory containing the model's image.
     */
    public function getDecorImagePathAttribute() {
        return public_path($this->imageDirectory);
    }

    /**
     * Gets the URL of the model's image.
     */
    public function getDecorImageUrlAttribute() {
        if (!$this->has_image) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->decorImageFileName);
    }

    /**
     * Gets the file name of the model's SVG art (svg render mode).
     */
    public function getDecorSvgFileNameAttribute() {
        return $this->hash.$this->id.'.svg';
    }

    /**
     * Gets the URL of the model's SVG art.
     */
    public function getDecorSvgUrlAttribute() {
        if (!$this->has_image || !$this->isSvg) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->decorSvgFileName);
    }

    /**
     * Gets the raw SVG markup for inlining (svg render mode), or '' when absent.
     */
    public function getSvgContentsAttribute() {
        $path = $this->decorImagePath.'/'.$this->decorSvgFileName;
        if (!$this->isSvg || !$this->has_image || !file_exists($path)) {
            return '';
        }

        return file_get_contents($path);
    }

    /**
     * Gets the file name of the decor's active art for its render mode.
     */
    public function getDecorArtFileNameAttribute() {
        return $this->isSvg ? $this->decorSvgFileName : $this->decorImageFileName;
    }

    /**
     * Whether the decor renders as inline SVG rather than a masked image.
     */
    public function getIsSvgAttribute() {
        return $this->render_mode == 'svg';
    }

    /**
     * Gets the human-readable label for the decor's kind.
     */
    public function getKindLabelAttribute() {
        return config('lorekeeper.housing.kinds')[$this->kind] ?? $this->kind;
    }

    /**
     * Gets the human-readable label for the decor's layer.
     */
    public function getLayerLabelAttribute() {
        if (!$this->layer) {
            return null;
        }

        return config('lorekeeper.housing.layers')[$this->layer] ?? $this->layer;
    }

    /**
     * Gets the admin edit URL.
     */
    public function getAdminUrlAttribute() {
        return url('admin/data/housing/edit/'.$this->id);
    }

    /**
     * Gets the power required to edit this model.
     */
    public function getAdminPowerAttribute() {
        return 'edit_data';
    }
}
