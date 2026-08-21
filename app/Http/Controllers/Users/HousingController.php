<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
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
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
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
            foreach ($service->errors()->getMessages()['error'] ?? [] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
