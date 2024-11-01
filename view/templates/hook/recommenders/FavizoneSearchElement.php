<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 06/12/2017
 * Time: 15:57
 */

add_action( 'woocommerce_before_shop_loop', 'favizone_search_before_main_content', 10, 0 );
add_action( 'woocommerce_after_shop_loop', 'favizone_search_after_main_content', 10, 0 );
/**
 * Favizone search before main content
 */
function favizone_search_before_main_content(  )
{
    if(is_search()){
        $favizone_recommender_html = "<div id='favizone_before_search_element'></div>";
        echo $favizone_recommender_html;
    }
}

/**
 * Favizone search after main content
 */
function favizone_search_after_main_content(  )
{
    if(is_search()){
        $favizone_recommender_html = "<div id='favizone_after_search_element'></div>";
        echo $favizone_recommender_html;
    }
}