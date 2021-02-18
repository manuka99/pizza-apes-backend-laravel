<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Product;
use App\Models\ProductVariantValues;
use App\Models\ProductVarient;
use Exception;
use Hamcrest\Type\IsArray;
use Illuminate\Http\Request;
use TypeError;
use Illuminate\Support\Str;

class ProductVariationController extends Controller
{
    public function getProductVariants($pid)
    {
        $product = Product::findOrFail($pid);
        $productVariants = $product->productVarients;
        foreach ($productVariants as $productVariant)
            $productVariant->productVarientValues;
        return $productVariants;
    }

    public function createAllPosibleVariations($pid)
    {
        if ($pid !== null) {
            $product = Product::findOrFail($pid);
            if ($product->type === 'variant') {
                $options = $product->options;
                $optionsWithValues = [];
                foreach ($options as $option) {
                    if (count($option->optionValues) > 0)
                        array_push($optionsWithValues, $option->optionValues);
                }
                // get all posible variants array
                $allPosibleVariants = $this->generate2($optionsWithValues);
                // /save to db
                // delete all previous variations
                $product->productVarients()->delete();
                foreach ($allPosibleVariants as $allPosibleVariant) {
                    $productVariant = $product->productVarients()->create();
                    foreach ($allPosibleVariant as $variantValue) {
                        ProductVariantValues::create([
                            'product_varient_id' => $productVariant->id,
                            'product_id' => $pid,
                            'option_values_id' => $variantValue->id
                        ]);
                    }
                }
                return $this->getProductVariants($pid);
            }
        }
    }

    public function destroyAllVariants($pid)
    {
        $product = Product::findOrFail($pid);
        $product->productVarients()->delete();
    }

    public function destroyVariant($pvid)
    {
        ProductVarient::destroy($pvid);
        return $pvid;
    }

    public function createCustomVariation(Request $request, $pid)
    {
        $product = Product::findOrFail($pid);
        $productVariant = $product->productVarients()->create();
        foreach ($request->option_value_ids as $option_value_id) {
            ProductVariantValues::create([
                'product_varient_id' => $productVariant->id,
                'product_id' => $pid,
                'option_values_id' => $option_value_id
            ]);
        }
        $productVariant->refresh();
        $productVariant->productVarientValues = $productVariant->productVarientValues;
        return $productVariant;
    }

    public function createOtherVariationsPosible($pid)
    {
        $product = Product::findOrFail($pid);
        $productVariantValues = [];
        foreach ($product->productVarients as $productVarient) {
            array_push($productVariantValues, $productVarient->productVarientValues()->pluck('option_values_id'));
        }
        if ($product->type === 'variant') {
            $options = $product->options;
            $optionsWithValues = [];
            foreach ($options as $option) {
                if (count($option->optionValues) > 0)
                    array_push($optionsWithValues, $option->optionValues);
            }
            // get other variants
            $otherPosibleVariants =  $this->generateOther($productVariantValues, $optionsWithValues);
            $newProductVariantIds = [];
            // save to db
            foreach ($otherPosibleVariants as $otherPosibleVariant) {
                $productVariant = $product->productVarients()->create();
                array_push($newProductVariantIds, $productVariant->id);
                foreach ($otherPosibleVariant as $otherPosibleValue) {
                    ProductVariantValues::create([
                        'product_varient_id' => $productVariant->id,
                        'product_id' => $pid,
                        'option_values_id' => $otherPosibleValue
                    ]);
                }
            }

            // get the new Variants from db
            $newProductVariants = ProductVarient::whereIn('id', $newProductVariantIds)->get();
            foreach ($newProductVariants as $newProductVariant) {
                $newProductVariant->productVarientValues;
            }
            return $newProductVariants;
        }
    }


    public function updateProductVariants(Request $request, $pid)
    {
        $product = Product::findOrFail($pid);
        if ($product->type = 'variant') {
            $errors = [];
            foreach ($request->all() as $requestProductVariant) {
                $productVariant = ProductVarient::find($requestProductVariant['id']);
                // update variant data
                if ($productVariant !== null) {
                    $productVariant->update($requestProductVariant);
                    $requestProductVariant = (object) $requestProductVariant;
                    // update variant values
                    if ($requestProductVariant->product_varient_values !== null) {
                        $productVariant->productVarientValues()->detach();
                        foreach ($requestProductVariant->product_varient_values as $newVariantValue) {
                            $newVariantValue = (object) $newVariantValue;
                            if ($newVariantValue !== null && $newVariantValue->id !== "") {
                                try {
                                    ProductVariantValues::create([
                                        'product_varient_id' => $productVariant->id,
                                        'product_id' => $pid,
                                        'option_values_id' => $newVariantValue->id
                                    ]);
                                } catch (\Exception $e) {
                                    $error = 'Error occured at variant of id: ' . $productVariant->id . ". " . $e->getMessage();
                                    array_push($errors, ['id' => Str::random(10), 'message' => $error]);
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($errors) > 0) {
            return response()->json($errors, 422);
        }
    }

    public function generate2($optionsWithValues = null, $variant = null, $count = 0)
    {
        $allPosibleVariants = [];
        if ($optionsWithValues !== null && $count <= (count($optionsWithValues) - 1)) {
            foreach ($optionsWithValues[$count] as $option) {
                $newVariant = [];
                if ($variant !== null)
                    $newVariant = $variant;
                array_push($newVariant, $option);
                if (($count + 1) === count($optionsWithValues))
                    array_push($allPosibleVariants, $newVariant);
                else {
                    $productVariants = $this->generate2($optionsWithValues, $newVariant, ($count + 1));
                    foreach ($productVariants as $productVariant)
                        array_push($allPosibleVariants, $productVariant);
                }
            }
        }
        return $allPosibleVariants;
    }

    public function generateOther($currentValues = null, $optionsWithValues = null, $variant = null, $count = 0)
    {
        $allPosibleVariants = [];
        if ($optionsWithValues !== null && $count <= (count($optionsWithValues) - 1)) {
            foreach ($optionsWithValues[$count] as $option) {
                $newVariant = [];
                if ($variant !== null)
                    $newVariant = $variant;
                array_push($newVariant, $option->id);
                if (($count + 1) === count($optionsWithValues) && !Helper::search_array_in_array($currentValues, $newVariant))
                    array_push($allPosibleVariants, $newVariant);
                else {
                    $productVariants = $this->generateOther($currentValues, $optionsWithValues, $newVariant, ($count + 1));
                    foreach ($productVariants as $productVariant)
                        array_push($allPosibleVariants, $productVariant);
                }
            }
        }
        return $allPosibleVariants;
    }

    public static function generate($optionsWithValues = null)
    {
        $allPosibleVariants = [];

        foreach ($optionsWithValues[0] as $option) {
            $variant = [];

            array_push($variant, $option->value_name);

            foreach ($optionsWithValues[1] as $option) {

                $newVariant1 = $variant;
                array_push($newVariant1, $option->value_name);

                foreach ($optionsWithValues[2] as $option) {

                    $newVariant2 = $newVariant1;
                    array_push($newVariant2, $option->value_name);
                    array_push($allPosibleVariants, $newVariant2);
                }
            }
        }

        return $allPosibleVariants;
    }
}
