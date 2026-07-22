<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Theme\AccessibilitySetting;
use App\Services\AccessibilityManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessibilityController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Accessibility Controller
    |--------------------------------------------------------------------------
    |
    | Handles the public accessibility/alt settings panel!
    | Logged in users save to their user settings, while guests
    | use localstorage.
    |
    */

    /**
     * Gets the accessibility/alt settings.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSettings() {
        return view('account.accessibility');
    }

    /**
     * Saves selected settings.
     *
     * @param App\Services\AccessibilityManager $service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postSave(Request $request, AccessibilityManager $service) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => true]);
        }

        $setting = AccessibilitySetting::active()->where('setting_key', $request->input('setting_key'))->first();
        if (!$setting) {
            return response()->json(['success' => false, 'error' => 'Unknown setting.'], 422);
        }

        $clean = $service->saveUserValue($user, $setting, $request->input('value'));
        if ($service->errors()->any()) {
            return response()->json(['success' => false, 'error' => $service->errors()->first()], 422);
        }

        return response()->json(['success' => true, 'value' => $clean]);
    }

    /**
     * Resets all set settings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postResetAll(AccessibilityManager $service) {
        $user = Auth::user();
        if ($user) {
            $service->resetAllUserValues($user);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Resets a specific setting.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postReset(AccessibilityManager $service, $id) {
        $user = Auth::user();
        if ($user) {
            $setting = AccessibilitySetting::where('id', $id)->first();
            if ($setting) {
                $service->resetUserValue($user, $setting);
            }
        }

        return response()->json(['success' => true]);
    }
}
