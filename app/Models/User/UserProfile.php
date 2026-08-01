<?php

namespace App\Models\User;

use App\Models\Forum\ForumDecor;
use App\Models\Forum\ForumFlair;
use App\Models\Model;
use App\Traits\Commentable;

class UserProfile extends Model {
    use Commentable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'parsed_text', 'forum_signature', 'parsed_forum_signature', 'forum_flair_id', 'forum_decor', 'forum_bg_hash', 'forum_bg_extension', 'forum_bg_opacity',
    ];

    protected $casts = [
        'forum_decor' => 'array',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_profiles';

    /**
     * The primary key of the model.
     *
     * @var string
     */
    public $primaryKey = 'user_id';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user this profile belongs to.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user's set forum flair.
     */
    public function forumFlair() {
        return $this->belongsTo(ForumFlair::class, 'forum_flair_id');
    }

    /**********************************************************************************************

        ATTRIBUTES

    **********************************************************************************************/

    /**
     * Gets the directory for forum background images.
     */
    public function getForumBgDirectoryAttribute() {
        return 'images/data/forum-bgs';
    }

    /**
     * Gets the file name of the user's forum background image.
     */
    public function getForumBgFileNameAttribute() {
        return $this->user_id.$this->forum_bg_hash.'.'.($this->forum_bg_extension ?? 'png');
    }

    /**
     * Gets the URL of the user's forum background image.
     */
    public function getForumBgUrlAttribute() {
        if (!$this->forum_bg_hash) {
            return null;
        }

        return asset($this->forumBgDirectory.'/'.$this->forumBgFileName);
    }

    /**
     * Gets the inline CSS style string for the user's uploaded forum background.
     */
    public function getForumBgCssStyleAttribute() {
        if (!$this->forumBgUrl) {
            return null;
        }

        $default = config('lorekeeper.forums.user_uploads.background.default_opacity', 15);
        $max = config('lorekeeper.forums.user_uploads.background.max_opacity', 100);
        $opacity = min($max, $this->forum_bg_opacity ?? $default);

        return "background-image: url('{$this->forumBgUrl}'); opacity: {$opacity}%";
    }

    /**
     * Get the user's active forum decor of a given type.
     *
     * @param mixed|null $type
     */
    public function forumDecorOf($type = null) {
        if (!$type) {
            return null;
        }

        $id = $this->forum_decor[$type] ?? null;

        return $id ? ForumDecor::find($id) : null;
    }
}
