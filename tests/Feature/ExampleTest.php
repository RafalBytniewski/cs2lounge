<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed();

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_a_public_trade_offer_can_be_created(): void
    {
        $this->seed();

        $inventoryItemIds = InventoryItem::query()->where('is_available', true)->limit(2)->pluck('id')->all();

        $response = $this->post('/offers', [
            'inventory_item_ids' => $inventoryItemIds,
            'want_type' => 'any-knife',
            'want_details' => 'Looking for a knife upgrade.',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseCount('trade_offers', 4);
        $this->assertDatabaseHas('trade_offers', [
            'want_type' => 'any-knife',
            'want_label' => 'any knife',
        ]);
    }
}
