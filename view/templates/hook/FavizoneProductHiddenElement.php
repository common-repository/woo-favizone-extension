<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 23/11/2017
 * Time: 11:08
 */


require_once str_replace("/", "\\", WP_PLUGIN_DIR) . "/" . FavizoneApi::FAVIZONE_PLUGIN_NAME . "/model/FavizoneMetaProduct.php";

//add Product information after <div id="page"> tag
add_action('woocommerce_after_single_product', 'favizone_after_single_product', 10, 0);

/**
 * favizone after single product
 */
function favizone_after_single_product()
{
    global $product;
    $fz_meta_product = new FavizoneMetaProduct();
    $fz_meta_prod = $fz_meta_product->favizone_load_product($product);
    $html = "<section id='favizone_product_hidden_element' style='display:none'>";
    foreach ($fz_meta_prod as $key => $value) {
        if (is_array($value)) {
            $element = "<div id='favizone_product_" . $key . "' >";
            foreach ($value as $key2 => $value2) {
                if (is_array($value2)) {
                    foreach ($value2 as $key3 => $value3) {
                        $element .= "<div id='favizone_product_" . $key3 . "'  >";
                        if (is_array($value3)) {
                            foreach ($value3 as $key4 => $value4) {
                                $element .= "<span>" . $value4 . "</span>";
                            }
                        } else {
                            $element .= "<span>" . $value3 . "</span>";
                        }
                        $element .= "</div>";
                    }
                } else {
                    $element .= "<span>" . $value2 . "</span>";
                }
            }
            $element .= "</div>";
            $html .= $element;
        } else if(is_bool($value)) {
            ($value)?$html .= "<span id='favizone_product_" . $key . "'>true</span>":$html .= "<span id='favizone_product_" . $key . "'>false</span>";
        } else {
            $html .= "<span id='favizone_product_" . $key . "'>" . $value . "</span>";
        }
    }
    $html .= "</section>";
    echo $html;
}