<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\TradeOffer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TradeOfferController extends Controller
{
    public function index(): View
    {
        $currentUser = User::query()
            ->with(['inventoryItems' => fn ($query) => $query->where('is_available', true)->orderBy('estimated_value', 'desc')])
            ->firstOrFail();

        $tradeOffers = TradeOffer::query()
            ->with(['user', 'inventoryItems'])
            ->latest()
            ->get();

        $stats = [
            'publicOffers' => $tradeOffers->count(),
            'knifeRequests' => $tradeOffers->where('want_type', 'any-knife')->count(),
            'anySkinTrades' => $tradeOffers->where('want_type', 'any-skins')->count(),
        ];

        return view('welcome', [
            'currentUser' => $currentUser,
            'inventoryItems' => $currentUser->inventoryItems,
            'tradeOffers' => $tradeOffers,
            'stats' => $stats,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $currentUser = User::query()->firstOrFail();

        $validated = $request->validate([
            'inventory_item_ids' => ['required', 'array', 'min:1'],
            'inventory_item_ids.*' => ['integer', 'exists:inventory_items,id'],
            'want_type' => ['required', 'in:any-skins,any-knife,specific-item,upgrade-only'],
            'want_label' => ['nullable', 'string', 'max:120'],
            'want_details' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $selectedItems = InventoryItem::query()
            ->where('user_id', $currentUser->id)
            ->where('is_available', true)
            ->whereIn('id', $validated['inventory_item_ids'])
            ->get();

        if ($selectedItems->count() !== count($validated['inventory_item_ids'])) {
            return back()
                ->withInput()
                ->withErrors(['inventory_item_ids' => 'One or more selected skins are no longer available.']);
        }

        $offer = TradeOffer::query()->create([
            'user_id' => $currentUser->id,
            'want_type' => $validated['want_type'],
            'want_label' => $this->resolveWantLabel($validated['want_type'], $validated['want_label'] ?? null),
            'want_details' => $validated['want_details'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'active',
        ]);

        $offer->inventoryItems()->attach($selectedItems->pluck('id'));

        InventoryItem::query()
            ->whereIn('id', $selectedItems->pluck('id'))
            ->update(['is_available' => false]);

        return redirect('/')
            ->with('status', 'Public trade offer published.');
    }

    private function resolveWantLabel(string $wantType, ?string $wantLabel): string
    {
        return match ($wantType) {
            'any-skins' => 'any skins',
            'any-knife' => 'any knife',
            'upgrade-only' => 'upgrade only',
            'specific-item' => $wantLabel ?: 'specific item',
            default => 'trade request',
        };
    }
}
