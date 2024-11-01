<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 06/12/2017
 * Time: 09:57
 */

add_action( 'woocommerce_before_shop_loop', 'favizone_before_shop_loop1', 10, 0 );
add_action( 'woocommerce_after_main_content', 'favizone_after_main_content', 10, 0 );
add_action('woocommerce_after_shop_loop', 'favizone_after_shop_loop');

/**
 * Favizone before shop loop1
 */
function favizone_before_shop_loop1(  )
{
    if(is_shop() && !is_search()){
        $favizone_recommender_html = "<div id='favizone_home_top_element'></div>";
        echo $favizone_recommender_html;
    }
}

/**
 * Favizone after main content
 */
function favizone_after_main_content(  )
{
    if(is_shop() && !is_search()){
        $favizone_recommender_html = "<div id='favizone_home_footer_element'></div>";
        echo $favizone_recommender_html;
    }
}

/**
 * Favizone after shop loop
 */
function favizone_after_shop_loop(){
    if(is_shop() && !is_search()){
        $recommenderHtml = "<div id='favizone_home_bottom_element'></div>";
        echo $recommenderHtml;
    }
}