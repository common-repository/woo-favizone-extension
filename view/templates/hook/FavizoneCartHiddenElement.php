<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 23/11/2017
 * Time: 11:08
 */


add_action('woocommerce_after_cart', 'favizone_after_cart', 10, 0);

/**
 * favizone after cart
 */
function favizone_after_cart() {
    global $woocommerce;
    $favizone_items = $woocommerce->cart->get_cart();
    $favizone_html = "<section id='favizone_cart_hidden_element' style='display:none'>";
    foreach($favizone_items as $item => $favizone_values){
        $favizone_product = new WC_Product( $favizone_values['product_id']) ;
        $favizone_html .= "<div>";
        $favizone_html .= "<span id='favizone_product_cart_identifier'>".$favizone_values['product_id']."</span>";
        $favizone_html .= "<span id='favizone_product_cart_quantity'>".$favizone_values['quantity']."</span>";
        $favizone_html .= "<span id='favizone_product_cart_price'>".$favizone_product->get_price()."</span>";
        $favizone_html .= "</div>";
    }
    $favizone_html .="</section>";
    echo $favizone_html;
}



