<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
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
    | use browser localstorage.
    |
    */

    /**
     * Gets the accessibility/alt settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSettings() {
        return view('account.accessibility');
    }

    /**
     * Get the accessibility/alt settings partial (the panel).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPanel() {
        return view('account._accessibility_panel');
    }

    /**
     * Process the selected options in one request (full replace).
     * Guest settings persist via localStorage.
     *
     * @param App\Services\AccessibilityManager $service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postSaveAll(Request $request, AccessibilityManager $service) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => true]);
        }

        $clean = $service->saveAllUserValues($user, $request->input('values', []));
        if ($service->errors()->any()) {
            return response()->json(['success' => false, 'error' => $service->errors()->first()], 422);
        }

        return response()->json(['success' => true, 'values' => $clean]);
    }
}
