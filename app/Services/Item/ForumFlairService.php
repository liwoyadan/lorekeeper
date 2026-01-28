<?php

namespace App\Services\Item;

use App\Models\Forum\ForumFlair;
use App\Services\InventoryManager;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class ForumFlairService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Forum Flair Service
    |--------------------------------------------------------------------------
    |
    | Handles the forum flair item tag.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData() {
        return [
            'flairs' => ForumFlair::default(0)->isStaff(0)->sortAlphabetical()->pluck('name', 'id')->toArray(),
            'grantTypes' => ['Choice' => 'User Choice', 'Random' => 'Randomized from Choices', 'All' => 'All Selected Flairs'],
        ];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param object $tag
     *
     * @return mixed
     */
    public function getTagData($tag) {
        return [
            'type'      => $tag->data['grant_type'] ?? null,
            'flair_ids' => $tag->data['flair_ids'] ?? [],
            'flairs'    => ForumFlair::visible()->default(0)->isStaff(0)->whereIn('id', $tag->data['flair_ids'] ?? [])->sortAlphabetical()->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Processes the data attribute of the tag and returns it in the preferred format.
     *
     * @param object $tag
     * @param array  $data
     *
     * @return bool
     */
    public function updateData($tag, $data) {
        DB::beginTransaction();

        try {
            $tag->update(['data' => Arr::only($data, ['grant_type', 'flair_ids'])]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Acts upon the item when used from the inventory.
     *
     * @param \App\Models\User\UserItem $stacks
     * @param \App\Models\User\User     $user
     * @param array                     $data
     *
     * @return bool
     */
    public function act($stacks, $user, $data) {
        DB::beginTransaction();

        try {
            $firstInstance = $stacks->first()->item->tag('forum_flair')->getData();
            if (!isset($firstInstance['flair_ids']) || !count($firstInstance['flair_ids'])) {
                throw new \Exception('This item has no forum flairs it can grant.');
            }
            $flairPool = ForumFlair::visible()->default(0)->isStaff(0)->whereIn('id', $tag->data['flair_ids'] ?? [])->whereNotIn('id', $user->forumFlairs()->pluck('forum_flair_id')->toArray());
            if (!$flairPool->count()) {
                throw new \Exception('No redeemable forum flairs via this item were found.');
            }

            if (isset($firstInstance['type']) && $firstInstance['type'] == 'Choice') {
                $flairPool = $flairPool->where('id', $data['redeem_flair_id'])->first();

                if (!$flairPool) {
                    throw new \Exception('The selected forum flair is invalid or already unlocked.');
                }
            } elseif (isset($firstInstance['type'])) {
                if ((isset($firstInstance['type']) && $firstInstance['type'] == 'All') && array_sum($data['quantities']) > 1) {
                    throw new \Exception('As this item redeems all forum flairs associated with it on single use, you cannot redeem more than one.');
                } elseif ($flairPool->count() < array_sum($data['quantities'])) {
                    throw new \Exception('You have selected a quantity too high for the amount of forum flairs you are able to unlock with this item.');
                }
            } else {
                throw new \Exception('This forum flair item has not been configured properly.');
            }

            foreach ($stacks as $key=> $stack) {
                if ($stack->user_id != $user->id) {
                    throw new \Exception('This item does not belong to you.');
                }

                if ((new InventoryManager)->debitStack($stack->user, 'Forum Flair Redeemed', ['data' => ''], $stack, $data['quantities'][$key])) {
                    for ($q = 0; $q < $data['quantities'][$key]; $q++) {
                        if (!$rewards = fillUserAssets(parseAssetData($stack->item->tag('forum_flair')->data), $user, $user, 'Forum Flair Redeemed', [
                            'data' => 'Redeemed forum flair from '.$stack->item->name,
                        ])) {
                            throw new \Exception('Failed to redeem forum flair.');
                        }

                        flash($this->getFlairRewardsString($rewards))->success();
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
     * Acts upon the item when used from the inventory.
     *
     * @param array $rewards
     *
     * @return string
     */
    private function getFlairRewardsString($rewards) {
        return 'You have received the forum flair(s): '.createRewardsString($rewards);
    }
}
