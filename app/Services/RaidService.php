<?php

namespace App\Services;

use App\Models\Raid\Raid;
use App\Models\Raid\RaidBoss;
use App\Models\Raid\RaidBossImage;
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

            if ($image) {
                $this->handleImage($image, $raid->imagePath, $raid->imageFileName);
            }

            $this->populateDamage(Arr::only($data, ['damage_type', 'damage_id', 'damage_quantity', 'damage_base', 'damage_max']), $raid);
            $this->populateRewards(Arr::only($data, ['rewardable_type', 'rewardable_id', 'quantity', 'damage_required']), $raid);

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

            $a = 0;
            $c = 0;
            if (isset($data['threshold_type'])) {
                $bossData = $boss->data;
                $t = 0;

                foreach ($data['threshold_type'] as $key => $value) {
                    if (!isset($data['threshold_amount'][$key])) {
                        $a++;
                        continue;
                    }
                    if (!isset($data['threshold_bar_color'][$key]) && !isset($data['threshold_text_color'][$key])) {
                        $c++;
                        continue;
                    }

                    $bossData['thresholds'][$t]['type'] = $data['threshold_type'][$key];
                    if ($data['threshold_type'][$key] == 'percent' && $data['threshold_amount'][$key] > 100) {
                        $bossData['thresholds'][$t]['amount'] = 100;
                    } else {
                        $bossData['thresholds'][$t]['amount'] = $data['threshold_amount'][$key];
                    }
                    $bossData['thresholds'][$t]['bar_color'] = $data['threshold_bar_color'][$key] ?? null;
                    $bossData['thresholds'][$t]['text_color'] = $data['threshold_text_color'][$key] ?? null;
                    $t++;
                }

                $boss->data = $bossData;
                $boss->save();
            } else {
                $bossData = $boss->data;
                $bossData['thresholds'] = null;
                $boss->data = $bossData;
                $boss->save();
            }

            if ($a > 0) {
                flash('One or more threshold entries have been skipped due to lacking an amount value.')->error();
            }
            if ($c > 0) {
                flash('One or more threshold entries have been skipped due to lacking both bar and bar text color values.')->error();
            }

            return $this->commitReturn($boss);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a raid boss.
     *
     * @param \App\Models\Raid\RaidBoss $boss
     *
     * @return bool
     */
    public function deleteRaidBoss($boss) {
        DB::beginTransaction();

        try {
            if ($boss->images->count()) {
                $boss->images->delete();
            }

            $boss->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Creates a new image for a raid boss.
     *
     * @param \App\Models\Raid\RaidBoss $boss
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Raid\Raid|bool
     */
    public function createRaidBossImage($boss, $data, $user) {
        DB::beginTransaction();

        try {
            if (!$boss) {
                throw new \Exception('The raid boss you are trying to add an image to could not be found.');
            }
            if (!isset($data['image'])) {
                throw new \Exception('Image is required.');
            }

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $data['extension'] = $data['image']->getClientOriginalExtension();
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            $raidBossImage = RaidBossImage::create([
                'raid_boss_id' => $boss->id,
                'health_threshold' => $data['health_threshold'] ?? null,
                'threshold_type' => $data['threshold_type'] ?? 'percent',
                'has_image' => $data['has_image'],
                'hash' => $data['hash'] ?? null,
                'extension' => $data['extension'] ?? null,
            ]);

            if ($image) {
                $this->handleImage($image, $raidBossImage->imagePath, $raidBossImage->imageFileName);
            }

            return $this->commitReturn($raidBossImage);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates an image for a raid boss.
     *
     * @param \App\Models\Raid\RaidBoss $boss
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return \App\Models\Raid\Raid|bool
     */
    public function updateRaidBossImage($boss, $data, $user, $bossImage) {
        DB::beginTransaction();

        try {
            if (!$boss) {
                throw new \Exception('The raid boss you are trying to editing an image of could not be found.');
            }
            if (!$bossImage) {
                throw new \Exception('Invalid boss image.');
            }

            $image = null;
            if (isset($data['image']) && $data['image']) {
                if ($boss && $boss->has_image) {
                    $data['has_image'] = 0;
                    $this->deleteImage($bossImage->imagePath, $bossImage->imageFileName);
                }

                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $data['extension'] = $data['image']->getClientOriginalExtension();
                $image = $data['image'];
                unset($data['image']);
            }

            $bossImage->health_threshold = $data['health_threshold'] ?? null;
            $bossImage->threshold_type = $data['threshold_type'] ?? 'percent';
            $bossImage->save();

            if ($image) {
                $bossImage->hash = $data['hash'];
                $bossImage->extension = $data['extension'];
                $bossImage->save();

                $this->handleImage($image, $bossImage->imagePath, $bossImage->imageFileName);
            }

            return $this->commitReturn($bossImage);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a raid.
     *
     * @param \App\Models\Raid\RaidBoss $boss
     * @param \App\Models\Raid\RaidBossImage $bossImage
     *
     * @return bool
     */
    public function deleteRaidBossImage($boss, $bossImage) {
        DB::beginTransaction();

        try {
            if ($boss->id != $bossImage->raid_boss_id) {
                throw new \Exception('The image you are trying to delete is not associated with this raid boss.');
            }

            if ($bossImage->has_image) {
                $this->deleteImage($bossImage->imagePath, $bossImage->imageFileName);
            }
            $bossImage->delete();

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
                    'damage_required' => $data['damage_required'][$key] ?? 0,
                ]);
            }
        }
    }

    /**
     * Processes input for what deals damage to the raid.
     *
     * @param array                     $data
     * @param \App\Models\Raid\Raid $raid
     */
    private function populateDamage($data, $raid) {
        $raidData = $raid->data;
        $raidData['damage']['type'] = $data['damage_type'] ?? null;
        $raidData['damage']['id'] = $data['damage_id'] ?? null;
        $raidData['damage']['quantity'] = $data['damage_quantity'] ?? null;
        $raidData['damage']['base'] = $data['damage_base'] ?? 1;
        $raidData['damage']['max'] = $data['damage_max'] ?? null;
        $raid->data = $raidData;
        $raid->save();
    }

    /**
     * Updates raids if they should be visible or not.
     *
     * @return bool
     */
    public function updateQueue() {
        $count = Raid::shouldBeVisible()->count();
        if ($count) {
            DB::beginTransaction();

            try {
                Raid::shouldBeVisible()->update(['is_visible' => 1]);
                $bosses = RaidBoss::where('is_visible', 0)->whereHas('raid', function($r) {
                    $r->where('is_visible', 1);
                })->update(['is_visible' => 1]);

                return $this->commitReturn(true);
            } catch (\Exception $e) {
                $this->setError('error', $e->getMessage());
            }

            return $this->rollbackReturn(false);
        }
    }
}
