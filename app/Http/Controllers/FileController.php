<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $rules = array(
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:8',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return [
                'index' => $request->index,
                'error' => true,
                'message' => $validator->errors()->first()
            ];
        } else {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);

            return [
                'index' => $request->index,
                'success' => true,
                'message' => "File has been uploaded successfully"
            ];
        }
    }
}
