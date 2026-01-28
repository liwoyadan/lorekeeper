<?php

namespace App\Models\User;

use App\Models\Forum\ForumFlair;
use App\Models\Model;

class UserForumFlair extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'forum_flair_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_forum_flairs';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who owns the currency.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the forum flair associated with this record.
     */
    public function flair() {
        return $this->belongsTo(ForumFlair::class, 'forum_flair_id');
    }
}
