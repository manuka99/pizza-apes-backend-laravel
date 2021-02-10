<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function store(Request $request, $id)
    {
        $names = explode(",", $request->name);
        foreach ($names as $name) {
            if (strlen($name) > 0) {
                Tag::create([
                    'pid' => $id,
                    'name' => trim($name)
                ]);
            }
        }
        return Tag::where('pid', $id)->get();
    }

    public function destroy($id)
    {
        Tag::destroy($id);
    }
    public function destroyAllProductTags($pid)
    {
        Tag::where('pid', $pid)->delete();
    }
}
