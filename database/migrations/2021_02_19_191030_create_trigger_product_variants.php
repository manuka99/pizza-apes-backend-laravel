<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTriggerProductVariants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // DB::unprepared('
        // CREATE TRIGGER trigger_price_ranges_product_variant AFTER UPDATE ON `product_varients` FOR EACH ROW
        //     BEGIN
        //     declare product_id INT;
        //     declare product_type varchar(45);
        //     declare min double;
        //     declare max double;

        //     set product_id = OLD.product_id;

        //     select type INTO product_type from products where id = product_id;
        //     if (product_type = `variant`)
        //     THEN
        //         select regular_price
        //         INTO min 
        //         from product_varients
        //         where product_id = 1 and regular_price is not null
        //         order by regular_price asc 
        //         limit 1;

        //         select regular_price 
        //         INTO max
        //         from product_varients
        //         where product_id = 1 and regular_price is not null
        //         order by regular_price desc 
        //         limit 1;

        //         update products set minimun_price = min, maximum_price = max where id = product_id;
        //     END IF;
        //     END
        // ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // DB::unprepared('DROP TRIGGER `trigger_price_ranges_product_variant`');
    }
}
