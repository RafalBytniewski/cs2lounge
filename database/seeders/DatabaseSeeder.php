<?php

namespace Database\Seeders;

use App\Models\InventoryItem;
use App\Models\TradeOffer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        TradeOffer::query()->delete();
        InventoryItem::query()->delete();
        User::query()->delete();

        $currentUser = User::factory()->create([
            'name' => 'Rafal',
            'email' => 'rafal@example.com',
        ]);

        $otherUsers = User::factory(3)->create();

        $inventoryItems = collect([
            ['name' => 'AK-47 Redline', 'weapon_type' => 'Rifle', 'exterior' => 'Field-Tested', 'rarity' => 'Covert', 'estimated_value' => 34.00],
            ['name' => 'M4A1-S Player Two', 'weapon_type' => 'Rifle', 'exterior' => 'Minimal Wear', 'rarity' => 'Classified', 'estimated_value' => 56.00],
            ['name' => 'AWP Neo-Noir', 'weapon_type' => 'Sniper Rifle', 'exterior' => 'Minimal Wear', 'rarity' => 'Covert', 'estimated_value' => 42.50],
            ['name' => 'USP-S Kill Confirmed', 'weapon_type' => 'Pistol', 'exterior' => 'Field-Tested', 'rarity' => 'Covert', 'estimated_value' => 51.20],
            ['name' => 'Driver Gloves Rezan the Red', 'weapon_type' => 'Gloves', 'exterior' => 'Battle-Scarred', 'rarity' => 'Extraordinary', 'estimated_value' => 91.00],
            ['name' => 'Talon Knife Blue Steel', 'weapon_type' => 'Knife', 'exterior' => 'Well-Worn', 'rarity' => 'Covert', 'estimated_value' => 312.00],
        ])->map(fn (array $item) => $currentUser->inventoryItems()->create($item));

        $sampleOffers = [
            [
                'user' => $otherUsers[0],
                'offer' => ['want_type' => 'any-knife', 'want_label' => 'any knife', 'want_details' => 'or equal value skins', 'status' => 'active'],
                'items' => [
                    ['name' => 'Bayonet Slaughter', 'weapon_type' => 'Knife', 'exterior' => 'Factory New', 'rarity' => 'Covert', 'estimated_value' => 640.00, 'is_available' => false],
                ],
            ],
            [
                'user' => $otherUsers[1],
                'offer' => ['want_type' => 'any-skins', 'want_label' => 'any skins', 'want_details' => 'upgrade around same value', 'status' => 'active'],
                'items' => [
                    ['name' => 'AWP Neo-Noir', 'weapon_type' => 'Sniper Rifle', 'exterior' => 'Minimal Wear', 'rarity' => 'Covert', 'estimated_value' => 41.00, 'is_available' => false],
                    ['name' => 'Desert Eagle Printstream', 'weapon_type' => 'Pistol', 'exterior' => 'Field-Tested', 'rarity' => 'Classified', 'estimated_value' => 31.50, 'is_available' => false],
                ],
            ],
            [
                'user' => $otherUsers[2],
                'offer' => ['want_type' => 'specific-item', 'want_label' => 'Butterfly Knife Fade', 'want_details' => 'specific skin request', 'status' => 'active'],
                'items' => [
                    ['name' => 'Sport Gloves Omega', 'weapon_type' => 'Gloves', 'exterior' => 'Battle-Scarred', 'rarity' => 'Extraordinary', 'estimated_value' => 505.00, 'is_available' => false],
                ],
            ],
        ];

        foreach ($sampleOffers as $sampleOffer) {
            $offer = $sampleOffer['user']->tradeOffers()->create($sampleOffer['offer']);
            $itemIds = collect($sampleOffer['items'])
                ->map(fn (array $item) => $sampleOffer['user']->inventoryItems()->create($item)->id);

            $offer->inventoryItems()->attach($itemIds);
        }
    }
}
