<?php

namespace App\Services\Item;

use App\Models\Forum\ForumDecor;
use App\Services\InventoryManager;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class ForumDecorService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Forum Decor Service
    |--------------------------------------------------------------------------
    |
    | Handles the forum decor item tag.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData() {
        return [
            'decors' => ForumDecor::default(0)->isStaff(0)->orderBy('type')->sortAlphabetical()->get()->pluck('fullName', 'id')->toArray(),
            'grantTypes' => ['Choice' => 'User Choice', 'Random' => 'Randomized from Choices', 'All' => 'All Selected Decors'],
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
            'decor_ids' => $tag->data['decor_ids'] ?? [],
            'decors'    => ForumDecor::visible()->default(0)->isStaff(0)->whereIn('id', $tag->data['decor_ids'] ?? [])->orderBy('type')->sortAlphabetical()->get()->pluck('fullName', 'id')->toArray(),
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
            $tag->update(['data' => Arr::only($data, ['grant_type', 'decor_ids'])]);

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
            foreach ($stacks as $key=> $stack) {
                // We don't want to let anyone who isn't the owner of the box open it,
                // so do some validation...
                if ($stack->user_id != $user->id) {
                    throw new \Exception('This item does not belong to you.');
                }

                // Next, try to delete the box item. If successful, we can start distributing rewards.
                if ((new InventoryManager)->debitStack($stack->user, 'Box Opened', ['data' => ''], $stack, $data['quantities'][$key])) {
                    for ($q = 0; $q < $data['quantities'][$key]; $q++) {
                        // Distribute user rewards
                        if (!$rewards = fillUserAssets(parseAssetData($stack->item->tag('box')->data), $user, $user, 'Box Rewards', [
                            'data' => 'Received rewards from opening '.$stack->item->name,
                        ])) {
                            throw new \Exception('Failed to open box.');
                        }
                        flash($this->getBoxRewardsString($rewards));
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
    private function getDecorRewardsString($rewards) {
        return 'You have received the forum decor(s): '.createRewardsString($rewards);
    }
}
