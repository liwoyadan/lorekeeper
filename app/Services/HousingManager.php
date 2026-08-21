<?php

namespace App\Services;

use App\Facades\Settings;
use App\Models\Character\Character;
use App\Models\Housing\Home;
use App\Models\Housing\OwnedDecor;
use App\Models\User\User;
use Illuminate\Support\Facades\DB;

class HousingManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Housing Manager
    |--------------------------------------------------------------------------
    |
    | Handles home provisioning for users and characters.
    |
    */

    /**
     * Whether housing is enabled and this owner type may own a home.
     *
     * @param mixed $owner
     *
     * @return bool
     */
    public static function homeEnabledFor($owner) {
        if (!Settings::get('housing_enabled')) {
            return false;
        }

        $mode = Settings::get('housing_mode');
        if ($owner instanceof User) {
            return $mode != 1;
        }
        if ($owner instanceof Character) {
            return $mode != 0;
        }

        return false;
    }

    /**
     * Gets the owner's home, auto-creating it when provisioning is set to auto.
     *
     * @param mixed $owner
     *
     * @return Home|null
     */
    public function getOrProvisionHome($owner) {
        $home = Home::where('owner_type', get_class($owner))->where('owner_id', $owner->id)->first();

        if (!$home && Settings::get('housing_acquirement') == 0) {
            $home = Home::create([
                'owner_type' => get_class($owner),
                'owner_id'   => $owner->id,
            ]);
        }

        return $home;
    }

    /**
     * Overwrites a home's layout (placements + wall/floor slots) after validating
     * ownership, per-decor counts, the placement cap, and clamping every field.
     *
     * @param Home  $home
     * @param mixed $user
     * @param mixed $layout
     *
     * @return Home|bool
     */
    public function saveLayout($home, $user, $layout) {
        DB::beginTransaction();

        try {
            if (!$this->userControlsHome($home, $user)) {
                throw new \Exception('You do not have permission to edit this home.');
            }

            if (!is_array($layout)) {
                $layout = [];
            }
            $placements = $layout['placements'] ?? [];
            if (!is_array($placements)) {
                $placements = [];
            }

            $owned = OwnedDecor::with('decor')->where('user_id', $user->id)->get()->keyBy('id');

            $min = config('lorekeeper.housing.min_scale');
            $max = config('lorekeeper.housing.max_scale');
            $cap = Settings::get('housing_max_placements');

            $clean = [];
            $used = [];
            foreach ($placements as $p) {
                $ownedId = isset($p['owned_decor_id']) ? (int) $p['owned_decor_id'] : 0;
                if (!isset($owned[$ownedId])) {
                    continue;
                }

                $stack = $owned[$ownedId];
                if (!$stack->decor || $stack->decor->kind != 'furniture') {
                    continue;
                }

                $this->chargeStack($stack, $ownedId, $used);

                $clean[] = [
                    'owned_decor_id' => $ownedId,
                    'x'              => $this->clampNum($p['x'] ?? 0, 0, 100),
                    'y'              => $this->clampNum($p['y'] ?? 0, 0, 100),
                    'scale'          => $this->clampNum($p['scale'] ?? $min, $min, $max),
                    'z'              => max(0, (int) ($p['z'] ?? 0)),
                    'flip_x'         => isset($p['flip_x']) && $p['flip_x'] ? true : false,
                ];
            }

            if (count($clean) > $cap) {
                throw new \Exception('This room can hold at most '.$cap.' items.');
            }

            $wall = $this->validateSlot($layout['wall'] ?? null, 'wall', $owned, $used);
            $floor = $this->validateSlot($layout['floor'] ?? null, 'floor', $owned, $used);

            $out = $home->layoutData;
            $out['placements'] = $clean;
            $out['wall'] = $wall;
            $out['floor'] = $floor;
            $home->update(['layout' => json_encode($out)]);

            return $this->commitReturn($home);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Validates a wall/floor slot selection, returning the owned decor id (or
     * null), and counting the slot against the shared per-decor count invariant.
     *
     * @param mixed  $ownedId
     * @param string $kind
     * @param mixed  $owned
     * @param array  $used
     *
     * @return int|null
     */
    private function validateSlot($ownedId, $kind, $owned, &$used) {
        $ownedId = (int) $ownedId;
        if (!$ownedId || !isset($owned[$ownedId])) {
            return null;
        }

        $stack = $owned[$ownedId];
        if (!$stack->decor || $stack->decor->kind != $kind) {
            return null;
        }

        $this->chargeStack($stack, $ownedId, $used);

        return $ownedId;
    }

    /**
     * Counts one use of an owned stack against the shared per-decor tally,
     * throwing when the placements plus slot uses exceed what the user owns.
     *
     * @param mixed $stack
     * @param int   $ownedId
     * @param array $used
     */
    private function chargeStack($stack, $ownedId, &$used) {
        $used[$ownedId] = ($used[$ownedId] ?? 0) + 1;
        if ($used[$ownedId] > $stack->count) {
            throw new \Exception('You have used more copies of a decor than you own.');
        }
    }

    /**
     * Whether the acting user controls this home (owns it, or owns the character
     * that owns it).
     *
     * @param Home  $home
     * @param mixed $user
     *
     * @return bool
     */
    private function userControlsHome($home, $user) {
        if ($home->owner_type == User::class) {
            return $home->owner_id == $user->id;
        }
        if ($home->owner_type == Character::class) {
            $character = Character::find($home->owner_id);

            return $character && $character->user_id == $user->id;
        }

        return false;
    }

    /**
     * Clamps a numeric value into a range, rounded to 2 decimals.
     *
     * @param mixed $value
     * @param mixed $min
     * @param mixed $max
     *
     * @return float
     */
    private function clampNum($value, $min, $max) {
        $value = (float) $value;
        if ($value < $min) {
            return $min;
        }
        if ($value > $max) {
            return $max;
        }

        return round($value, 2);
    }
}
