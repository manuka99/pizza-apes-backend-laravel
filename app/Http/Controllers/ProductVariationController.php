<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Hamcrest\Type\IsArray;
use Illuminate\Http\Request;
use TypeError;

class ProductVariationController extends Controller
{
    public function createAllPosibleVariations($pid, $optionsWithValues = null, $variant = null,)
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
                $this->generate2($optionsWithValues);
            }
        }
    }

    public function generate2($optionsWithValues = null, $variant = null, $count = 0)
    {
        if ($optionsWithValues !== null && $count <= (count($optionsWithValues) - 1)) {

            foreach ($optionsWithValues[$count] as $option) {

                $newVariant = [];

                if ($variant !== null)
                    $newVariant = $variant;

                array_push($newVariant, $option->value_name);

                if (($count + 1) === count($optionsWithValues)) {

                    echo json_encode($newVariant) . '<br/> <br/>';
                } else
                    $this->generate2($optionsWithValues, $newVariant, ($count + 1));
            }
        }
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
