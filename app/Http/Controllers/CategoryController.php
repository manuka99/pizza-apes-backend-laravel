<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('root_id', null)->get();
        foreach ($categories as $category) {
            $category->children = Category::allCategoryChildren($category->id);
        }

        return ['categories' => $categories];
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:85'],
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->root_id = $request->root_id;
        $category->slug = $request->slug;
        $category->image = $request->image;
        $category->description = $request->description;

        if ($category->slug === '') {
            $category->slug = $request->name;
        }

        $category->save();
    }

    public function setDefault($cid)
    {
        $category = Category::findOrFail($cid);
        Category::where('is_default', true)->update(['is_default' => false]);
        $category->is_default = true;
        $category->save();
    }

    public function update(Request $request, $cid)
    {
        $category = Category::findOrFail($cid);
        $category->update($request->all());
    }

    public function destroy($cid)
    {
        Category::destroy($cid);
    }
}
