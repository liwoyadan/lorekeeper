<?php

namespace App\Models\Character;

use App\Models\Model;
use App\Models\User\User;
use App\Models\User\UserItem;

class RelationsLog extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_1_id', 'character_2_id', 'sender_id', 'recipient_id', 'relation_id', 'stack_id', 'log', 'log_type', 'data',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'relations_log';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who initiated the logged action.
     */
    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the user who received the logged action.
     */
    public function recipient() {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Get the character of ID 1.
     */
    public function characterOne() {
        return $this->belongsTo(Character::class, 'character_1_id');
    }

    /**
     * Get the character of ID 2.
     */
    public function characterTwo() {
        return $this->belongsTo(Character::class, 'character_2_id');
    }

    /**
     * Get the associated relation.
     */
    public function relation() {
        return $this->belongsTo(CharacterRelation::class, 'relation_id');
    }

    /**
     * Get the user who received the logged action.
     */
    public function userItem() {
        return $this->belongsTo(UserItem::class, 'stack_id');
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/
}
