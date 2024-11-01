<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 23/11/2017
 * Time: 11:08
 */


//add User information after <div id="page"> tag
add_action('wp_head', 'favizone_user_element', 10, 0);
/**
 * Favizone_user_element
 */
function favizone_user_element()
{
    $favizone_current_user = wp_get_current_user();
    global $woocommerce;
    echo "<div id='favizone_user_element' style='display:none'>" .
        "<span id='favizone_customer_email'>" . $favizone_current_user->user_email . "</span>" .
        "<span id='favizone_customer_identifier'>" . $favizone_current_user->ID . "</span>" .
        "<span id='favizone_customer_first_name'>" . $favizone_current_user->first_name . "</span>" .
        "<span id='favizone_customer_last_name'>" . $favizone_current_user->last_name . "</span>" .
        "<span id='favizone_customer_country'>" . $woocommerce->customer->get_billing_country() . "</span>" .
        "<span id='favizone_customer_languages'>" . get_locale() . "</span>" . //default language
        "<span id='favizone_price_currency_code'>" . get_woocommerce_currency() . "</span>" . //default currency
        "</div>";
}
