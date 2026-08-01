<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Forum\Forum;
use App\Models\Forum\ForumDecor;
use App\Models\Forum\ForumFlair;
use App\Models\Rank\Rank;
use App\Services\ForumService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Forum Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of forums, forum flairs, and forum decors.
    |
    */

    /**********************************************************************************************

        FORUMS

    **********************************************************************************************/

    /**
     * Shows the forum index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex() {
        return view('admin.forums.forums', [
            'forums' => Forum::orderBy('sort')->paginate(20),
        ]);
    }

    /**
     * Shows the create forum forum.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateForum() {
        return view('admin.forums.create_edit_forum', [
            'forum'  => new Forum,
            'forums' => Forum::visible()->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'ranks'  => Rank::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),

        ]);
    }

    /**
     * Shows the edit forum forum.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditForum($id) {
        $forum = Forum::find($id);
        if (!$forum) {
            abort(404);
        }

        return view('admin.forums.create_edit_forum', [
            'forum'  => $forum,
            'forums' => Forum::visible()->orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
            'ranks'  => Rank::orderBy('sort', 'DESC')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits a forum.
     *
     * @param App\Services\ForumService $service
     * @param int|null                  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditForum(Request $request, ForumService $service, $id = null) {
        $id ? $request->validate(Forum::$updateRules) : $request->validate(Forum::$createRules);
        $data = $request->only([
            'name', 'description', 'layout', 'is_active', 'is_locked', 'staff_only', 'sort', 'role_limit', 'parent_id', 'image', 'remove_image',
            'color', 'icon', 'remove_icon', 'characters_enabled', 'forum_rules', 'forum_styles',
        ]);

        if ($id && $service->updateForum(Forum::find($id), $data, Auth::user())) {
            flash('Forum updated successfully.')->success();
        } elseif (!$id && $forum = $service->createForum($data, Auth::user())) {
            flash('Forum created successfully.')->success();

            return redirect()->to('admin/forums/edit/'.$forum->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the forum deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteForum($id) {
        $forum = Forum::find($id);

        return view('admin.forums._delete_forum', [
            'forum' => $forum,
        ]);
    }

    /**
     * Deletes a forum.
     *
     * @param App\Services\ForumService $service
     * @param int                       $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteForum(Request $request, ForumService $service, $id) {
        $data = $request->only(['child_boards']);
        if ($id && $service->deleteForum(Forum::find($id), $data)) {
            flash('Forum deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/forums');
    }

    /**********************************************************************************************

        FORUM FLAIRS

    **********************************************************************************************/

    /**
     * Shows the forum flair index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getFlairIndex(Request $request) {
        $query = ForumFlair::query();

        if ($request->get('name')) {
            $query->where('name', 'LIKE', '%'.$request->get('name').'%');
        }

        return view('admin.forums.forum_flairs', [
            'flairs' => $query->sortNewest()->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the create forum flair page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateFlair() {
        return view('admin.forums.create_edit_forum_flair', [
            'flair' => new ForumFlair,
        ]);
    }

    /**
     * Shows the edit forum flair page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditFlair($id) {
        $flair = ForumFlair::find($id);
        if (!$flair) {
            abort(404);
        }

        return view('admin.forums.create_edit_forum_flair', [
            'flair' => $flair,
        ]);
    }

    /**
     * Creates or edits a forum flair.
     *
     * @param App\Services\ForumService $service
     * @param int|null                  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditFlair(Request $request, ForumService $service, $id = null) {
        $id ? $request->validate(ForumFlair::$updateRules) : $request->validate(ForumFlair::$createRules);

        $data = $request->only([
            'name', 'post_requirement', 'description', 'color', 'bg_color', 'image', 'remove_image',
            'staff_only', 'is_default', 'is_visible', 'text_shadow_x', 'text_shadow_y', 'text_shadow_blur', 'text_shadow_color',
        ]);

        if ($id && $service->updateForumFlair(ForumFlair::find($id), $data, Auth::user())) {
            flash('Forum flair updated successfully.')->success();
        } elseif (!$id && $flair = $service->createForumFlair($data, Auth::user())) {
            flash('Forum flair created successfully.')->success();

            return redirect()->to('admin/forum-flairs/edit/'.$flair->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the forum flair deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteFlair($id) {
        $flair = ForumFlair::find($id);

        return view('admin.forums._delete_forum_flair', [
            'flair' => $flair,
        ]);
    }

    /**
     * Deletes a forum flair.
     *
     * @param App\Services\ForumService $service
     * @param int                       $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteFlair(Request $request, ForumService $service, $id) {
        if ($id && $service->deleteForumFlair(ForumFlair::find($id))) {
            flash('Forum flair deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/forum-flairs');
    }

    /**********************************************************************************************

        FORUM DECORS

    **********************************************************************************************/

    /**
     * Shows the forum decor index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDecorIndex(Request $request) {
        $query = ForumDecor::query();

        if ($request->get('name')) {
            $query->where('name', 'LIKE', '%'.$request->get('name').'%');
        }

        if ($request->get('type')) {
            $query->where('type', $request->get('type'));
        }

        return view('admin.forums.forum_decors', [
            'decors' => $query->sortNewest()->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the create forum decor page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateDecor() {
        return view('admin.forums.create_edit_forum_decor', [
            'decor' => new ForumDecor,
        ]);
    }

    /**
     * Shows the edit forum decor page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditDecor($id) {
        $decor = ForumDecor::find($id);
        if (!$decor) {
            abort(404);
        }

        return view('admin.forums.create_edit_forum_decor', [
            'decor' => $decor,
        ]);
    }

    /**
     * Creates or edits a forum decor.
     *
     * @param App\Services\ForumService $service
     * @param int|null                  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditDecor(Request $request, ForumService $service, $id = null) {
        $id ? $request->validate(ForumDecor::$updateRules) : $request->validate(ForumDecor::$createRules);

        $data = $request->only([
            'name', 'type', 'description', 'image', 'remove_image',
            'staff_only', 'is_default', 'is_visible',
            'opacity', 'background_size', 'background_repeat',
            'border_image_slice', 'border_image_width', 'border_image_outset', 'border_image_repeat',
        ]);

        if ($id && $service->updateForumDecor(ForumDecor::find($id), $data, Auth::user())) {
            flash('Forum decor updated successfully.')->success();
        } elseif (!$id && $decor = $service->createForumDecor($data, Auth::user())) {
            flash('Forum decor created successfully.')->success();

            return redirect()->to('admin/forum-decors/edit/'.$decor->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the forum decor deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteDecor($id) {
        $decor = ForumDecor::find($id);

        return view('admin.forums._delete_forum_decor', [
            'decor' => $decor,
        ]);
    }

    /**
     * Deletes a forum decor.
     *
     * @param App\Services\ForumService $service
     * @param int                       $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteDecor(Request $request, ForumService $service, $id) {
        if ($id && $service->deleteForumDecor(ForumDecor::find($id))) {
            flash('Forum decor deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/forum-decors');
    }
}
