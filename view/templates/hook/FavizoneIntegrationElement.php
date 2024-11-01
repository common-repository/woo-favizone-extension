<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 23/11/2017
 * Time: 11:08
 */


//favizone integration js
add_action( 'wp_enqueue_scripts', 'favizone_integration_script' );
function favizone_integration_script() {
    wp_enqueue_script( 'favizone_integration_script', plugins_url( '../../../assets/js/favizone-integration.js', __FILE__ ) );
    $favizone_common = new FavizoneCommon();
    $favizone_site_id = get_current_blog_id();
    $favizone_language = get_locale();
    $favizone_shop_id = $favizone_common->get_favizone_site_access_key($favizone_site_id, $favizone_language)->shop_id;
    $favizone_data_to_be_passed = array(
        'favizone_shop_id'            => $favizone_shop_id
    );
    wp_localize_script( 'favizone_integration_script', 'php_vars', $favizone_data_to_be_passed );
}
//end favizone integration js


