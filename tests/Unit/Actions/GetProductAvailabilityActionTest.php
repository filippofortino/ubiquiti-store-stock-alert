<?php

namespace Tests\Unit\Actions;

use Tests\TestCase;
use App\Actions\GetProductAvailabilityAction;
use Illuminate\Support\Facades\Http;

class GetProductAvailabilityActionTest extends TestCase
{
    protected $productPageHtml;
    protected $expectedRawProductData;

    public function setUp(): void
    {
        parent::setUp();

        $this->productPageHtml = file_get_contents(base_path('tests/stubs/example-product-page.html'));
        $this->expectedRawProductData = trim(file_get_contents(base_path('tests/stubs/example-raw-product-data.js')));

        Http::fake([
            '*' => Http::response($this->productPageHtml, 200),
        ]);
    }

    /** @test **/
    public function it_can_get_the_product_availability()
    {
        $availability = (new GetProductAvailabilityAction())->execute('https://link-to-product.com');

        $this->assertFalse($availability);
    }

    /** @test **/
    public function it_can_extract_the_product_data()
    {
        $html = $this->productPageHtml;
        $action = invade(new GetProductAvailabilityAction());

        $rawProduct = $action->extractRawProductData($html);

        $this->assertEquals($this->expectedRawProductData, $rawProduct);
    }

    /** @test **/
    public function it_returns_null_if_it_cannot_find_product_data()
    {
        $html = file_get_contents(base_path('tests/stubs/product-page-without-data.html'));
        $action = invade(new GetProductAvailabilityAction());

        $rawProduct = $action->extractRawProductData($html);

        $this->assertNull($rawProduct);
    }
}
