<?php

namespace App\Models\Forum;

use App\Models\Comment\Comment;
use App\Models\Rank\Rank;
use App\Traits\Commentable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Model;
use Illuminate\Support\Facades\Auth;

class ForumFlair extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'post_requirement', 'description', 'parsed_description', 'color', 'bg_color',
        'data', 'has_image', 'extension', 'hash', 'staff_only', 'is_default', 'is_visible',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forum_flairs';

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
        'name'              => 'required|unique:forum_flairs|between:2,100',
        'description'       => 'nullable',
        'image'             => 'nullable|mimes:png,gif,webp|max:2048',
    ];

    /**
     * Validation rules for updating.
     *
     * @var array
     */
    public static $updateRules = [
        'name'              => 'required|between:2,100',
        'description'       => 'nullable',
        'image'             => 'mimes:png,gif,webp|max:2048',
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
     * Displays the flair itself.
     *
     * @return string
     */
    public function getDisplayFlairAttribute() {
        $html = '<span class="display-flair"';
        if ($this->imageUrl) {
            $icon = '<img src="'.$this->imageUrl.'" alt="'.$this->name.' Icon" class="display-flair-icon">';
        }
        if ($this->inlineStyles && ($this->inlineStyles != '')) {
            $html .= ' style="'.$this->inlineStyles.'"';
        }
        $html .= '>'.($icon ?? '').$this->name.'</span>';

        return $html;
    }

    /**
     * Previews the flair with a given string.
     *
     * @return string
     */
    public function previewFlair($name = 'Username') {
        $html = '<span class="display-flair"';
        if ($this->imageUrl) {
            $icon = '<img src="'.$this->imageUrl.'" alt="'.$this->name.' Icon" class="display-flair-icon">';
        }
        if ($this->inlineStyles && ($this->inlineStyles != '')) {
            $html .= ' style="'.$this->inlineStyles.'"';
        }
        $html .= '>'.($icon ?? '').$name.'</span>';

        return $html;
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/forums-flairs';
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
     * Gets the flair's asset type for asset management.
     *
     * @return string
     */
    public function getAssetTypeAttribute() {
        return 'forum_flairs';
    }

    /**
     * Gets the formatted inline color and bg color CSS.
     *
     * @return string|null
     */
    public function getInlineStylesAttribute() {
        $css = '';

        if ($this->bg_color) {
            $css .= 'background-color: '.$this->bg_color.'!important; ';
        }
        if ($this->color) {
            $css .= 'color: '.$this->color.'!important; ';
        }
        if ($this->textShadowInline) {
            $css .= 'text-shadow: '.$this->textShadowInline.';';
        }

        return $css;
    }

    /**
     * Gets the formatted CSS inline text-shadow values from the data array.
     *
     * @return string|null
     */
    public function getTextShadowInlineAttribute() {
        if (!isset($this->data['text_shadow']) || !is_array($this->data['text_shadow']) || empty($this->data['text_shadow'])) {
            return null;
        }

        $shadows = [];
        foreach ($this->data['text_shadow'] as $shadow) {
            $offsetX = $shadow['offset_x'] ?? '0px';
            $offsetY = $shadow['offset_y'] ?? '0px';
            $blurRadius = $shadow['blur_radius'] ?? '0px';
            $color = $shadow['color'] ?? 'transparent';

            $shadows[] = trim("{$offsetX} {$offsetY} {$blurRadius} {$color}");
        }

        return !empty($shadows) ? implode(', ', $shadows) : null;
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/
}
