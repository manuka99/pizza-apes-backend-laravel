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

    public function storeProductVariantExtra(Request $request, $pid)
    {
        $product = Product::findOrFail($pid);
        $productVariant = new ProductVarient();
        if (($product->type === 'simple' || $product->type === 'bundle') && $product->productVarients()->count() < 2) {
            if ($product->productVarients()->count() === 1)
                $productVariant = $product->productVarients()->first();
            // create new variant
            else
                $productVariant = $product->productVarients()->save($productVariant);
        } else if ($product->type === 'variant' && $request->product_varient_id !== null)
            $productVariant = ProductVarient::findOrFail($request->product_varient_id);
        else
            return response()->json(['message' => "Product type does not match with data."], 422);
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

    public function getProductVariantExtra(Request $request, $pid)
    {
        $productVariant = null;
        if ($request->type !== 'variant') {
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
