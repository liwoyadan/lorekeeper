<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Forum\ForumDecor;
use App\Models\Forum\ForumFlair;
use App\Models\Notification;
use App\Models\User\User;
use App\Models\User\UserAlias;
use App\Services\ForumService;
use App\Services\LinkService;
use App\Services\UserService;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\RecoveryCode;

class AccountController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Account Controller
    |--------------------------------------------------------------------------
    |
    | Handles the user's account management.
    |
    */

    /**
     * Shows the banned page, or redirects the user to the home page if they aren't banned.
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function getBanned() {
        if (Auth::user()->is_banned) {
            return view('account.banned');
        } else {
            return redirect()->to('/');
        }
    }

    /**
     * Shows the deactivation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeactivated() {
        if (!Auth::user()->is_deactivated) {
            return redirect()->to('/');
        } else {
            return view('account.deactivated');
        }
    }

    /**
     * Shows the user settings page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSettings() {
        $user = Auth::user();

        $staffFlairs = ForumFlair::isStaff()->default(0)->sortAlphabetical()->pluck('name', 'id')->toArray();
        $defaultFlairs = ForumFlair::visible($user)->default()->sortAlphabetical()->pluck('name', 'id')->toArray();
        $obtainedFlairs = ForumFlair::visible($user)->default(0)->whereIn('id', $user->forumFlairs()->pluck('forum_flair_id')->toArray())->sortAlphabetical()->pluck('name', 'id')->toArray();

        $obtainedDecorIds = $user->forumDecors()->pluck('forum_decor_id')->toArray();
        $decorOptionsByType = [];
        foreach (array_keys(config('lorekeeper.forums.decor_types', [])) as $typeKey) {
            $staffDecors = ForumDecor::isStaff()->default(0)->where('type', $typeKey)->sortAlphabetical()->get()->pluck('fullName', 'id')->toArray();
            $defaultDecors = ForumDecor::visible($user)->default()->where('type', $typeKey)->sortAlphabetical()->get()->pluck('fullName', 'id')->toArray();
            $obtainedDecors = ForumDecor::visible($user)->default(0)->where('type', $typeKey)->whereIn('id', $obtainedDecorIds)->sortAlphabetical()->get()->pluck('fullName', 'id')->toArray();
            $decorOptionsByType[$typeKey] = ($user->isStaff ? ['Staff Decors' => $staffDecors] : []) + ['Default Decors' => $defaultDecors, 'Obtained Decors' => $obtainedDecors];
        }

        return view('account.settings', [
            'flairOptions' => ($user->isStaff ? ['Staff Flairs' => $staffFlairs] : []) + ['Default Flairs' => $defaultFlairs, 'Obtained Flairs' => $obtainedFlairs],
            'decorOptionsByType' => $decorOptionsByType,
        ]);
    }

    /**
     * Edits the user's profile.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postProfile(Request $request) {
        Auth::user()->profile->update([
            'text'        => $request->get('text'),
            'parsed_text' => parse($request->get('text')),
        ]);
        flash('Profile updated successfully.')->success();

        return redirect()->back();
    }

    /**
     * Edits the user's forum settings.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postForumSettings(Request $request) {
        $user = Auth::user();
        $data = $request->validate([
            'forum_signature' => 'nullable|string|max:1000',
            'forum_flair_id'  => 'nullable|exists:forum_flairs,id',
            'forum_decor'     => 'nullable|array',
            'forum_decor.*'   => 'nullable|exists:forum_decors,id',
        ]);

        if (isset($data['forum_flair_id']) && $data['forum_flair_id']) {
            $flair = ForumFlair::find($data['forum_flair_id']);
            $canAccessFlair = false;
            if ($flair) {
                if ($flair->is_staff && $user->isStaff) {
                    $canAccessFlair = true;
                } elseif ($flair->is_default) {
                    $canAccessFlair = true;
                } elseif ($user->forumFlairs()->where('forum_flair_id', $flair->id)->exists()) {
                    $canAccessFlair = true;
                }
            }

            if (!$canAccessFlair) {
                flash('You do not have access to the selected forum flair.')->error();
            }
        }

        $forumDecor = [];
        $decorTypes = config('lorekeeper.forums.decor_types', []);
        foreach ($decorTypes as $typeKey => $typeLabel) {
            $decorId = $data['forum_decor'][$typeKey] ?? null;
            if (!$decorId) {
                continue;
            }

            $decor = ForumDecor::find($decorId);
            $canAccess = false;
            if ($decor) {
                if ($decor->staff_only && $user->isStaff) {
                    $canAccess = true;
                } elseif ($decor->is_default) {
                    $canAccess = true;
                } elseif ($user->forumDecors()->where('forum_decor_id', $decor->id)->exists()) {
                    $canAccess = true;
                }
            }

            if (!$canAccess) {
                flash("You do not have access to the selected {$typeLabel} decoration.")->error();
            } else {
                $forumDecor[$typeKey] = $decorId;
            }
        }

        $user->profile->update([
            'forum_signature'        => $data['forum_signature'] ?? null,
            'parsed_forum_signature' => (isset($data['forum_signature']) && $data['forum_signature']) ? parse($data['forum_signature']) : null,
            'forum_flair_id'         => (isset($canAccessFlair) && $canAccessFlair) ? $data['forum_flair_id'] : null,
            'forum_decor'            => $forumDecor ?: null,
        ]);

        flash('Forum settings updated successfully.')->success();

        return redirect()->back();
    }

    /**
     * Uploads or removes the user's custom forum post background.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postForumBackground(Request $request, ForumService $service) {
        $request->validate([
            'forum_bg'         => 'nullable|image|mimes:png,jpg,jpeg,gif,webp',
            'forum_bg_opacity' => 'nullable|integer|min:0|max:100',
        ]);

        if ($service->updateForumBackground($request->file('forum_bg'), $request->boolean('remove_forum_bg'), Auth::user(), $request->input('forum_bg_opacity'))) {
            flash('Forum background updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Edits the user's avatar.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAvatar(Request $request, UserService $service) {
        if ($service->updateAvatar($request->file('avatar'), Auth::user())) {
            flash('Avatar updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Edits the user's username.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUsername(Request $request, UserService $service) {
        if ($service->updateUsername($request->get('username'), Auth::user())) {
            flash('Username updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Changes the user's password.
     *
     * @param App\Services\UserService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postPassword(Request $request, UserService $service) {
        $user = Auth::user();
        if (!isset($user->password) && (!isset($user->email) || !isset($user->email_verified_at))) {
            flash('Please set and verify an email before setting a password for email login.')->error();

            return redirect()->back();
        }

        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ] + (isset($user->password) ? ['old_password' => 'required|string'] : []));
        if ($service->updatePassword($request->only(['old_password', 'new_password', 'new_password_confirmation']), Auth::user())) {
            flash('Password updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Changes the user's email address and sends a verification email.
     *
     * @param App\Services\UserService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEmail(Request $request, UserService $service) {
        $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
        ]);
        if ($service->updateEmail($request->only(['email']), Auth::user())) {
            flash('Email updated successfully. A verification email has been sent to your new email address.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Changes user birthday setting.
     *
     * @param App\Services\UserService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postBirthday(Request $request, UserService $service) {
        if ($service->updateBirthdayVisibilitySetting($request->input('birthday_setting'), Auth::user())) {
            flash('Setting updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Enables the user's two factor auth.
     *
     * @param App\Services\UserService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEnableTwoFactor(Request $request, UserService $service) {
        if (!$request->session()->put([
            'two_factor_secret'         => encrypt(app(TwoFactorAuthenticationProvider::class)->generateSecretKey()),
            'two_factor_recovery_codes' => encrypt(json_encode(Collection::times(8, function () {
                return RecoveryCode::generate();
            })->all())),
        ])) {
            flash('2FA info generated. Please confirm to enable 2FA.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('account/two-factor/confirm');
    }

    /**
     * Shows the confirm two-factor auth page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getConfirmTwoFactor(Request $request) {
        // Assemble URL and QR Code svg from session information
        $qrUrl = app(TwoFactorAuthenticationProvider::class)->qrCodeUrl(config('app.name'), Auth::user()->email, decrypt($request->session()->get('two_factor_secret')));
        $qrCode = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd
            )
        ))->writeString($qrUrl);
        $qrCode = trim(substr($qrCode, strpos($qrCode, "\n") + 1));

        return view('auth.confirm_two_factor', [
            'qrCode'        => $qrCode,
            'recoveryCodes' => json_decode(decrypt($request->session()->get('two_factor_recovery_codes'))),
        ]);
    }

    /**
     * Confirms and fully enables the user's two factor auth.
     *
     * @param App\Services\UserService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postConfirmTwoFactor(Request $request, UserService $service) {
        $request->validate([
            'code' => 'required',
        ]);
        if ($service->confirmTwoFactor($request->only(['code']), $request->session()->only(['two_factor_secret', 'two_factor_recovery_codes']), Auth::user())) {
            flash('2FA enabled succesfully.')->success();
            $request->session()->forget(['two_factor_secret', 'two_factor_recovery_codes']);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('account/settings');
    }

    /**
     * Confirms and disables the user's two factor auth.
     *
     * @param App\Services\UserService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDisableTwoFactor(Request $request, UserService $service) {
        $request->validate([
            'code' => 'required',
        ]);
        if ($service->disableTwoFactor($request->only(['code']), Auth::user())) {
            flash('2FA disabled succesfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows the notifications page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getNotifications() {
        $notifications = Auth::user()->notifications()->orderBy('id', 'DESC')->paginate(30);
        Auth::user()->notifications()->update(['is_unread' => 0]);
        Auth::user()->notifications_unread = 0;
        Auth::user()->save();

        return view('account.notifications', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Deletes a notification and returns a response.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Http\Response
     */
    public function getDeleteNotification($id) {
        $notification = Notification::where('id', $id)->where('user_id', Auth::user()->id)->first();
        if ($notification) {
            $notification->delete();
        }

        return response(200);
    }

    /**
     * Deletes all of the user's notifications.
     *
     * @param mixed|null $type
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postClearNotifications($type = null) {
        if (isset($type)) {
            Auth::user()->notifications()->where('notification_type_id', $type)->delete();
        } else {
            Auth::user()->notifications()->delete();
        }
        flash('Notifications cleared successfully.')->success();

        return redirect()->back();
    }

    /**
     * Shows the account links page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getAliases() {
        return view('account.aliases');
    }

    /**
     * Shows the make primary alias modal.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMakePrimary($id) {
        return view('account._make_primary_modal', ['alias' => UserAlias::where('id', $id)->where('user_id', Auth::user()->id)->first()]);
    }

    /**
     * Makes an alias the user's primary alias.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postMakePrimary(LinkService $service, $id) {
        if ($service->makePrimary($id, Auth::user())) {
            flash('Your primary alias has been changed successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows the hide alias modal.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getHideAlias($id) {
        return view('account._hide_alias_modal', ['alias' => UserAlias::where('id', $id)->where('user_id', Auth::user()->id)->first()]);
    }

    /**
     * Hides or unhides the selected alias from public view.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postHideAlias(LinkService $service, $id) {
        if ($service->hideAlias($id, Auth::user())) {
            flash('Your alias\'s visibility setting has been changed successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows the remove alias modal.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRemoveAlias($id) {
        return view('account._remove_alias_modal', ['alias' => UserAlias::where('id', $id)->where('user_id', Auth::user()->id)->first()]);
    }

    /**
     * Removes the selected alias from the user's account.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRemoveAlias(LinkService $service, $id) {
        if ($service->removeAlias($id, Auth::user())) {
            flash('Your alias has been removed successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Show a user's deactivate confirmation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeactivate() {
        return view('account.deactivate');
    }

    /**
     * Show a user's deactivate confirmation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeactivateConfirmation() {
        return view('account._deactivate_confirmation');
    }

    public function postDeactivate(Request $request, UserService $service) {
        $wasDeactivated = Auth::user()->is_deactivated;
        if ($service->deactivate(['deactivate_reason' => $request->get('deactivate_reason')], Auth::user(), null)) {
            flash($wasDeactivated ? 'Deactivation reason edited successfully.' : 'Your account has successfully been deactivated. We hope to see you again and wish you the best!')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Show a user's reactivate confirmation page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getReactivateConfirmation() {
        return view('account._reactivate_confirmation');
    }

    public function postReactivate(Request $request, UserService $service) {
        if ($service->reactivate(Auth::user(), null)) {
            flash('You have reactivated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
