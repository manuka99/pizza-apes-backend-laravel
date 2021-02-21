<?php

namespace App\Http\Controllers;

use App\Models\Extras;
use App\Models\ExtrasValues;
use App\Models\Product;
use App\Models\ProductVariantExtras;
use App\Models\ProductVarient;
use Illuminate\Http\Request;

class ProductVariantExtrasController extends Controller
{

    //extras

    public function index()
    {
        $extras = Extras::all();
        foreach ($extras as $extra) {
            $extra->count = $extra->extrasValues()->count();
        }
        return $extras;
    }

    public function get($eid)
    {
        $extra = Extras::findOrFail($eid);
        return $extra->extrasValues;
    }

    public function store(Request $request)
    {
        Extras::create($request->all());
    }

    public function update(Request $request, $eid)
    {
        $extra = Extras::findOrFail($eid);
        $extra->update($request->all());
    }

    public function destroy($eid)
    {
        Extras::destroy($eid);
    }


    //extras values
    public function storeExtraValue(Request $request, $eid)
    {
        $extra = Extras::findOrFail($eid);
        $extra->extrasValues()->save(new ExtrasValues($request->all()));
    }

    public function updateExtraValue(Request $request, $evid)
    {
        $extraValue = ExtrasValues::findOrFail($evid);
        $extraValue->update($request->all());
    }

    public function destroyExtraValue($evid)
    {
        ExtrasValues::destroy($evid);
    }

    public function storeVariantExtra(Request $request, $pvid)
    {
        $this->storeProductVariantExtra($request, $pvid, true);
    }

    public function storeProductVariantExtra(Request $request, $pid, $isVariant = false)
    {
        $productVariant = null;
        if (!$isVariant) {
            // assume pid is product id and fetch the first variant
            $product = Product::findOrFail($pid);
            $productVariant = $product->productVarients()->first();

            if ($productVariant === null) {
                // create new variant
                $productVariant = new ProductVarient();
                $product->productVarients()->save($productVariant);
            }
        } else {
            // assume pid is product variant id
            $productVariant = ProductVarient::findOrFail($pid);
        }

        $productVariant->extras()->attach([
            $request->extras_id => [
                'display_name' => $request->display_name,
                'select_count' => $request->select_count
            ]
        ]);
    }

    // public function getProductVariantExtra($pvid)
    // {
    //     ProductVarient::findOrFail($pvid);
    //     $extras = ProductVariantExtras::where('product_varient_id', $pvid)->get();
    //     foreach ($extras as $extra) {
    //         $extra->data = Extras::find($extra->extras_id);
    //         $extra->values = ExtrasValues::where('extras_id', $extra->extras_id)->get();
    //     }
    //     return $extras;
    // }

    public function updateProductVariantExtra(Request $request, $pveid)
    {
        $productVariantExtra = ProductVariantExtras::findOrFail($pveid);
        $productVariantExtra->update($request->all());
    }

    public function getVariantExtra($pvid)
    {
        return $this->getProductVariantExtra($pvid, true);
    }

    public function getProductVariantExtra($pid, $isVariant = false)
    {
        $productVariant = null;
        if (!$isVariant) {
            // assume pid is product id and fetch the first variant
            $product = Product::findOrFail($pid);
            $productVariant = $product->productVarients()->first();
        } else {
            // assume pid is product variant id
            $productVariant = ProductVarient::findOrFail($pid);
        }
        if ($productVariant != null) {
            $productVariantExtras = ProductVariantExtras::where('product_varient_id', $productVariant->id)->get();
            foreach ($productVariantExtras as $productVariantExtra) {
                $extra = Extras::find($productVariantExtra->extras_id);
                $productVariantExtra->extra = $extra;
                $productVariantExtra->count = $extra->extrasValues()->count();
                $productVariantExtra->extrasValues = $extra->extrasValues;
            }
            return $productVariantExtras;
        }
    }

    public function destroyProductVariantExtra($pveid)
    {
        ProductVariantExtras::destroy($pveid);
    }
}
