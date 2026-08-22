<?php

namespace App\Services;

use App\Facades\Settings;
use App\Models\Character\Character;
use App\Models\Currency\Currency;
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
     * Claims an empty home for its owner when acquirement is set to Claim,
     * debiting the configured currency from the acting user when priced.
     */
    public function claimHome($owner, $user) {
        DB::beginTransaction();

        try {
            if (!self::homeEnabledFor($owner)) {
                throw new \Exception('Housing is not available for this owner.');
            }

            if (Settings::get('housing_acquirement') != 1) {
                throw new \Exception('Homes are not claimable right now.');
            }

            if (!$this->userControlsOwner($owner, $user)) {
                throw new \Exception('You do not have permission to claim this home.');
            }

            $existing = Home::where('owner_type', get_class($owner))->where('owner_id', $owner->id)->first();
            if ($existing) {
                throw new \Exception('This home has already been claimed.');
            }

            $cost = (int) Settings::get('housing_claim_cost');
            $currencyId = (int) Settings::get('housing_claim_currency');
            if ($cost > 0 && $currencyId) {
                $currency = Currency::find($currencyId);
                if (!$currency) {
                    throw new \Exception('The configured claim currency does not exist.');
                }

                if (!(new CurrencyManager)->debitCurrency($user, null, 'Home Claim', 'Claimed a home.', $currency, $cost)) {
                    throw new \Exception('You do not have enough '.$currency->name.' to claim this home.');
                }
            }

            $home = Home::create([
                'owner_type' => get_class($owner),
                'owner_id'   => $owner->id,
            ]);

            return $this->commitReturn($home);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Overwrites a home's layout (placements + wall/floor slots) after validating
     * ownership, per-decor counts, the placement cap, and clamping every field.
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
     * Whether the acting user controls this owner directly (is the user, or
     * owns the character).
     */
    private function userControlsOwner($owner, $user) {
        if (!$user) {
            return false;
        }
        if ($owner instanceof User) {
            return $owner->id == $user->id;
        }
        if ($owner instanceof Character) {
            return $owner->user_id == $user->id;
        }

        return false;
    }

    /**
     * Clamps a numeric value into a range, rounded to 2 decimals.
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
