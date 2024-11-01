<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 06/12/2017
 * Time: 12:14
 */

add_action('woocommerce_after_cart_table', 'favizone_after_cart_table1', 10, 0);
add_action('woocommerce_after_cart', 'favizone_after_cart1', 10, 0);

/**
 * favizone after cart table1
 */
function favizone_after_cart_table1()
{
    if (is_cart()) {
        $favizone_recommender_html = "<div id='favizone_after_cart_table_element'></div>";
        echo $favizone_recommender_html;
    }
}

/**
 * favizone after cart1
 */
function favizone_after_cart1()
{
    if (is_cart()) {
        $favizone_recommender_html = "<div id='favizone_after_cart_element'></div>";
        echo $favizone_recommender_html;
    }
}