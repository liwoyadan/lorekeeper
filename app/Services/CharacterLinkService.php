<?php

namespace App\Services;

use App\Facades\Notifications;
use App\Models\Character\Character;
use App\Models\Character\CharacterRelation;
use App\Models\Item\Item;
use App\Models\User\User;
use App\Models\User\UserItem;
use App\Services\InventoryManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CharacterLinkService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Character Link Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of character links.
    |
    */

    /**
     * @param mixed $character
     * @param mixed $slugs
     * @param mixed $user
     */
    public function createCharacterRelationLinks($character, $data, $user, $isStaff = false) {
        DB::beginTransaction();

        try {
            if (!isset($data['slug'])) {
                throw new \Exception('No character selected.');
            }
            if (!isset($data['link_item_id']) && !$isStaff) {
                throw new \Exception('You must select an item in order to establish a link.');
            }

            $otherCharacter = Character::where('slug', $data['slug'])->first();
            if (!$otherCharacter) {
                throw new \Exception('Character not found.');
            }
            if (!$otherCharacter->is_links_open && ($user->id != $otherCharacter->user_id)) {
                throw new \Exception('The character you are attempting to establish a link with has their link requests closed.');
            }

            // check if there is an existing link, the lower id is always character_1_id
            $lowerId = $character->id < $otherCharacter->id ? $character->id : $otherCharacter->id;
            $higherId = $character->id < $otherCharacter->id ? $otherCharacter->id : $character->id;
            if (CharacterRelation::where('character_1_id', $lowerId)->where('character_2_id', $higherId)->exists()) {
                throw new \Exception('A relation already exists between one or more of these characters.');
            }

            if (!$isStaff) {
                $linkItem = UserItem::where('id', $data['link_item_id'])->where('user_id', $user->id)->where('count', '>', 0)->first();
                if (!$linkItem) {
                    throw new \Exception('Item to establish link with not found.');
                }

                $invManager = new InventoryManager;
                if (!$invManager->debitStack($user, 'Link Request', ['data' => 'Used to request a link between '.$character->displayName.' and '.$otherCharacter->displayName.' (by '.$user->displayName.')'], $linkItem, 1)) {
                    throw new \Exception('The selected item for this link was unable to be debited from your inventory.');
                }

                if ($user->id == $otherCharacter->user_id) {
                    $relation = CharacterRelation::create([
                        'character_1_id' => $lowerId,
                        'character_2_id' => $higherId,
                        'status'         => 'Approved',
                        'user_item_id'   => $linkItem->id,
                    ]);

                    $type = 'Link Established';
                    $data = 'Link established between '.$character->displayName.' and '.$otherCharacter->displayName;
                    if ($relation && !$this->createLog($character->id, $otherCharacter->id, $user->id, $user->id, $relation->id, $linkItem->id, $type, $data)) {
                        throw new \Exception('Failed to create relation log.');
                    }
                } else {
                    $relation = CharacterRelation::create([
                        'character_1_id' => $lowerId,
                        'character_2_id' => $higherId,
                        'user_item_id'   => $linkItem->id,
                    ]);

                    $type = 'Link Requested';
                    $data = 'Link requested between '.$character->displayName.' and '.$otherCharacter->displayName;
                    if ($relation && !$this->createLog($character->id, $otherCharacter->id, $user->id, $otherCharacter->user_id, $relation->id, $linkItem->id, $type, $data)) {
                        throw new \Exception('Failed to create relation log.');
                    }

                    // notify the other user
                    Notifications::create('LINK_REQUESTED', $otherCharacter->user, [
                        'character' => $character->displayName,
                        'requested' => $otherCharacter->displayName,
                        'character_slug' => $otherCharacter->slug,
                        'user'      => $user->displayName,
                        'id'        => $relation->id,
                    ]);
                }
            } else {
                $relation = CharacterRelation::create([
                    'character_1_id' => $lowerId,
                    'character_2_id' => $higherId,
                    'status'         => 'Approved',
                    'user_item_id'   => null,
                ]);

                $type = 'Link Established';
                $data = 'Link established between '.$character->displayName.' and '.$otherCharacter->displayName.' (Staff Action)';
                if ($relation && !$this->createLog($character->id, $otherCharacter->id, $user->id, null, $relation->id, null, $type, $data)) {
                    throw new \Exception('Failed to create relation log.');
                }
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Accepts or rejects a link request.
     *
     * @param mixed $id
     * @param mixed $action
     */
    public function handleCharacterRelationLink($id, $action, $user) {
        DB::beginTransaction();

        try {
            if (!$user) {
                throw new \Exception('You must be logged in to handle a link request.');
            }

            $link = CharacterRelation::find($id);
            if (!$link) {
                throw new \Exception('Link not found.');
            }
            if ($link->status != 'Pending') {
                throw new \Exception('This link\'s status is not pending.');
            }

            if ($action != 'accept' && $action != 'reject') {
                throw new \Exception('Invalid action specified.');
            }

            if ($action == 'accept') {
                $link->status = 'Approved';
                $link->save();

                $otherCharacter = ($link->characterOne->user_id == $user->id) ? $link->characterTwo : $link->characterOne;
                $character = $link->getOtherCharacter($otherCharacter->id);
                Notifications::create('LINK_ACCEPTED', $otherCharacter->user, [
                    'link'      => $user->url,
                    'user'      => $user->name,
                    'requested' => $otherCharacter->displayName,
                    'character' => $otherCharacter->url,
                ]);

                $type = 'Link Established';
                $data = 'Link request approved between '.$character->displayName.' and '.$otherCharacter->displayName;
                if (!$this->createLog($link->character_1_id, $link->character_2_id, $user->id, $otherCharacter->user->id, $link->id, $link->user_item_id, $type, $data)) {
                    throw new \Exception('Failed to create relation log.');
                }
            } else {
                $link->status = 'Rejected';
                $link->save();

                $otherCharacter = ($link->characterOne->user_id == $user->id) ? $link->characterTwo : $link->characterOne;
                $character = $link->getOtherCharacter($otherCharacter->id);
                Notifications::create('LINK_REJECTED', $otherCharacter->user, [
                    'link'      => $user->url,
                    'user'      => $user->name,
                    'requested' => $otherCharacter->displayName,
                    'character' => $otherCharacter->url,
                ]);

                $invManager = new InventoryManager;
                $item = Item::where('id', $link->userItem->item_id)->first();
                if (!$invManager->creditItem($character->user, $otherCharacter->user, 'Link Rejection Refund', ['data' => 'Refunded from a rejected link request.'], $item, 1)) {
                    throw new \Exception('Failed to refund the link item back to the user.');
                }

                $type = 'Link Rejected';
                $data = 'Link request rejected between '.$character->displayName.' and '.$otherCharacter->displayName;
                if (!$this->createLog($link->character_1_id, $link->character_2_id, $user->id, $otherCharacter->user->id, $link->id, $link->user_item_id, $type, $data)) {
                    throw new \Exception('Failed to create relation log.');
                }
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a link link.
     *
     * @param mixed $id
     */
    public function deleteCharacterRelationLink($id) {
        DB::beginTransaction();

        try {
            $link = CharacterRelation::find($id);

            if (!$link) {
                throw new \Exception('Link not found.');
            }

            $link->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     *  this is when a user changes the link type.
     *
     * @param mixed $data
     * @param mixed $id
     * @param mixed $user
     */
    public function updateCharacterRelationLinkInfo($data, $id, $user) {
        DB::beginTransaction();

        try {
            $link = CharacterRelation::find($id);

            if (!$link) {
                throw new \Exception('Link not found.');
            }

            $character = Character::where('slug', $data['slug'])->first();
            if (!$character) {
                throw new \Exception('Character not found.');
            }

            if ($character->id == $link->character_1_id) {
                $link->info = [$data['info'], $link->info ? $link->info[1] : ''];
            } else {
                $link->info = [$link->info ? $link->info[0] : '', $data['info']];
            }

            if (isset($data['type'])) {
                $link->type = $data['type'];
            }

            $link->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Nulls all active relationships when a character is transferred.
     *
     * @param mixed $character
     *
     * @return bool
     */
    public function clearRelations($character) {
        DB::beginTransaction();

        try {
            if (!$character) {
                throw new \Exception('Character not found.');
            }

            $firstRelations = CharacterRelation::where('character_1_id', $character->id)->whereNull('deleted_at');
            $relations = CharacterRelation::where('character_2_id', $character->id)->whereNull('deleted_at')->union($firstRelations)->get();
            if ($relations->count() > 0) {
                foreach ($relations as $link) {
                    $link->deleted_at = Carbon::now();
                    $link->save();

                    $data = 'Relationship between '.$link->characterOne->fullName.' and '.$link->characterTwo->fullName.' nulled in the process of character transfer.';
                    if (!$this->createLog($link->character_1_id, $link->character_2_id, Auth::user()->id, null, $link->id, null, 'Relationship Nulled', $data)) {
                        throw new \Exception('Failed to create relation log.');
                    }
                }
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Creates a link log.
     *
     * @param int    $characterOneId
     * @param int    $characterTwoId
     * @param int    $senderId
     * @param int    $recipientId
     * @param int    $relationId
     * @param int    $stackId
     * @param string $type
     * @param string $data
     *
     * @return int
     */
    public function createLog($characterOneId, $characterTwoId, $senderId, $recipientId, $relationId, $stackId, $type, $data) {
        return DB::table('relations_log')->insert(
            [
                'character_1_id'      => $characterOneId,
                'character_2_id'    => $characterTwoId,
                'sender_id'   => $senderId,
                'recipient_id'   => $recipientId ?? null,
                'relation_id' => $relationId,
                'stack_id'       => $stackId ?? null,
                'log'            => $type.($data ? ' ('.$data.')' : ''),
                'log_type'       => $type,
                'data'           => $data,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]
        );
    }
}
