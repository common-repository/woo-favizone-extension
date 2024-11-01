<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 23/11/2017
 * Time: 11:08
 */

/**add <div class='favizone_product'><span class='favizone_product_loop_identifier' style='display: none'>".$id."</span>
 * in shop_loop_item
 **/

add_action('woocommerce_before_shop_loop_item', 'favizone_before_shop_loop_item', 10, 0);
add_action('woocommerce_after_shop_loop_item', 'favizone_after_shop_loop_item', 10, 0);

/**
 * Favizone before shop loop_item
 * define the woocommerce_before_shop_loop_item callback
 */
function favizone_before_shop_loop_item()
{
    global $product;
    $favizone_id = $product->get_id();
    echo "<div class='favizone_product'><span class='favizone_product_loop_identifier' style='display: none'>" . $favizone_id . "</span>";

}

/**
 * Favizone after shop loop item
 */
function favizone_after_shop_loop_item()
{
    echo "</div>";
}



