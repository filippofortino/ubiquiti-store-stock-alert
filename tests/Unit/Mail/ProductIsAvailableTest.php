<?php

namespace Tests\Unit\Mail;

use App\Mail\ProductIsAvailable;
use Tests\TestCase;
use App\Models\Product;

class ProductIsAvailableTest extends TestCase
{
    /** @test **/
    public function test_mailable_content()
    {
        $product = Product::factory()->make([
            'name' => 'Example Product',
            'url' => 'https://link-to-product.com',
        ]);

        $mailable = new ProductIsAvailable($product);

        $mailable->assertSeeInHtml($product->name);
        $mailable->assertSeeInHtml($product->url);

        $mailable->assertSeeInText($product->name);
        $mailable->assertSeeInText($product->url);
    }
}
