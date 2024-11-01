<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 06/12/2017
 * Time: 11:24
 */

add_action( 'woocommerce_after_shop_loop', 'favizone_product_category_after_shop_loop', 10, 0 );

/**
 * favizone product category after shop loop
 */
function favizone_product_category_after_shop_loop(  )
{
    if(is_product_category()){
        $favizone_recommender_html = "<div id='favizone_category_element'></div>";
        echo $favizone_recommender_html;
    }
}