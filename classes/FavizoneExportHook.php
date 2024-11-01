<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 06/12/2017
 * Time: 10:34
 */
require_once str_replace("/", "\\", WP_PLUGIN_DIR) . "/" . FavizoneApi::FAVIZONE_PLUGIN_NAME . "/classes/FavizoneExport.php";

add_filter('query_vars','favizone_add_export_trigger');
function favizone_add_export_trigger($vars) {
    $vars[] = 'favizone_export_trigger';
    $vars[] .= 'favizone_export_access_key';
    $vars[] .= 'favizone_export_site';
    $vars[] .= 'favizone_export_lang';
    return $vars;
}

add_action('template_redirect', 'favizone_trigger_check');
function favizone_trigger_check() {
    if(intval(get_query_var('favizone_export_trigger')) == 1){
        $favizone_export_access_key = get_query_var('favizone_export_access_key');
        $favizone_export_site = get_query_var('favizone_export_site');
        $favizone_export_lang = get_query_var('favizone_export_lang');
        $fz_export = new FavizoneExport();
        $favizone_export = $fz_export->favizone_export($favizone_export_access_key, $favizone_export_site, $favizone_export_lang);
        echo $favizone_export;
        exit;
    }
}