<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function store(Request $request)
    {
        //
    }

    public function storeProduct(Request $request, $id)
    {
        //first delete all images of the produut
        Gallery::where('pid', $id)->where('type', 'product')->delete();
        if ($request->has('images')) {
            foreach ($request->images as $image) {
                Gallery::create($image);
            }
        } else
            Gallery::create($request->all());
    }
}
