<?php

namespace App\Http\Controllers;

use App\Models\Options;
use App\Models\OptionValues;
use App\Models\Product;
use Illuminate\Http\Request;

class OptionsController extends Controller
{
    // store option
    public function storeOption(Request $request, $pid)
    {
        $product = Product::findOrFail($pid);
        if ($request->type === $product->type)
            $product->options()->save(new Options($request->all()));
        else
            return response()->json(['message' => "Product type does not match with data."], 422);
    }

    public function updateOption(Request $request, $oid)
    {
        $option = Options::findOrFail($oid);
        $option->name = $request->name;
        $option->select_count = $request->select_count;
        $option->save();
    }

    public function deleteOption(Request $request, $oid)
    {
        Options::destroy($oid);
    }

    public function storeOptionValue(Request $request, $oid)
    {
        $option = Options::findOrFail($oid);
        if ($option->type === "bundle") {
            foreach ($request->all() as $productValue) {
                $option_value = new OptionValues();
                $option_value->value_product_id = $productValue['value'];
                $option->optionValues()->save($option_value);
            }
        } else if ($option->type === "variant" && $option->value_name !== null) {
            $option_value = new OptionValues();
            $option_value->option_id = $request->option_id;
            $option_value->value_name = $request->value_name;
            $option_value->save();
        } else
            return response()->json(['message' => "Option values are invalid."], 422);
    }

    public function updateOptionValue(Request $request, $ovid)
    {
        $optionValue = OptionValues::findOrFail($ovid);
        if ($request->value_name !== null) {
            $optionValue->value_name = $request->value_name;
            $optionValue->save();
        } else
            return response()->json(['message' => "Option values are invalid."], 422);
    }

    public function deleteOptionValue(Request $request, $ovid)
    {
        OptionValues::destroy($ovid);
    }

    public function getBundleOptions($pid)
    {
        $product = Product::findOrFail($pid);
        if ($product->type === "bundle") {
            $options = $product->options;
            foreach ($options as $option) {
                $option->values = $option->optionValues;
                foreach ($option->values as $value) {
                    $value->product = Product::find($value->value_product_id);
                }
            }
            return $options;
        } else
            return response()->json(['message' => "Product type does not match with data."], 422);
    }
}
