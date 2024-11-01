<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 29/11/2017
 * Time: 11:53
 */

//add Category information after <div id="page"> tag
add_action('woocommerce_after_shop_loop', 'favizone_after_single_category', 10, 0);

/**
 * Favizone after single category
 */
function favizone_after_single_category()
{
    if (is_product_category()) {
        global $wp_query;
        $favizone_html = "<section id='favizone_category_hidden_element' style='display:none'>";
        $favizone_cat_obj = $wp_query->get_queried_object();
        $favizone_thumbnail_id = get_term_meta($favizone_cat_obj->term_id, 'thumbnail_id', true);
        $favizone_image = wp_get_attachment_url($favizone_thumbnail_id);
        $favizone_category_link = get_term_link($favizone_cat_obj->term_id);
        $fz_common = new FavizoneCommon();
        $favizone_category = $fz_common->get_favizone_category_bread_crumb();
        (!empty($favizone_cat_obj->name)) ? $favizone_html .= "<span id='favizone_category_name'>" . $favizone_cat_obj->name . "</span>" : true;
        (!empty($favizone_category['path'])) ? $favizone_html .= "<span id='favizone_category_id'>" . $favizone_category['path'] . "</span>" : true;
        ($favizone_category['isRoot']) ? $favizone_html .= "<span id='favizone_category_isRoot'>true</span>" : $favizone_html .= "<span id='favizone_category_isRoot'>false</span>";
        (!empty($favizone_category['idParent'])) ? $favizone_html .= "<span id='favizone_category_id_parent'>" . $favizone_category['idParent'] . "</span>" : true;
        (!empty($favizone_category['level'])) ? $favizone_html .= "<span id='favizone_category_level'>" . $favizone_category['level'] . "</span>" : true;
        (!empty($favizone_category_link)) ? $favizone_html .= "<span id='favizone_category_url'>" . $favizone_category_link . "</span>" : true;
        (!empty($favizone_image)) ? $favizone_html .= "<span id='favizone_category_cover'>" . $favizone_image . "</span>" : true;
        (!empty($favizone_cat_obj->description)) ? $favizone_html .= "<span id='favizone_category_description'>" . $favizone_cat_obj->description . "</span>" : true;
        $favizone_html .= "</section>";
        echo $favizone_html;
    }
}
