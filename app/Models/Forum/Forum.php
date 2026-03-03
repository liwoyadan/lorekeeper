<?php

namespace App\Models\Forum;

use App\Models\Model;
use App\Models\Comment\Comment;
use App\Models\Rank\Rank;
use App\Traits\Commentable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Forum extends Model {
    use Commentable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'parsed_description', 'is_locked', 'staff_only', 'role_limit', 'parent_id', 'sort', 'is_active',
        'has_image', 'hash', 'extension', 'has_icon', 'icon_hash', 'icon_extension', 'color', 'characters_enabled',
        'forum_rules', 'forum_styles',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'forum_rules' => 'array',
        'forum_styles' => 'array',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forums';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**
     * Validation rules for creation.
     *
     * @var array
     */
    public static $createRules = [
        'name'              => 'required|unique:forums|between:2,100',
        'description'       => 'nullable',
        'image'             => 'nullable|mimes:png,gif,webp|max:2048',
        'icon'             => 'nullable|mimes:png,gif,webp|max:2048',
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
        'icon'             => 'nullable|mimes:png,gif,webp|max:2048',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the parent of this forum.
     */
    public function parent() {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get the children of this forum.
     */
    public function children() {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the children of this forum.
     */
    public function role() {
        return $this->belongsTo(Rank::class, 'role_limit');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope only forums with no parent_id.
     *
     * @param mixed $query
     */
    public function scopeCategory($query) {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope forums that are staff only.
     *
     * @param mixed $query
     * @param mixed $only
     */
    public function scopeStaff($query, $user = null) {
        if ($user && $user->isStaff) {
            return $query;
        }

        return $query->where('staff_only', 0);
    }

    /**
     * Scope forums are locked for new posts/comments.
     *
     * @param mixed $query
     * @param mixed $state
     */
    public function scopeLocked($query, $state = 1) {
        return $query->where('is_locked', $state);
    }

    /**
     * Scope forums are locked for new posts/comments.
     *
     * @param mixed $query
     * @param mixed $state
     */
    public function scopeVisible($query, $user = null) {
        if ($user && $user->hasPower('manage_forums')) {
            return $query;
        } else {
            return $query->where('is_active', 1);
        }
    }

    /**
     * Scope forums that have children.
     *
     * @param mixed $query
     * @param boolean $children
     */
    public function scopeHasChildren($query, $children = true) {
        if ($children) {
            return $query->has('children');
        } else {
            return $query;
        }
    }

    /**
     * Scope forums that have children.
     *
     * @param mixed $query
     * @param boolean $children
     */
    public function scopeCanAccess($query, $user = null) {
        if ($user && $user->hasPower('manage_forums')) {
            return $query;
        } else {
            return $query->whereNull('role_limit')->orWhere('role_limit', $user->rank_id);
        }
    }

    /**********************************************************************************************

        ATTRIBUTES

    **********************************************************************************************/

    /**
     * Gets the forum url.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('forum/'.$this->id);
    }

    /**
     * Displays the forum's name, linked to its page.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        $icon = [];
        if ($this->is_locked) {
            $lockIcon = '<i class="fas fa-lock mr-1" data-toggle="tooltip" title="This forum is locked.';
            if (Auth::check() && Auth::user()->hasPower('manage_forums')) {
                $lockIcon .= ' You may access it as a staff member able to manage forums."></i>';
            } else {
                $lockIcon .= '"></i>';
            }

            $icon[] = $lockIcon;
        }
        if ($this->staff_only) {
            $icon[] = '<i class="fas fa-crown mr-1" data-toggle="tooltip" title="Staff-only Forum."></i>';
        }
        if ($this->role) {
            $icon[] = '<i class="fas fa-star mr-1" data-toggle="tooltip" title="'.$this->role->name.'-only Forum."></i>';
        }
        $icon = (isset($icon) ? implode('', $icon) : '');

        if ($this->is_locked) {
            return '<a href="'.$this->url.'" class="display-forum text-muted">'.$icon.$this->name.'</a>';
        } else {
            return '<a href="'.$this->url.'" class="display-forum">'.$icon.$this->name.'</a>';
        }
    }

    /**
     * Determines if Forum has any restrictions.
     *
     * @return string
     */
    public function getHasRestrictionsAttribute() {
        if ($this->is_locked || $this->staff_only || $this->role) {
            return true;
        } else {
            return false;
        }
    }

    public function getAccessibleSubforumsAttribute() {
        $children = collect();
        if ($this->children) {
            foreach ($this->children as $child) {
                if (!$child->hasRestrictions || Auth::check() && Auth::user()->canVisitForum($child->id)) {
                    $children->push($child);
                }
            }
        }

        return $children;
    }

    /**
     * Gets the file directory containing the model's image.
     *
     * @return string
     */
    public function getImageDirectoryAttribute() {
        return 'images/data/forums';
    }

    /**
     * Gets the file name of the model's image.
     *
     * @return string
     */
    public function getImageFileNameAttribute() {
        return $this->id.$this->hash.'-image.'.$this->extension;
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
     * Gets the file name of the model's icon.
     *
     * @return string
     */
    public function getIconFileNameAttribute() {
        return $this->id.$this->icon_hash.'-icon.'.$this->icon_extension;
    }

    /**
     * Gets the URL of the model's icon.
     *
     * @return string
     */
    public function getIconUrlAttribute() {
        if (!$this->has_icon) {
            return null;
        }

        return asset($this->imageDirectory.'/'.$this->iconFileName);
    }

    /**
     * Gets all forum rules for the given forum,
     * including any rules parents have.
     *
     * @return array
     */
    public function getAllRulesAttribute() {
        if ((!$this->parent || $this->parent && (isset($this->parent->forum_rules) && !count($this->parent->forum_rules))) && !isset($this->forum_rules)) {
            return [];
        }

        $ruleSets = [];
        if ($this->parent && (isset($this->parent->forum_rules) && count($this->parent->forum_rules))) {
            $parentForum = $this->parent;
            if ($parentForum->parent && (isset($parentForum->parent->forum_rules) && $parentForum->parent->forum_rules)) {
                $parentParentForum = $parentForum->parent;
                $ruleSets['parents'][$parentParentForum->id]['name'] = $parentParentForum->name;
                $ruleSets['parents'][$parentParentForum->id]['rules'] = $parentParentForum->forum_rules;
            }

            $ruleSets['parents'][$parentForum->id]['name'] = $parentForum->name;
            $ruleSets['parents'][$parentForum->id]['rules'] = $parentForum->forum_rules;
        }

        if (isset($this->forum_rules) && count($this->forum_rules)) {
            $ruleSets['current'] = $this->forum_rules;
        }

        return $ruleSets;
    }

    /**
     * Gets the URL of the model's image.
     *
     * @return string
     */
    public function getCommentsAttribute() {
        return Comment::where('commentable_type', 'App\Models\Forum\Forum')->where('commentable_id', $this->id)->get();
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Checks if a board is locked.
     *
     * @param mixed|null $board
     */
    public function canUsersPost($board = null) {
        if ($board == null) {
            $board = $this;
        }
        if ($board->is_locked) {
            return false;
        } elseif (isset($board->parent_id)) {
            if (!$board->canUsersPost($board->parent)) {
                return false;
            }
        }

        return true;
    }
    
    /**
     * Display's the forum's icon.
     *
     * @return string
     */
    public function displayIcon($sizeLimit = null) {
        if (!$this->iconUrl) {
            return null;
        }
        $styleString = $sizeLimit ? ' style="max-width: '.$sizeLimit.'px;"' : '';

        return '<img src="'.$this->iconUrl.'" alt="'.$this->name.'\'s Icon" class="forum-icon"'.$styleString.'>';
    }
}
