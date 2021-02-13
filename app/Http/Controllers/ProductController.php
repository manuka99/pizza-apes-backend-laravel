<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function create()
    {
        $statement = DB::select("SHOW TABLE STATUS LIKE 'products'");
        $nextId = $statement[0]->Auto_increment;
        $product = new Product();
        $product->id = $nextId;
        $product->url_name = $nextId;
        $product->save();
        return $nextId;
    }

    public function store(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $newProduct = Product::updateOrCreate(
            ['id' => $id],
            $request->except(['id']),
        );

        //if product type change remove all types
        if ($product->type !== $newProduct->type) {
            $newProduct->productVarients()->delete();
        }

        return $newProduct;
    }

    public function storeCategories(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->categories()->sync($request->all());
    }

    public function product($id)
    {
        $productData = Product::findOrFail($id);
        $productCategories = $productData->categories;
        $productCategoriesIds = $productCategories->pluck('id');
        $productGalleryImages = Gallery::where('pid', $id)->where('type', 'product')->get();
        $productTags = Tag::where('pid', $id)->get();
        return [
            'productData' => $productData,
            'productCategories' => $productCategories,
            "productCategoriesIds" => $productCategoriesIds,
            "productGalleryImages" => $productGalleryImages,
            "productTags" => $productTags,
        ];
        return back()->setStatusCode(200);
    }

    // simple and bundle product data
    public function getSimpleAndBundleData($id)
    {
        $product = Product::findOrFail($id);
        if ($product->type === 'simple' || $product->type === 'bundle') {
            $productVariant = $product->productVarients()->first();
            if ($productVariant !== null) {
                $generalData = $productVariant->only('regular_price', 'offer_price', 'schedule_offer', 'offer_from', 'offer_to');
                $inventory = $productVariant->only('sku_id', 'manage_stock', 'stock_qty', 'low_stock_threshold', 'back_orders', 'order_limit_count', 'order_limit_days');
                $shipping = $productVariant->only('weight', 'height', 'width', 'length', 'shipping_class');
                return [
                    'generalData' => $generalData,
                    'inventory' => $inventory,
                    'shipping' => $shipping,
                ];
            }
        } else
            return back()->withErrors(['message' => "Product type does not match with data."]);
    }

    public function storeSimpleAndBundleData(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        if ($product->type === 'simple' || $product->type === 'bundle') {
            $productVarients = $product->productVarients;
            if (count($productVarients) > 0)
                $productVarients[0]->update($request->all());
            else
                $product->productVarients()->save(new ProductVarient($request->all()));
        } else
            return back()->withErrors(['message' => "Product type does not match with data."])->setStatusCode(601);
    }
}
