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
        } else if ($option->type === "variant" && $request->name !== null) {
            $names = explode(",", $request->name);
            foreach ($names as $name) {
                if (strlen(trim($name)) > 0) {
                    $option_value = new OptionValues();
                    $option_value->value_name = $name;
                    $option->optionValues()->save($option_value);
                }
            }
        } else
            return response()->json(['message' => "Option values are invalid."], 422);
    }

    public function updateOptionValue(Request $request, $ovid)
    {
        $optionValue = OptionValues::findOrFail($ovid);
        if ($request->value_name !== null && $request->value_name !== "") {
            $optionValue->value_name = $request->value_name;
            $optionValue->value_image = $request->value_image;
            $optionValue->save();
        } else
            return response()->json(['message' => "Option values are invalid."], 422);
    }

    public function deleteOptionValue(Request $request, $ovid)
    {
        OptionValues::destroy($ovid);
    }

    public function deleteAllOptionValues($oid)
    {
        $options = Options::findOrFail($oid);
        $options->optionValues()->delete();
    }

    public function getProductOptions($pid)
    {
        $product = Product::findOrFail($pid);
        if ($product->type !== "simple") {
            $options = $product->options;
            foreach ($options as $option) {
                $option->values = $option->optionValues;
                if ($product->type === "bundle") {
                    foreach ($option->values as $value) {
                        $value->product = Product::find($value->value_product_id);
                    }
                }
            }
            return $options;
        } else
            return response()->json(['message' => "Product type does not match with data."], 422);
    }
}
