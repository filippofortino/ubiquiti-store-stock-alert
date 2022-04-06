<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Models\Product;
use App\Mail\ProductIsAvailable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Jobs\CheckProductsAvailability;
use App\Actions\GetProductAvailabilityAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

class CheckProductsAvailabilityTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        Http::fake([
            'ui.com/available' => Http::response(
                file_get_contents(base_path('tests/stubs/product-page-with-available-product.html')),
                200
            ),
            'ui.com/unavailable' => Http::response(
                file_get_contents(base_path('tests/stubs/example-product-page.html')),
                200
            ),
        ]);
    }

    /** @test **/
    public function it_notify_user_if_a_product_becomes_available()
    {
        $product = Product::factory()->create(['url' => 'https://ui.com/available']);

        (new CheckProductsAvailability)->handle(new GetProductAvailabilityAction);

        Mail::assertSent(ProductIsAvailable::class);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'available' => true,
        ]);
    }

    /** @test **/
    public function it_does_noting_if_product_is_still_unavailable()
    {
        $product = Product::factory()->create(['url' => 'https://ui.com/unavailable']);

        (new CheckProductsAvailability)->handle(new GetProductAvailabilityAction);

        Mail::assertNotSent(ProductIsAvailable::class);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'available' => false,
        ]);
    }

    /** @test **/
    public function it_ignores_product_that_are_already_marked_as_available()
    {
        $productA = Product::factory()->create(['url' => 'https://ui.com/available']);
        $productB = Product::factory()->create(['available' => true]);

        (new CheckProductsAvailability)->handle(new GetProductAvailabilityAction);

        Mail::assertSent(ProductIsAvailable::class, 1);
        $this->assertDatabaseHas('products', [
            'id' => $productA->id,
            'available' => true,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $productB->id,
            'available' => true,
        ]);
    }
}
