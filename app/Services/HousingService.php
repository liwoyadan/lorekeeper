<?php

namespace App\Services;

use App\Models\Housing\HousingDecor;
use App\Models\Housing\HousingPattern;
use App\Models\Housing\HousingZone;
use App\Models\Housing\HousingZoneColor;
use DB;

class HousingService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Housing Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of housing decor and patterns.
    |
    */

    /**
     * Creates a new housing pattern.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|HousingPattern
     */
    public function createPattern($data, $user) {
        DB::beginTransaction();

        try {
            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            if (!isset($data['is_visible'])) {
                $data['is_visible'] = 0;
            }

            $pattern = HousingPattern::create($data);

            if ($image) {
                $this->handleImage($image, $pattern->patternImagePath, $pattern->patternImageFileName);
            }

            return $this->commitReturn($pattern);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a housing pattern.
     *
     * @param HousingPattern        $pattern
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|HousingPattern
     */
    public function updatePattern($pattern, $data, $user) {
        DB::beginTransaction();

        try {
            if (HousingPattern::where('name', $data['name'])->where('id', '!=', $pattern->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            if (!isset($data['is_visible'])) {
                $data['is_visible'] = 0;
            }

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $image = $data['image'];
                unset($data['image']);
            }

            if (isset($data['remove_image'])) {
                if ($pattern->has_image && $data['remove_image']) {
                    $data['has_image'] = 0;
                    $this->deleteImage($pattern->patternImagePath, $pattern->patternImageFileName);
                }
                unset($data['remove_image']);
            }

            $pattern->update($data);

            if ($image) {
                $this->handleImage($image, $pattern->patternImagePath, $pattern->patternImageFileName);
            }

            return $this->commitReturn($pattern);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a housing pattern.
     *
     * @param HousingPattern $pattern
     *
     * @return bool
     */
    public function deletePattern($pattern) {
        DB::beginTransaction();

        try {
            if ($pattern->has_image) {
                $this->deleteImage($pattern->patternImagePath, $pattern->patternImageFileName);
            }
            $pattern->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Sorts housing pattern order.
     *
     * @param array $data
     *
     * @return bool
     */
    public function sortPattern($data) {
        DB::beginTransaction();

        try {
            $sort = array_reverse(explode(',', $data));
            foreach ($sort as $key => $s) {
                HousingPattern::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Creates a new housing decor.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|HousingDecor
     */
    public function createDecor($data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateDecorData($data);

            $art = null;
            $isSvg = ($data['render_mode'] ?? 'mask') == 'svg';
            $field = $isSvg ? 'svg_file' : 'image';
            if (isset($data[$field]) && $data[$field]) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $art = $data[$field];
            } else {
                $data['has_image'] = 0;
            }
            unset($data['image'], $data['svg_file']);

            $decor = HousingDecor::create($data);

            if ($art) {
                $this->handleImage($art, $decor->decorImagePath, $decor->decorArtFileName);
            }

            return $this->commitReturn($decor);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a housing decor.
     *
     * @param HousingDecor          $decor
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|HousingDecor
     */
    public function updateDecor($decor, $data, $user) {
        DB::beginTransaction();

        try {
            if (HousingDecor::where('name', $data['name'])->where('id', '!=', $decor->id)->exists()) {
                throw new \Exception('The name has already been taken.');
            }

            $zoneData = [];
            foreach (['zone_id', 'zone_name', 'zone_selector', 'zone_free_color', 'zone_colors', 'zone_patterns', 'zone_mask'] as $zoneKey) {
                if (isset($data[$zoneKey])) {
                    $zoneData[$zoneKey] = $data[$zoneKey];
                    unset($data[$zoneKey]);
                }
            }

            $data = $this->populateDecorData($data, $decor);

            $art = null;
            $isSvg = ($data['render_mode'] ?? $decor->render_mode) == 'svg';
            $field = $isSvg ? 'svg_file' : 'image';
            if (isset($data[$field]) && $data[$field]) {
                $data['has_image'] = 1;
                if (!$decor->hash) {
                    $data['hash'] = randomString(10);
                }
                $art = $data[$field];
            }
            unset($data['image'], $data['svg_file']);

            $decor->update($data);

            if ($art) {
                $this->handleImage($art, $decor->decorImagePath, $decor->decorArtFileName);
            }

            $this->syncDecorZones($decor, $zoneData);

            return $this->commitReturn($decor);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a housing decor.
     *
     * @param HousingDecor $decor
     *
     * @return bool
     */
    public function deleteDecor($decor) {
        DB::beginTransaction();

        try {
            foreach ($decor->zones as $zone) {
                if ($zone->has_mask) {
                    $this->deleteImage($zone->maskPath, $zone->maskFileName);
                }
                $zone->patterns()->detach();
                $zone->colors()->delete();
                $zone->delete();
            }

            if ($decor->has_image) {
                $this->deleteImage($decor->decorImagePath, $decor->decorArtFileName);
            }
            $decor->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Sorts housing decor order.
     *
     * @param array $data
     *
     * @return bool
     */
    public function sortDecor($data) {
        DB::beginTransaction();

        try {
            $sort = array_reverse(explode(',', $data));
            foreach ($sort as $key => $s) {
                HousingDecor::where('id', $s)->update(['sort' => $key]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Syncs a decor's recolor zones, patterns, and colors from submitted form data.
     *
     * @param HousingDecor $decor
     * @param array        $data
     */
    private function syncDecorZones($decor, $data) {
        $names = $data['zone_name'] ?? [];
        $ids = $data['zone_id'] ?? [];
        $selectors = $data['zone_selector'] ?? [];
        $frees = $data['zone_free_color'] ?? [];
        $colors = $data['zone_colors'] ?? [];
        $masks = $data['zone_mask'] ?? [];
        $patterns = $data['zone_patterns'] ?? [];

        $keptIds = [];

        foreach ($names as $i => $name) {
            if (!$name) {
                continue;
            }

            $zoneId = isset($ids[$i]) && $ids[$i] ? $ids[$i] : null;
            $zone = $zoneId ? HousingZone::where('id', $zoneId)->where('decor_id', $decor->id)->first() : null;

            $attrs = [
                'decor_id'         => $decor->id,
                'name'             => $name,
                'sort'             => $i,
                'svg_selector'     => $decor->render_mode == 'svg' && isset($selectors[$i]) ? $selectors[$i] : null,
                'allow_free_color' => isset($frees[$i]) && $frees[$i] ? 1 : 0,
            ];

            if ($zone) {
                $zone->update($attrs);
            } else {
                $attrs['has_mask'] = 0;
                $zone = HousingZone::create($attrs);
            }

            $mask = isset($masks[$i]) && $masks[$i] ? $masks[$i] : null;
            if ($decor->render_mode == 'mask' && $mask) {
                $zone->has_mask = 1;
                $zone->hash = randomString(10);
                $zone->save();
                $this->handleImage($mask, $zone->maskPath, $zone->maskFileName);
            }

            $patternIds = isset($patterns[$i]) && $patterns[$i] ? $patterns[$i] : [];
            $zone->patterns()->sync($patternIds);

            $zone->colors()->delete();
            if (isset($colors[$i]) && $colors[$i]) {
                $hexes = array_filter(array_map('trim', explode(',', $colors[$i])));
                $c = 0;
                foreach ($hexes as $hex) {
                    $hex = str_replace('#', '', $hex);
                    if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
                        continue;
                    }
                    HousingZoneColor::create(['zone_id' => $zone->id, 'hex' => $hex, 'sort' => $c]);
                    $c++;
                }
            }

            $keptIds[] = $zone->id;
        }

        foreach ($decor->zones()->whereNotIn('id', $keptIds)->get() as $removed) {
            if ($removed->has_mask) {
                $this->deleteImage($removed->maskPath, $removed->maskFileName);
            }
            $removed->patterns()->detach();
            $removed->colors()->delete();
            $removed->delete();
        }
    }

    /**
     * Processes decor data for saving.
     *
     * @param array        $data
     * @param HousingDecor $decor
     *
     * @return array
     */
    private function populateDecorData($data, $decor = null) {
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        } else {
            $data['parsed_description'] = null;
        }

        if (!isset($data['kind']) || $data['kind'] != 'furniture') {
            $data['layer'] = null;
        }

        if (!isset($data['default_scale']) || !$data['default_scale']) {
            $data['default_scale'] = 1;
        }

        if (!isset($data['is_visible'])) {
            $data['is_visible'] = 0;
        }

        if (isset($data['remove_image'])) {
            if ($decor && $decor->has_image && $data['remove_image']) {
                $data['has_image'] = 0;
                $this->deleteImage($decor->decorImagePath, $decor->decorArtFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }
}
