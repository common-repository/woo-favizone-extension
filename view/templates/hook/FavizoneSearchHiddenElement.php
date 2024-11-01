<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 23/11/2017
 * Time: 11:08
 */

//add search information
add_action('pre_get_posts', function ($query) {
    if ($query->is_main_query() && !is_admin() && $query->is_search()) {
        $favizone_html = "<span id='favizone_search_term' style='display: none'>" . get_search_query() . "</span>";
        echo $favizone_html;
    }
});



