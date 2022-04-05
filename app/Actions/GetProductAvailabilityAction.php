<?php

namespace App\Actions;

use App\Sidecar\ParseJavascript;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class GetProductAvailabilityAction
{
    public function execute(string $productUrl): bool
    {
        $html = Http::get($productUrl)->body();


        $rawProduct = $this->extractRawProductData($html);

        $product = ParseJavascript::execute([
            'code' => $rawProduct
        ])->body();

        return $product['product']['variants'][0]['available'];
    }

    protected function extractRawProductData(string $html): string|null
    {
        $crawler = new Crawler($html);
        $rawProductData = null;

        $crawler->filter('script')->each(function ($item) use (&$rawProductData) {
            if (str($item->text())->startsWith('window.APP_DATA = {')) {
                $rawProductData = str($item->text())->after('window.APP_DATA =')->beforeLast(';')->trim();
            }
        });

        return $rawProductData;
    }
}
