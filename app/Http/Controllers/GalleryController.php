<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Models\Product;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function store(Request $request)
    {
        //
    }

    public function storeProductGallery(Request $request, $pid)
    {
        //first delete all images of the produut
        $product = Product::findOrFail($pid);
        $product->galleryImages()->delete();
        if ($request->has('images')) {
            foreach ($request->images as $image) {
                $galleryImage = new Gallery();
                $galleryImage->url = $image['url'];
                $galleryImage->name = $image['name'];
                $galleryImage->type = 'product_gallery';
                $product->galleryImages()->save($galleryImage);
            }
        }
    }
}
