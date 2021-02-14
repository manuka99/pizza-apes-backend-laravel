<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Options;
use App\Models\OptionValues;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\SuggestedProducts;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    // basic product data
    public function store(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $newProduct = Product::updateOrCreate(
            ['id' => $id],
            $request->except(['id']),
        );

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

    //suggested products
    public function getSuggestedProducts($id)
    {
        $product = Product::findOrFail($id);
        return Product::whereIn('id', $product->suggestedProducts()->pluck('pid'))->get();
    }

    //search
    public function searchProducts(Request $request)
    {
        $products = Product::search($request->name)->get();
        return $products;
    }

    public function storeSuggestedProducts(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        foreach ($request->options as $option) {
            SuggestedProducts::create([
                'pid' => $option['value'],
                'pid_parent' => $id
            ]);
        }
        $product = Product::findOrFail($id);
        return Product::whereIn('id', $product->suggestedProducts()->pluck('pid'))->get();
    }

    public function destroySuggestedProducts(Request $request, $id)
    {
        SuggestedProducts::where('pid', $request->pid)->where('pid_parent', $id)->delete();
    }

    public function destroyAllSuggestedProducts($id)
    {
        $product = Product::findOrFail($id);
        $product->suggestedProducts()->delete();
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
            return response()->json(['message' => "Product type does not match with data."], 422);
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
            return response()->json(['message' => "Product type does not match with data."], 422);
    }
}
