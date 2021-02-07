<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
        Product::findOrFail($id);
        Product::updateOrCreate(
            ['id' => $id],
            $request->except(['id']),
        );
    }

    public function storeCategories(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->categories()->sync($request->all());
    }

    public function product(Request $request, $id)
    {
        $productData = Product::findOrFail($id);
        $productCategories = $productData->categories;
        $productCategoriesIds = $productCategories->pluck('id');
        return ['productData' => $productData, 'productCategories' => $productCategories, "productCategoriesIds" => $productCategoriesIds];
    }
}
