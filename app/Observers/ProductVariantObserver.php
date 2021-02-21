<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\ProductVarient;

class ProductVariantObserver
{
    /**
     * Handle the ProductVarient "created" event.
     *
     * @param  \App\Models\ProductVarient  $productVarient
     * @return void
     */
    public function created(ProductVarient $productVarient)
    {
        //
    }

    /**
     * Handle the ProductVarient "updated" event.
     *
     * @param  \App\Models\ProductVarient  $productVarient
     * @return void
     */
    public function updated(ProductVarient $productVarient)
    {

        $product = Product::find($productVarient->product_id);
        if ($product !== null) {

            $min_regular = ProductVarient::where('product_id', $productVarient->product_id)->whereNotNull('regular_price')->orderBy('regular_price')->first();

            $max_regular = ProductVarient::where('product_id', $productVarient->product_id)->whereNotNull('regular_price')->orderByDesc('regular_price')->first();

            $min_Offer = ProductVarient::where('product_id', $productVarient->product_id)->whereNotNull('offer_price')->orderBy('offer_price')->first();

            $max_offer = ProductVarient::where('product_id', $productVarient->product_id)->whereNotNull('offer_price')->orderByDesc('offer_price')->first();


            $min = 0;
            $max = 0;

            if ($min_regular !== null)
                $min = $min_regular->regular_price;

            if ($min_Offer !== null && $min > $min_Offer->offer_price)
                $min = $min_Offer->offer_price;

            if ($max_regular !== null)
                $max = $max_regular->regular_price;

            if ($max_offer !== null && $max < $max_offer->offer_price)
                $max = $max_offer->offer_price;

            $product->minimun_price = $min;
            $product->maximum_price = $max;
            $product->save();
        }
    }

    /**
     * Handle the ProductVarient "deleted" event.
     *
     * @param  \App\Models\ProductVarient  $productVarient
     * @return void
     */
    public function deleted(ProductVarient $productVarient)
    {
        //
    }

    /**
     * Handle the ProductVarient "restored" event.
     *
     * @param  \App\Models\ProductVarient  $productVarient
     * @return void
     */
    public function restored(ProductVarient $productVarient)
    {
        //
    }

    /**
     * Handle the ProductVarient "force deleted" event.
     *
     * @param  \App\Models\ProductVarient  $productVarient
     * @return void
     */
    public function forceDeleted(ProductVarient $productVarient)
    {
        //
    }
}
