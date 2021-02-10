<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function store(Request $request)
    {
        Tag::create($request->all());
    }

    public function destroy($id)
    {
        Tag::destroy($id);
    }
}
