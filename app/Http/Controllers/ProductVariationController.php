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

class ProductVariationController extends Controller
{
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
                return $allPosibleVariants;
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
            // save to db
            foreach ($otherPosibleVariants as $otherPosibleVariant) {
                $productVariant = $product->productVarients()->create();
                foreach ($otherPosibleVariant as $otherPosibleValue) {
                    ProductVariantValues::create([
                        'product_varient_id' => $productVariant->id,
                        'product_id' => $pid,
                        'option_values_id' => $otherPosibleValue
                    ]);
                }
            }

            return $otherPosibleVariants;
        }
    }

    public function getProductVariants($pid)
    {
        $product = Product::findOrFail($pid);
        $productVariants = $product->productVarients;
        foreach ($productVariants as $productVariant)
            $productVariant->productVarientValues;
        return $productVariants;
    }

    public function updateProductVariants(Request $request, $pid)
    {
        Product::findOrFail($pid);
        foreach ($request->productVariants as $requestProductVariant) {
            $productVariant = ProductVarient::find($requestProductVariant->id);
            // update variant data
            if ($productVariant !== null) {
                $productVariant->update($requestProductVariant);

                // update variant values
                if ($requestProductVariant->productVarientValues !== null) {
                    $productVariant->productVarientValues()->detach();
                    foreach ($requestProductVariant->productVarientValues as $newVariantValues) {
                        ProductVariantValues::create([
                            'product_varient_id' => $productVariant->id,
                            'product_id' => $pid,
                            'option_values_id' => $newVariantValues->id
                        ]);
                    }
                }
            }
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
