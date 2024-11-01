<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 06/12/2017
 * Time: 10:34
 */
require_once str_replace("/", "\\", WP_PLUGIN_DIR) . "/" . FavizoneApi::FAVIZONE_PLUGIN_NAME . "/classes/FavizoneExport.php";

add_filter('query_vars','favizone_add_open_trigger');
function favizone_add_open_trigger($vars) {
    $vars[] = 'favizone_open_trigger';
    $vars[] .= 'favizone_export_open_site';
    return $vars;
}

add_action('template_redirect', 'favizone_open_trigger_check');
function favizone_open_trigger_check() {
    if(intval(get_query_var('favizone_open_trigger')) == 1){
        $favizone_export_open_site = get_query_var('favizone_export_open_site');
        $xml=file(plugin_dir_url(__FILE__) . '../favizoneexport/favizone-export-cataloge-' . $favizone_export_open_site . '.xml') or die("Error: Cannot create object");
        header('Content-Type: text/xml');
        echo implode('', $xml);

        exit;
    }
}