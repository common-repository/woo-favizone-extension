<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 06/12/2017
 * Time: 10:34
 */
require_once str_replace("/", "\\", WP_PLUGIN_DIR) . "/" . FavizoneApi::FAVIZONE_PLUGIN_NAME . "/classes/FavizoneExport.php";

add_filter('query_vars','favizone_add_download_trigger');
function favizone_add_download_trigger($vars) {
    $vars[] = 'favizone_download_trigger';
    $vars[] .= 'favizone_export_download_site';
    return $vars;
}

add_action('template_redirect', 'favizone_download_trigger_check');
function favizone_download_trigger_check() {
    $favizone_date = gmdate('D, d M Y H:i:s');
    if(intval(get_query_var('favizone_download_trigger')) == 1){
        $favizone_export_site = get_query_var('favizone_export_download_site');
        if (isset($favizone_export_site)) {
            $favizone_local_file = str_replace("/","\\",WP_PLUGIN_DIR)."/".FavizoneApi::FAVIZONE_PLUGIN_NAME.'/favizoneexport/favizone-export-cataloge-'.$favizone_export_site.'.xml';
            if (file_exists($favizone_local_file) && is_file($favizone_local_file)) {
                chmod($favizone_local_file, 0644);
                // Vous voulez afficher un xml
                header('Content-Type: text/xml');
                // Il sera nommé favizone-export-cataloge.xml
                header('Content-Disposition: attachment; filename=favizone-export-cataloge-'.$favizone_export_site.'.xml'/*.$favizone_local_file*/);
                header('Last-Modified: '. $favizone_date . ' GMT');
                header('Expires: ' . $favizone_date);
                // Le source du xml original.xml
                readfile($favizone_local_file);
                exit();
            }
        }
        exit;
    }
}