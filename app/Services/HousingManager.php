<?php

namespace App\Services;

use App\Facades\Settings;
use App\Models\Character\Character;
use App\Models\Housing\Home;
use App\Models\User\User;

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
     * @return \App\Models\Housing\Home|null
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
}
