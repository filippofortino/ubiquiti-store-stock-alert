<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use App\Mail\ProductIsAvailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Actions\GetProductAvailabilityAction;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Support\Facades\Log;

class CheckProductsAvailability implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GetProductAvailabilityAction $getProductAvailabilityAction): void
    {
        $products = Product::query()
            ->where('available', false)
            ->get();

        foreach ($products as $product) {
            $isNowAvailable = $getProductAvailabilityAction->execute($product->url);

            if ($isNowAvailable) {
                Mail::to('filippofortino+ubalert@gmail.com')->send(new ProductIsAvailable($product));
                $product->available = true;
                $product->save();
                Log::info("Product: '{$product->name}' is now available");
            } else {
                Log::info("Product: '{$product->name}' is still not available");
            }
        }
    }
}
