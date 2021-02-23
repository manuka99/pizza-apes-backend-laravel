<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Gallery;
use App\Models\Options;
use App\Models\OptionValues;
use App\Models\Product;
use App\Models\ProductVariantExtras;
use App\Models\ProductVariantValues;
use App\Models\ProductVarient;
use App\Models\SuggestedProducts;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOption\Option;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::where('is_trashed', false)->get();
        $trash = Product::where('is_trashed', true)->get();
        return ['products' => $products, 'trashProducts' => $trash];
    }

    public function destroy(Request $request)
    {
        foreach ($request->all() as $id)
            Product::destroy($id);
    }

    public function trash(Request $request)
    {
        Product::whereIn('id', $request->all())->update(['is_trashed' => true]);
    }

    public function restore(Request $request)
    {
        Product::whereIn('id', $request->all())->update(['is_trashed' => false]);
    }

    public function draft($id)
    {
        $statement = DB::select("SHOW TABLE STATUS LIKE 'products'");
        $nextId = $statement[0]->Auto_increment;

        $product = Product::findOrFail($id);
        $newProduct = $product->replicate()->fill([
            'product_name' =>  "Copy of " . $product->product_name,
        ]);
        $newProduct->id = $nextId;
        $newProduct->url_name = $nextId;
        $newProduct->save();
        // tags
        foreach ($product->tags as $tag) {
            $newTag = $tag->replicate();
            unset($newTag->id);
            unset($newTag->product_id);
            $newProduct->tags()->save($newTag);
        }
        // gallery
        foreach ($product->galleryImages as $galleryImage) {
            $newGalleryImage = $galleryImage->replicate();
            unset($newGalleryImage->id);
            unset($newGalleryImage->product_id);
            $newProduct->galleryImages()->save($newGalleryImage);
        }
        //save categories
        $newProduct->categories()->attach($product->categories()->pluck('category_id'));
        //options
        foreach ($product->options as $productOption) {
            $newProductOption = $productOption->replicate();
            unset($newProductOption->id);
            unset($newProductOption->product_id);
            $newProduct->options()->save($newProductOption);
            $newProductOption->refresh();
            foreach ($productOption->optionValues as $optionValue) {
                $newOptionValue = $optionValue->replicate();
                unset($newOptionValue->id);
                unset($newOptionValue->option_id);
                $newProductOption->optionValues()->save($newOptionValue);
            }
        }
        //product variants
        foreach ($product->productVarients as $productVariant) {
            $newProductVariant = $productVariant->replicate();
            unset($newProductVariant->id);
            unset($newProductVariant->product_id);
            $newProduct->productVarients()->save($newProductVariant);
            $newProductVariant->refresh();
            // for extras
            foreach (ProductVariantExtras::where('product_varient_id', $productVariant->id)->get() as $extra) {
                $newProductVariant->extras()->attach([
                    $extra->extras_id => [
                        'display_name' => $extra->display_name,
                        'select_count' => $extra->select_count
                    ]
                ]);
            }
            // for variant values
            foreach ($productVariant->productVarientValues as $productVarientValue) {
                $chk_opt_values = OptionValues::where('value_name', $productVarientValue->value_name)->get();
                $newOptionValue = null;
                foreach ($chk_opt_values as $chk_opt_value) {
                    if (Options::where('id', $chk_opt_value->option_id)->where('product_id', $newProduct->id)->first() !== null)
                        $newOptionValue = $chk_opt_value;
                }
                if ($newOptionValue !== null) {
                    ProductVariantValues::create([
                        'product_varient_id' => $newProductVariant->id,
                        'product_id' => $newProduct->id,
                        'option_values_id' => $newOptionValue->id
                    ]);
                }
            }
        }

        //suggested_products
        foreach ($product->suggestedProducts as $suggestedProduct) {
            $newSuggestedProduct = $suggestedProduct->replicate();
            unset($newSuggestedProduct->id);
            $newSuggestedProduct->pid_parent = $newProduct->id;
            $newProduct->suggestedProducts()->save($newSuggestedProduct);
        }

        $newProduct->save();
        return $nextId;
    }

    public function create()
    {
        $statement = DB::select("SHOW TABLE STATUS LIKE 'products'");
        $nextId = $statement[0]->Auto_increment;
        $product = new Product();
        $product->id = $nextId;
        $product->url_name = $nextId;
        $product->save();
        $category = Category::where('is_default', true)->first();
        if ($category !== null)
            $product->categories()->attach($category);
        return $nextId;
    }

    // basic product data
    public function store(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $newProduct = $product->update(
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
        $productGalleryImages = $productData->galleryImages;
        $productTags = $productData->tags;
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
