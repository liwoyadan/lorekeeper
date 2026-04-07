<?php

namespace App\Models\Character;

use App\Models\Model;
use App\Models\User\UserItem;

class CharacterRelation extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'character_1_id', 'character_2_id', 'info', 'character_1_type', 'character_2_type', 'character_1_featured', 'character_2_featured', 'character_1_sort', 'character_2_sort', 'status', 'deleted_at', 'user_item_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'character_relations';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'info'                 => 'array',
        'deleted_at'           => 'datetime',
        'character_1_featured' => 'boolean',
        'character_2_featured' => 'boolean',
    ];

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
     * Get first character.
     */
    public function characterOne() {
        return $this->belongsTo(Character::class, 'character_1_id');
    }

    /**
     * Get second character.
     */
    public function characterTwo() {
        return $this->belongsTo(Character::class, 'character_2_id');
    }

    /**
     * Get the associated logs.
     */
    public function logs() {
        return $this->hasMany(RelationsLog::class, 'relation_id');
    }

    /**
     * Get the associated user item, if any.
     */
    public function userItem() {
        return $this->belongsTo(UserItem::class, 'user_item_id');
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Returns the other character in the relation based on the given character id.
     *
     * @param mixed $id
     */
    public function getOtherCharacter($id) {
        return $this->character_1_id == $id ? $this->characterTwo : $this->characterOne;
    }

    /**
     * Gets the information for the relation based on the given character id.
     *
     * @param mixed $id
     */
    public function getRelationshipInfo($id) {
        return (isset($this->info) && $this->info) ? ($this->info[$id == $this->character_1_id ? 0 : 1] ?? null) : null;
    }

    /**
     * Get the character in the relation belonging to the given user id, if any.
     *
     * @param mixed $id
     */
    public function getCharacterForUser($id) {
        return $this->characterOne->user_id == $id ? $this->characterOne : ($this->characterTwo->user_id == $id ? $this->characterTwo : null);
    }

    /**
     * Get the type of relation based on the given character id.
     *
     * @param mixed $id
     */
    public function getRelationType($id) {
        if (!$id) {
            return '???';
        }

        if ($this->character_1_id == $id) {
            return $this->character_1_type;
        } else {
            return $this->character_2_type;
        }
    }

    /**
     * Returns whether this link is featured for the given character ID.
     *
     * @param int $id
     */
    public function isFeaturedForCharacter($id) {
        return $this->character_1_id == $id ? $this->character_1_featured : $this->character_2_featured;
    }

    /**
     * Get the first log of this relation, which is the initial request.
     */
    public function initialLog() {
        return $this->logs()->where('log_type', 'Link Requested')->orderBy('created_at', 'ASC')->first();
    }
}
