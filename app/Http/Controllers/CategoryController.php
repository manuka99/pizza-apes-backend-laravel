<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('root_id', 0)->get();
        foreach ($categories as $category) {
            $category->children = Category::allCategoryChildren($category->id);
        }

        return ['categories' => $categories];
    }

    public function store(Request $request)
    {
        Category::create(
            $request->all()
        );
    }
}
