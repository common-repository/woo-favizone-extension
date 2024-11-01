<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 06/12/2017
 * Time: 11:50
 */

add_action( 'woocommerce_after_single_product_summary', 'favizone_product_after_single_product_summary', 10, 0 );
add_action( 'woocommerce_after_single_product', 'favizone_product_after_single_product', 10, 0 );

/**
 * Favizone product after single product summary
 */
function favizone_product_after_single_product_summary(  )
{
    if(is_product()){
        $favizone_recommender_html = "<div id='favizone_product_after_summary_element'></div>";
        echo $favizone_recommender_html;
    }
}

/**
 * Favizone product after single product
 */
function favizone_product_after_single_product(  )
{
    if(is_product()){
        $favizone_recommender_html = "<div id='favizone_product_element'></div>";
        echo $favizone_recommender_html;
    }
}