<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Changelog;
use App\Services\ChangelogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ChangelogController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Changelog Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of changelog entries.
    |
    */

    /**
     * Shows the changelog index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request) {
        $query = Changelog::query();
        $data = $request->only(['type', 'text']);

        if (isset($data['type']) && $data['type']) {
            $query->where('type', $data['type']);
        }
        if (isset($data['text']) && $data['text']) {
            $query->where(function ($q) use ($data) {
                $q->where('text', 'LIKE', '%'.$data['text'].'%')
                    ->orWhere('parsed_text', 'LIKE', '%'.$data['text'].'%');
            });
        }

        return view('admin.changelogs.changelogs', [
            'changelogs' => $query->orderBy('created_at', 'DESC')->paginate(20)->appends($request->query()),
            'types'      => ['' => 'Any Type'] + config('lorekeeper.changelogs.subject_types'),
        ]);
    }

    /**
     * Shows the create changelog page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateChangelog() {
        return view('admin.changelogs.create_edit_changelog', [
            'changelog'      => new Changelog,
            'types'          => config('lorekeeper.changelogs.subject_types'),
            'subjectOptions' => null,
        ]);
    }

    /**
     * Shows the edit changelog page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditChangelog($id) {
        $changelog = Changelog::find($id);
        if (!$changelog) {
            abort(404);
        }

        return view('admin.changelogs.create_edit_changelog', [
            'changelog'      => $changelog,
            'types'          => config('lorekeeper.changelogs.subject_types'),
            'subjectOptions' => $this->buildSubjectOptions($changelog->type),
        ]);
    }

    /**
     * Gets the subject options partial for a given type.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSubjectOptions(Request $request) {
        if (!class_exists($request->input('type'))) {
            return '';
        }
        
        return view('admin.changelogs._subject_options', [
            'options'  => $this->buildSubjectOptions($request->input('type')),
            'selected' => $request->input('selected'),
        ]);
    }

    /**
     * Builds the subject options list for a given type class.
     *
     * @param string|null $type
     *
     * @return array
     */
    private function buildSubjectOptions($type) {
        $options = [];
        if ($type && array_key_exists($type, config('lorekeeper.changelogs.subject_types')) && Changelog::typeIsModel($type)) {
            $column = in_array($type, config('lorekeeper.changelogs.title_columns') ?? []) ? 'title' : 'name';
            $options += $type::orderBy($column)->pluck($column, 'id')->toArray();
        }

        return $options;
    }

    /**
     * Creates or edits a changelog.
     *
     * @param App\Services\ChangelogService $service
     * @param int|null                      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditChangelog(Request $request, ChangelogService $service, $id = null) {
        $rules = Changelog::$rules;
        $rules['type'] = ['required', 'string', Rule::in(array_keys(config('lorekeeper.changelogs.subject_types')))];
        $request->validate($rules);
        $data = $request->only([
            'type', 'type_id', 'text', 'staff_only',
        ]);
        $changelog = $id ? Changelog::find($id) : null;
        if ($id && $changelog && $service->updateChangelog($changelog, $data, Auth::user())) {
            flash('Changelog updated successfully.')->success();
        } elseif ($id && !$changelog) {
            flash('Invalid changelog selected.')->error();
        } elseif (!$id && $created = $service->createChangelog($data, Auth::user())) {
            flash('Changelog created successfully.')->success();

            return redirect()->to('admin/changelogs/edit/'.$created->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the changelog deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteChangelog($id) {
        $changelog = Changelog::find($id);

        return view('admin.changelogs._delete_changelog', [
            'changelog' => $changelog,
        ]);
    }

    /**
     * Deletes a changelog.
     *
     * @param App\Services\ChangelogService $service
     * @param int                           $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteChangelog(Request $request, ChangelogService $service, $id) {
        $changelog = Changelog::find($id);
        if ($changelog && $service->deleteChangelog($changelog, Auth::user())) {
            flash('Changelog deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] ?? ['Invalid changelog selected.'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/changelogs');
    }
}
