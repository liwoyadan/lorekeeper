<?php

namespace App\Http\Controllers;

use App\Services\CharacterLinkService;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller {
    /**
     * Accepts or rejects a link request.
     *
     * @param mixed $action
     * @param mixed $id
     *
     * @return response 200
     */
    public function postHandleLink(CharacterLinkService $service, $action, $id) {
        if (!$service->handleCharacterRelationLink($id, $action, Auth::user())) {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        } else {
            flash('Link request '.$action.'ed successfully.')->success();
        }

        return redirect()->back();
    }
}
