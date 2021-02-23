<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function store(Request $request, $pid)
    {
        $product = Product::findOrFail($pid);
        $names = explode(",", $request->name);
        foreach ($names as $name) {
            if (strlen($name) > 0) {
                $tag = new Tag();
                $tag->name = trim($name);
                $product->tags()->save($tag);
            }
        }
        return $product->tags;
    }

    public function destroy($id)
    {
        Tag::destroy($id);
    }
    public function destroyAllProductTags($pid)
    {
        Tag::where('product_id', $pid)->delete();
    }
}
