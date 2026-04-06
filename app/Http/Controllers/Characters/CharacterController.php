<?php

namespace App\Http\Controllers\Characters;

use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Models\Character\Character;
use App\Models\Character\CharacterCurrency;
use App\Models\Character\CharacterItem;
use App\Models\Character\CharacterProfile;
use App\Models\Character\CharacterRelation;
use App\Models\Character\RelationsLog;
use App\Models\Character\CharacterTransfer;
use App\Models\Currency\Currency;
use App\Models\Gallery\GallerySubmission;
use App\Models\Item\Item;
use App\Models\Item\ItemCategory;
use App\Models\Item\ItemTag;
use App\Models\User\User;
use App\Models\User\UserCurrency;
use App\Models\User\UserItem;
use App\Services\CharacterLinkService;
use App\Services\CharacterManager;
use App\Services\CurrencyManager;
use App\Services\DesignUpdateManager;
use App\Services\InventoryManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Route;

class CharacterController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Character Controller
    |--------------------------------------------------------------------------
    |
    | Handles displaying and acting on a character.
    |
    */

    /**
     * Create a new controller instance.
     */
    public function __construct() {
        $this->middleware(function ($request, $next) {
            $slug = Route::current()->parameter('slug');
            $query = Character::myo(0)->where('slug', $slug);
            if (!(Auth::check() && Auth::user()->hasPower('manage_characters'))) {
                $query->where('is_visible', 1);
            }
            $this->character = $query->first();
            if (!$this->character) {
                abort(404);
            }

            $this->character->updateOwner();

            if (config('lorekeeper.extensions.previous_and_next_characters.display')) {
                $query = Character::myo(0);
                // Get only characters of this category if pull number is limited to category
                if (config('lorekeeper.settings.character_pull_number') === 'category') {
                    $query->where('character_category_id', $this->character->character_category_id);
                }

                if (!(Auth::check() && Auth::user()->hasPower('manage_characters'))) {
                    $query->where('is_visible', 1);
                }

                // Get the previous and next characters, if they exist
                $prevCharName = null;
                $prevCharUrl = null;
                $nextCharName = null;
                $nextCharUrl = null;

                if ($query->count()) {
                    $characters = $query->orderBy('number', 'DESC')->get();

                    // Filter
                    $lowerChar = $characters->where('number', '<', $this->character->number)->first();
                    $higherChar = $characters->where('number', '>', $this->character->number)->last();
                }

                if (config('lorekeeper.extensions.previous_and_next_characters.reverse') == 0) {
                    $nextCharacter = $lowerChar;
                    $previousCharacter = $higherChar;
                } else {
                    $previousCharacter = $lowerChar;
                    $nextCharacter = $higherChar;
                }

                if (!$previousCharacter || $previousCharacter->id == $this->character->id) {
                    $previousCharacter = null;
                } else {
                    $prevCharName = $previousCharacter->fullName;
                    $prevCharUrl = $previousCharacter->url;
                }

                if (!$nextCharacter || $nextCharacter->id == $this->character->id) {
                    $nextCharacter = null;
                } else {
                    $nextCharName = $nextCharacter->fullName;
                    $nextCharUrl = $nextCharacter->url;
                }

                $extPrevAndNextBtns = ['prevCharName' => $prevCharName, 'prevCharUrl' => $prevCharUrl, 'nextCharName' => $nextCharName, 'nextCharUrl' => $nextCharUrl];
                View::share('extPrevAndNextBtns', $extPrevAndNextBtns);
            }

            return $next($request);
        });
    }

    /**
     * Shows a character's masterlist entry.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacter($slug) {
        return view('character.character', [
            'character'             => $this->character,
            'showMention'           => true,
            'extPrevAndNextBtnsUrl' => '',
        ]);
    }

    /**
     * Shows a character's profile.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterProfile($slug) {
        return view('character.profile', [
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/profile',
            'featuredLinks'         => $this->character->featuredLinks()->with('characterOne', 'characterTwo')->get(),
        ]);
    }

    /**
     * Shows a character's edit profile page.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterProfile($slug) {
        if (!Auth::check()) {
            abort(404);
        }

        $isMod = Auth::user()->hasPower('manage_characters');
        $isOwner = ($this->character->user_id == Auth::user()->id);
        if (!$isMod && !$isOwner) {
            abort(404);
        }

        return view('character.edit_profile', [
            'character' => $this->character,
        ]);
    }

    /**
     * Edits a character's profile.
     *
     * @param App\Services\CharacterManager $service
     * @param string                        $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditCharacterProfile(Request $request, CharacterManager $service, $slug) {
        if (!Auth::check()) {
            abort(404);
        }

        $isMod = Auth::user()->hasPower('manage_characters');
        $isOwner = ($this->character->user_id == Auth::user()->id);
        if (!$isMod && !$isOwner) {
            abort(404);
        }

        $request->validate(CharacterProfile::$rules);

        if ($service->updateCharacterProfile($request->only(['name', 'link', 'text', 'is_gift_art_allowed', 'is_gift_writing_allowed', 'is_trading', 'alert_user', 'is_links_open']), $this->character, Auth::user(), !$isOwner)) {
            flash('Profile edited successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows a character's gallery.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterGallery(Request $request, $slug) {
        return view('character.gallery', [
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/gallery',
            'submissions'           => GallerySubmission::whereIn('id', $this->character->gallerySubmissions->pluck('gallery_submission_id')->toArray())->visible(Auth::user() ?? null)->orderBy('created_at', 'DESC')->paginate(20),
        ]);
    }

    /**
     * Shows a character's images.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterImages($slug) {
        return view('character.images', [
            'user'                  => Auth::check() ? Auth::user() : null,
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/images',
        ]);
    }

    /**
     * Shows a character's inventory.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterInventory($slug) {
        $categories = ItemCategory::visible(Auth::check() ? Auth::user() : null)->where('is_character_owned', '1')->orderBy('sort', 'DESC')->get();
        $itemOptions = Item::whereIn('item_category_id', $categories->pluck('id'));

        $items = count($categories) ?
            $this->character->items()
                ->with('category')
                ->where('count', '>', 0)
                ->orderByRaw('FIELD(item_category_id,'.implode(',', $categories->pluck('id')->toArray()).')')
                ->orderBy('name')
                ->orderBy('updated_at')
                ->get()
                ->groupBy(['item_category_id', 'id']) :
            $this->character->items()
                ->with('category')
                ->where('count', '>', 0)
                ->orderBy('name')
                ->orderBy('updated_at')
                ->get()
                ->groupBy(['item_category_id', 'id']);

        return view('character.inventory', [
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/inventory',
            'categories'            => $categories->keyBy('id'),
            'items'                 => $items,
            'logs'                  => $this->character->getItemLogs(),
        ] + (Auth::check() && (Auth::user()->hasPower('edit_inventories') || Auth::user()->id == $this->character->user_id) ? [
            'itemOptions' => $itemOptions->pluck('name', 'id'),
            'page'        => 'character',
        ] : []));
    }

    /**
     * Shows a character's bank.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterBank($slug) {
        $character = $this->character;

        return view('character.bank', [
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/bank',
            'currencies'            => $character->getCurrencies(true),
            'logs'                  => $this->character->getCurrencyLogs(),
        ] + (Auth::check() && Auth::user()->id == $this->character->user_id ? [
            'takeCurrencyOptions' => Currency::where('allow_character_to_user', 1)->where('is_user_owned', 1)->where('is_character_owned', 1)->whereIn('id', CharacterCurrency::where('character_id', $this->character->id)->pluck('currency_id')->toArray())->orderBy('sort_character', 'DESC')->pluck('name', 'id')->toArray(),
            'giveCurrencyOptions' => Currency::where('allow_user_to_character', 1)->where('is_user_owned', 1)->where('is_character_owned', 1)->whereIn('id', UserCurrency::where('user_id', Auth::user()->id)->pluck('currency_id')->toArray())->orderBy('sort_user', 'DESC')->pluck('name', 'id')->toArray(),

        ] : []) + (Auth::check() && (Auth::user()->hasPower('edit_inventories') || Auth::user()->id == $this->character->user_id) ? [
            'currencyOptions' => Currency::where('is_character_owned', 1)->orderBy('sort_character', 'DESC')->pluck('name', 'id')->toArray(),
        ] : []));
    }

    /**
     * Transfers currency between the user and character.
     *
     * @param App\Services\CharacterManager $service
     * @param string                        $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCurrencyTransfer(Request $request, CurrencyManager $service, $slug) {
        if (!Auth::check()) {
            abort(404);
        }

        $action = $request->get('action');
        $sender = ($action == 'take') ? $this->character : Auth::user();
        $recipient = ($action == 'take') ? Auth::user() : $this->character;

        if ($service->transferCharacterCurrency($sender, $recipient, Currency::where(($action == 'take') ? 'allow_character_to_user' : 'allow_user_to_character', 1)->where('id', $request->get(($action == 'take') ? 'take_currency_id' : 'give_currency_id'))->first(), $request->get('quantity'))) {
            flash('Currency transferred successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Handles inventory item processing, including transferring items between the user and character.
     *
     * @param App\Services\CharacterManager $service
     * @param string                        $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postInventoryEdit(Request $request, InventoryManager $service, $slug) {
        if (!Auth::check()) {
            abort(404);
        }
        switch ($request->get('action')) {
            default:
                flash('Invalid action selected.')->error();
                break;
            case 'give':
                $sender = Auth::user();
                $recipient = $this->character;

                if ($service->transferCharacterStack($sender, $recipient, UserItem::find($request->get('stack_id')), $request->get('stack_quantity'), Auth::user())) {
                    flash('Item transferred successfully.')->success();
                } else {
                    foreach ($service->errors()->getMessages()['error'] as $error) {
                        flash($error)->error();
                    }
                }
                break;
            case 'name':
                return $this->postName($request, $service);
                break;
            case 'delete':
                return $this->postDelete($request, $service);
                break;
            case 'take':
                return $this->postItemTransfer($request, $service);
                break;
        }

        return redirect()->back();
    }

    /**
     * Shows a character's currency logs.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterCurrencyLogs($slug) {
        return view('character.currency_logs', [
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/currency-logs',
            'logs'                  => $this->character->getCurrencyLogs(0),
        ]);
    }

    /**
     * Shows a character's item logs.
     *
     * @param mixed $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterItemLogs($slug) {
        return view('character.item_logs', [
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/item-logs',
            'logs'                  => $this->character->getItemLogs(0),
        ]);
    }

    /**
     * Shows a character's ownership logs.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterOwnershipLogs($slug) {
        return view('character.ownership_logs', [
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/ownership',
            'logs'                  => $this->character->getOwnershipLogs(0),
        ]);
    }

    /**
     * Shows a character's ownership logs.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterLogs($slug) {
        return view('character.character_logs', [
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/change-log',
            'logs'                  => $this->character->getCharacterLogs(),
        ]);
    }

    /**
     * Shows a character's submissions.
     *
     * @param mixed $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterSubmissions($slug) {
        return view('character.submission_logs', [
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/submissions',
            'logs'                  => $this->character->getSubmissions(),
        ]);
    }

    /**
     * Shows a character's transfer page.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTransfer($slug) {
        if (!Auth::check()) {
            abort(404);
        }

        $isMod = Auth::user()->hasPower('manage_characters');
        $isOwner = ($this->character->user_id == Auth::user()->id);
        if (!$isMod && !$isOwner) {
            abort(404);
        }

        return view('character.transfer', [
            'character'      => $this->character,
            'transfer'       => CharacterTransfer::active()->where('character_id', $this->character->id)->first(),
            'cooldown'       => Settings::get('transfer_cooldown'),
            'transfersQueue' => Settings::get('open_transfers_queue'),
            'userOptions'    => User::visible()->orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Opens a transfer request for a character.
     *
     * @param App\Services\CharacterManager $service
     * @param string                        $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postTransfer(Request $request, CharacterManager $service, $slug) {
        if (!Auth::check()) {
            abort(404);
        }

        if ($service->createTransfer($request->only(['recipient_id', 'user_reason']), $this->character, Auth::user())) {
            flash('Transfer created successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Cancels a transfer request for a character.
     *
     * @param App\Services\CharacterManager $service
     * @param string                        $slug
     * @param int                           $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCancelTransfer(Request $request, CharacterManager $service, $slug, $id) {
        if (!Auth::check()) {
            abort(404);
        }

        if ($service->cancelTransfer(['transfer_id' => $id], Auth::user())) {
            flash('Transfer cancelled.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows a character's design update approval page.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterApproval($slug) {
        if (!Auth::check() || $this->character->user_id != Auth::user()->id) {
            abort(404);
        }

        return view('character.update_form', [
            'character' => $this->character,
            'queueOpen' => Settings::get('is_design_updates_open'),
            'request'   => $this->character->designUpdate()->active()->first(),
        ]);
    }

    /**
     * Opens a new design update approval request for a character.
     *
     * @param App\Services\DesignUpdateManager $service
     * @param string                           $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCharacterApproval($slug, DesignUpdateManager $service) {
        if (!Auth::check() || $this->character->user_id != Auth::user()->id) {
            abort(404);
        }

        if ($request = $service->createDesignUpdateRequest($this->character, Auth::user())) {
            flash('Successfully created new design update request draft.')->success();

            return redirect()->to($request->url);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**********************************************************************************************

        LINKS

    **********************************************************************************************/

    /**
     * Shows a character's links page.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterLinks($slug) {
        return view('character.links', [
            'character' => $this->character,
            'links'     => $this->character->sortedLinks(),
        ]);
    }

    /**
     * Shows a character's edit links page.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateEditCharacterLinks($slug) {
        if (!Auth::check()) {
            abort(404);
        }

        if (Auth::user()->id != $this->character->user_id && !Auth::user()->hasPower('manage_characters')) {
            abort(404);
        }

        $tags = ItemTag::where('tag', 'link')->where('is_active', 1)->get()->pluck('item_id');
        $linkItems = UserItem::where('user_id', $this->character->user_id)->whereIn('item_id', $tags)->where('count', '>', 0)->with('item')->get()->pluck('item.name', 'id');

        return view('character.edit_links', [
            'character' => $this->character,
            'linkItems' => $linkItems,
        ]);
    }

    /**
     * Creates / requests character links.
     *
     * @param App\Services\CharacterManager $service
     * @param string                        $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditCharacterLinks(Request $request, CharacterLinkService $service, $slug) {
        if (!Auth::check()) {
            abort(404);
        }

        $isStaff = Auth::user()->hasPower('manage_characters') && ($this->character->user_id != Auth::user()->id);
        $isOwner = ($this->character->user_id == Auth::user()->id);
        if (!$isStaff && !$isOwner) {
            abort(404);
        }
        $data = $request->only(['slug', 'link_item_id']);

        if ($service->createCharacterRelationLinks($this->character, $data, Auth::user(), $isStaff)) {
            flash('Link requested successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Edits a character's link info.
     *
     * @param App\Services\CharacterManager $service
     * @param string                        $slug
     * @param mixed                         $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditCharacterLinkInfo(Request $request, CharacterLinkService $service, $slug, $id) {
        if (Auth::user()->id != $this->character->user_id && !Auth::user()->hasPower('manage_characters')) {
            abort(404);
        }

        $data = $request->only(['info', 'type']);
        if ($service->updateCharacterRelationLinkInfo($data + ['slug' => $slug], $id, Auth::user())) {
            flash('Link info updated successfully!')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the delete character relationship modal.
     *
     * @param mixed $slug
     * @param mixed $id
     */
    public function getDeleteCharacterLink($slug, $id) {
        if (Auth::user()->id != $this->character->user_id && !Auth::user()->hasPower('manage_characters')) {
            abort(404);
        }

        $link = CharacterRelation::find($id);
        if (!$link) {
            abort(404);
        }

        return view('character._delete_link', [
            'link'      => $link,
            'character' => $this->character,
        ]);
    }

    /**
     * deletes a character relationship link.
     *
     * @param string $slug
     * @param mixed  $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function postDeleteCharacterLink(Request $request, CharacterLinkService $service, $slug, $id) {
        if (Auth::user()->id != $this->character->user_id && !Auth::user()->hasPower('manage_characters')) {
            abort(404);
        }

        if ($service->deleteCharacterRelationLink($id)) {
            flash('Link deleted successfully!')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Saves the link sort order for the character.
     *
     * @param string $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSortCharacterLinks(Request $request, CharacterLinkService $service, $slug) {
        if (Auth::user()->id != $this->character->user_id && !Auth::user()->hasPower('manage_characters')) {
            abort(404);
        }

        if ($service->sortCharacterLinks($this->character, $request->get('sort'))) {
            flash('Link order saved.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows a character's relationship logs.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCharacterLinkLogs($slug) {
        return view('character.link_logs', [
            'character'             => $this->character,
            'extPrevAndNextBtnsUrl' => '/'.__('links.links').'-logs',
            'logs'                  => $this->character->getLinkLogs(0),
        ]);
    }

    /**
     * Shows the edit relationship info modal.
     *
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditCharacterLinkInfo(Request $request, $slug, $id) {
        if (!Auth::check()) {
            abort(404);
        }
        if (Auth::user()->id != $this->character->user_id && !Auth::user()->hasPower('manage_characters')) {
            abort(404);
        }

        $link = CharacterRelation::find($id);
        if (!$link) {
            abort(404);
        }

        $otherCharacter = $link->getOtherCharacter($this->character->id);
        $itemId = $link->userItem?->item_id;
        $types = null;
        if ($itemId) {
            $tag = ItemTag::where('item_id', $itemId)->where('tag', 'link')->first();
            if ($tag) {
                $tagData = $tag->getData()['links'];
                if ($tagData) {
                    $types = array_combine($tagData, $tagData) ?: null;
                }
            }
        }

        return view('character._edit_link_modal', [
            'character'      => $this->character,
            'link'           => $link,
            'otherCharacter' => $otherCharacter,
            'types'          => $types,
        ]);
    }

    /**
     * Toggles the featured state of a link for a character.
     *
     * @param string $slug
     * @param int    $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postToggleFeaturedCharacterLink(Request $request, CharacterLinkService $service, $slug, $id) {
        if (Auth::user()->id != $this->character->user_id) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $link = CharacterRelation::find($id);
        if (!$link || ($link->character_1_id != $this->character->id && $link->character_2_id != $this->character->id)) {
            return response()->json(['error' => 'Link not found.'], 404);
        }

        $result = $service->toggleFeaturedRelation($link, $this->character);
        if ($result !== false) {
            return response()->json(['featured' => (bool) $result]);
        }

        return response()->json(['error' => $service->errors()->getMessages()['error'][0] ?? 'An error occurred.'], 422);
    }

    /**
     * Transfers inventory items back to a user.
     *
     * @param App\Services\InventoryManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postItemTransfer(Request $request, InventoryManager $service) {
        if ($service->transferCharacterStack($this->character, $this->character->user, CharacterItem::find($request->get('ids')), $request->get('quantities'), Auth::user())) {
            flash('Item transferred successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Names an inventory stack.
     *
     * @param App\Services\CharacterManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postName(Request $request, InventoryManager $service) {
        $request->validate([
            'stack_name' => 'nullable|max:100',
        ]);

        if ($service->nameStack($this->character, CharacterItem::find($request->get('ids')), $request->get('stack_name'), Auth::user())) {
            flash('Item named successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Deletes an inventory stack.
     *
     * @param App\Services\CharacterManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postDelete(Request $request, InventoryManager $service) {
        if ($service->deleteStack($this->character, CharacterItem::find($request->get('ids')), $request->get('quantities'), Auth::user())) {
            flash('Item deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
