<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\Housing\Home;
use App\Services\HousingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HousingController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Housing Controller
    |--------------------------------------------------------------------------
    |
    | Handles a home owner saving their room layout.
    |
    */

    /**
     * Saves the posted layout for a home the acting user controls.
     */
    public function postLayout(Request $request, HousingManager $service, $id) {
        $home = Home::find($id);
        if (!$home) {
            abort(404);
        }

        $layout = json_decode($request->input('layout'), true);
        if (!is_array($layout)) {
            $layout = [];
        }

        if ($service->saveLayout($home, Auth::user(), $layout)) {
            flash('Home updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Claims the acting user's own home.
     */
    public function postClaim(HousingManager $service) {
        $this->claim($service, Auth::user());

        return redirect()->back();
    }

    /**
     * Claims a home for a character the acting user owns.
     */
    public function postClaimCharacter(HousingManager $service, $id) {
        $character = Character::find($id);
        if (!$character) {
            abort(404);
        }

        $this->claim($service, $character);

        return redirect()->back();
    }

    /**
     * Runs a claim for the given owner and flashes the result.
     */
    private function claim(HousingManager $service, $owner) {
        if ($service->claimHome($owner, Auth::user())) {
            flash('Home claimed successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }
    }
}
