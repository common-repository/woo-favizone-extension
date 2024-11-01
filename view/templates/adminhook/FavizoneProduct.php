<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 06/12/2017
 * Time: 11:21
 */

require_once str_replace("/", "\\", WP_PLUGIN_DIR) . "/" . FavizoneApi::FAVIZONE_PLUGIN_NAME . "/classes/FavizoneProduct.php";

add_action('save_post', 'favizone_product_save', 10, 3);
add_action('wp_trash_post', 'favizone_product_trash', 10, 1);

/**
 * favizone product save
 * @param $post_id
 * @param $post
 * @param $update
 */
function favizone_product_save($post_id, $post, $update)
{
    if ($post->post_status != 'publish' || $post->post_type != 'product') {
        return;
    }
    if (!$favizone_product = wc_get_product($post)) {
        return;
    }
    $favizone_product = wc_get_product($post_id);
    $favizone_shop_id = get_current_blog_id();
    $favizone_lang = get_locale();
    $fz_product = new FavizoneProduct();
    if ($update) {
        $fz_product->favizone_update_tagging_product_data($favizone_product, $favizone_shop_id, $favizone_lang, 'update');
    } else {
        $fz_product->favizone_update_tagging_product_data($favizone_product, $favizone_shop_id, $favizone_lang, 'add');
    }
}

/**
 * Favizone product trash
 * @param $post_id
 */
function favizone_product_trash($post_id)
{
    if (!$product = wc_get_product($post_id)) {
        return;
    }
    if ($product->post_type != 'product') {
        return;
    }
    $favizone_product = new FavizoneProduct();
    $favizone_shop_id = get_current_blog_id();
    $favizone_lang = get_locale();
    $favizone_product->favizone_update_tagging_product_data($product, $favizone_shop_id, $favizone_lang, 'delete');
}
