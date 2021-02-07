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
        $product->categories()->attach(1);
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

    public function product(Request $request, $id)
    {
        $productData = Product::findOrFail($id);
        return ['productData' => $productData];
    }
}
