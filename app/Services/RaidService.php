<?php

namespace App\Services;

use App\Models\Raid\Raid;
use App\Models\Raid\RaidBoss;
use App\Models\Raid\RaidReward;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RaidService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Raid Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of raids.
    |
    */

    /**********************************************************************************************

        RAIDS

    **********************************************************************************************/

    /**
     * Creates a new raid.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Raid\Raid|bool
     */
    public function createRaid($data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_background'] = 1;
                $data['background_hash'] = randomString(10);
                $data['background_extension'] = $data['image']->getClientOriginalExtension();
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_background'] = 0;
            }

            $raid = Raid::create(Arr::only($data, ['name', 'description', 'parsed_description', 'is_visible', 'start_at', 'end_at', 'has_background', 'background_hash', 'background_extension']));

            if ($image) {
                $this->handleImage($image, $raid->imagePath, $raid->imageFileName);
            }

            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity']), $raid);

            return $this->commitReturn($raid);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a raid.
     *
     * @param \App\Models\Raid\Raid $raid
     * @param array                     $data
     * @param \App\Models\User\User     $user
     *
     * @return \App\Models\Raid\Raid|bool
     */
    public function updateRaid($raid, $data, $user) {
        DB::beginTransaction();

        try {
            // More specific validation
            if (Raid::where('name', $data['name'])->where('id', '!=', $raid->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            $data = $this->populateData($data, $raid);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_background'] = 1;
                $data['background_hash'] = randomString(10);
                $data['background_extension'] = $data['image']->getClientOriginalExtension();
                $image = $data['image'];
                unset($data['image']);
            }

            $raid->update(Arr::only($data, ['name', 'description', 'parsed_description', 'is_visible', 'start_at', 'end_at', 'has_background', 'background_hash', 'background_extension']));

            if ($raid) {
                $this->handleImage($image, $raid->imagePath, $raid->imageFileName);
            }

            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity']), $raid);

            return $this->commitReturn($raid);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a raid.
     *
     * @param \App\Models\Raid\Raid $raid
     *
     * @return bool
     */
    public function deleteRaid($raid) {
        DB::beginTransaction();

        try {
            // // Check first if the category is currently in use
            // if (Submission::where('raid_id', $raid->id)->exists()) {
            //     throw new \Exception('A submission under this raid exists. Deleting the raid will break the submission page - consider setting the raid to be not active instead.');
            // }

            $raid->rewards()->delete();
            if ($raid->has_background) {
                $this->deleteImage($raid->imagePath, $raid->imageFileName);
            }
            $raid->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

        RAID BOSSES

    **********************************************************************************************/

    /**
     * Creates a new raid boss.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Raid\RaidBoss|bool
     */
    public function createRaidBoss($data, $user) {
        DB::beginTransaction();

        try {
            if (!isset($data['raid_id'])) {
                throw new \Exception('Raid this boss is associated with could not be found.');
            }

            $data = $this->populateBossData($data);

            $boss = RaidBoss::create(Arr::only($data, ['name', 'description', 'parsed_description', 'is_visible', 'health', 'raid_id']));

            return $this->commitReturn($boss);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a raid boss.
     *
     * @param \App\Models\Raid\RaidBoss $boss
     * @param array                     $data
     * @param \App\Models\User\User     $user
     *
     * @return \App\Models\Raid\RaidBoss|bool
     */
    public function updateRaidBoss($boss, $data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateBossData($data, $boss);

            $boss->update(Arr::only($data, ['name', 'description', 'parsed_description', 'is_visible', 'health', 'raid_id']));

            return $this->commitReturn($boss);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a raid.
     *
     * @param \App\Models\Raid\RaidBoss $boss
     *
     * @return bool
     */
    public function deleteRaidBoss($boss) {
        DB::beginTransaction();

        try {
            // // Check first if the category is currently in use
            // if (Submission::where('raid_id', $raid->id)->exists()) {
            //     throw new \Exception('A submission under this raid exists. Deleting the raid will break the submission page - consider setting the raid to be not active instead.');
            // }

            $raid->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Processes user input for creating/updating a raid.
     *
     * @param array                     $data
     * @param \App\Models\Raid\Raid $raid
     *
     * @return array
     */
    private function populateData($data, $raid = null) {
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        } else {
            $data['description'] = null;
            $data['parsed_description'] = null;
        }

        if (!isset($data['is_visible'])) {
            $data['is_visible'] = 0;
        }

        if (isset($data['remove_image'])) {
            if ($raid && $raid->has_background && $data['remove_image']) {
                $data['has_background'] = 0;
                $this->deleteImage($raid->imagePath, $raid->imageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Processes user input for creating/updating a raid boss.
     *
     * @param array                     $data
     * @param \App\Models\Raid\Raid $raid
     *
     * @return array
     */
    private function populateBossData($data, $boss = null) {
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        } else {
            $data['description'] = null;
            $data['parsed_description'] = null;
        }

        if (!isset($data['is_visible'])) {
            $data['is_visible'] = 0;
        }

        if (!isset($data['health'])) {
            $data['health'] = null;
        }

        return $data;
    }

    /**
     * Processes user input for creating/updating raid rewards.
     *
     * @param array                     $data
     * @param \App\Models\Raid\Raid $raid
     */
    private function populateRewards($data, $raid) {
        // Clear the old rewards...
        $raid->rewards()->delete();

        if (isset($data['rewardable_type'])) {
            foreach ($data['rewardable_type'] as $key => $type) {
                RaidReward::create([
                    'raid_id'       => $raid->id,
                    'rewardable_type' => $type,
                    'rewardable_id'   => $data['rewardable_id'][$key],
                    'quantity'        => $data['quantity'][$key],
                ]);
            }
        }
    }
}
