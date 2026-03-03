<?php

namespace App\Models\User;

use App\Models\Model;
use App\Models\Forum\ForumFlair;
use App\Traits\Commentable;

class UserProfile extends Model {
    use Commentable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'text', 'parsed_text', 'forum_signature', 'parsed_forum_signature', 'forum_flair_id', 'forum_decor_id', 'forum_decor_hash', 'forum_decor_extension',
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
}
