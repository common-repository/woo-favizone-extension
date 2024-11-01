<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 06/12/2017
 * Time: 12:38
 */

add_action( 'woocommerce_thankyou_cheque', 'favizone_thankyou_cheque', 10, 0 );
add_action( 'woocommerce_order_details_after_order_table', 'favizone_order_details_after_order_table', 10, 0 );

/**
 * Favizone thankyou cheque
 */
function favizone_thankyou_cheque()
{
    if(is_order_received_page()){
        $favizone_recommender_html = "<div id='favizone_thankyou_cheque_element'></div>";
        echo $favizone_recommender_html;
    }
}

/**
 * favizone order details after order table
 */
function favizone_order_details_after_order_table(  )
{
    if(is_order_received_page()){
        $favizone_recommender_html = "<div id='favizone_order_details_after_order_table_element'></div>";
        echo $favizone_recommender_html;
    }
}