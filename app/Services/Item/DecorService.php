<?php

namespace App\Services\Item;

use App\Models\Housing\HousingDecor;
use App\Models\Housing\OwnedDecor;
use App\Services\InventoryManager;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

class DecorService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Decor Service
    |--------------------------------------------------------------------------
    |
    | Handles the editing and redemption of housing decor items.
    |
    */

    /**
     * Retrieves any data that should be used in the item tag editing form.
     *
     * @return array
     */
    public function getEditData() {
        return [
            'decors' => HousingDecor::orderBy('name')->pluck('name', 'id'),
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
        return $tag->data ?: [];
    }

    /**
     * Stores which decor the item grants.
     *
     * @param object $tag
     * @param array  $data
     *
     * @return bool
     */
    public function updateData($tag, $data) {
        DB::beginTransaction();

        try {
            if (!isset($data['decor_id']) || !HousingDecor::where('id', $data['decor_id'])->exists()) {
                throw new \Exception('Please select a valid decor piece.');
            }

            $tag->update(['data' => json_encode(['decor_id' => (int) $data['decor_id']])]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Acts upon the item when redeemed from the inventory.
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
            foreach ($stacks as $key => $stack) {
                if ($stack->user_id != $user->id) {
                    throw new \Exception('This item does not belong to you.');
                }

                $tagData = $stack->item->tag('decor')->data;
                $decor = HousingDecor::with(['zones.patterns', 'zones.colors'])->find(isset($tagData['decor_id']) ? $tagData['decor_id'] : null);
                if (!$decor) {
                    throw new \Exception('This item does not grant a valid decor piece.');
                }

                $customization = $this->buildCustomization($decor, $data);
                $encoded = json_encode($customization);

                $quantity = $data['quantities'][$key];
                if ((new InventoryManager)->debitStack($stack->user, 'Decor Redeemed', ['data' => ''], $stack, $quantity)) {
                    $owned = OwnedDecor::where('user_id', $user->id)
                        ->where('decor_id', $decor->id)
                        ->where('customization', $encoded)
                        ->first();

                    if (!$owned) {
                        $owned = OwnedDecor::create([
                            'user_id'       => $user->id,
                            'decor_id'      => $decor->id,
                            'customization' => $encoded,
                            'count'         => 0,
                        ]);
                    }

                    $owned->count += $quantity;
                    $owned->save();

                    flash('You have redeemed '.$quantity.'x <strong>'.$decor->name.'</strong>.')->success();
                }
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Validates the decor selection...
     *
     * @param \App\Models\Housing\HousingDecor $decor
     * @param array                            $data
     *
     * @return array
     */
    private function buildCustomization($decor, $data) {
        $result = [];
        $choices = isset($data['zone_choice']) ? $data['zone_choice'] : [];
        $freeColors = isset($data['zone_free_color']) ? $data['zone_free_color'] : [];

        foreach ($decor->zones as $zone) {
            $hasOptions = $zone->colors->count() || $zone->patterns->count() || $zone->allow_free_color;
            $choice = isset($choices[$zone->id]) && $choices[$zone->id] ? $choices[$zone->id] : null;

            if (!$choice) {
                if ($hasOptions) {
                    throw new \Exception('Please choose a fill for zone "'.$zone->name.'".');
                }
                continue;
            }

            if ($choice == 'free') {
                $hex = isset($freeColors[$zone->id]) ? strtolower(str_replace('#', '', $freeColors[$zone->id])) : null;
                if ($zone->allow_free_color && $hex && preg_match('/^[0-9a-f]{6}$/', $hex)) {
                    $result[$zone->id] = ['type' => 'color', 'value' => $hex];
                    continue;
                }
                throw new \Exception('Invalid custom color for zone "'.$zone->name.'".');
            }

            if (substr($choice, 0, 6) == 'color:') {
                $hex = strtolower(str_replace('#', '', substr($choice, 6)));
                $allowed = $zone->colors->pluck('hex')->map(function ($h) {
                    return strtolower($h);
                })->toArray();
                if (in_array($hex, $allowed)) {
                    $result[$zone->id] = ['type' => 'color', 'value' => $hex];
                    continue;
                }
                throw new \Exception('Invalid color selected for zone "'.$zone->name.'".');
            }

            if (substr($choice, 0, 8) == 'pattern:') {
                $patternId = (int) substr($choice, 8);
                if ($zone->patterns->contains('id', $patternId)) {
                    $result[$zone->id] = ['type' => 'pattern', 'value' => $patternId];
                    continue;
                }
                throw new \Exception('Invalid pattern selected for zone "'.$zone->name.'".');
            }

            throw new \Exception('Invalid selection for zone "'.$zone->name.'".');
        }

        ksort($result);

        return $result;
    }
}
