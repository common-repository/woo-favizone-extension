<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 07/11/2017
 * Time: 12:02
 */
require_once 'FavizoneSender.php';
/**
 * Class FavizoneCommon
 */
class FavizoneCommon
{
    /**
     * FavizoneCommon constructor.
     */
    public function __construct()
    {

    }

    /**
     * Favizone wordpress version
     * @return string
     */
    public function favizone_wordpress_version()
    {
        $show = 'version';
        $filter = 'raw';
        $favizone_version = get_bloginfo($show, $filter);
        if (!empty($favizone_version))
            return $favizone_version;
        else
            return "No_Wordpress_Version";
    }

    /**
     * Favizone send check init done
     * @param $favizone_auth_key
     * @return array|mixed|object
     */
    public function favizone_send_check_init_done($favizone_auth_key)
    {
        $fz_sender = new FavizoneSender();
        $fz_Api = new FavizoneApi();
        $favizone_data_to_send = array("key" => $favizone_auth_key);
        $result = json_decode($fz_sender->favizone_post_request($fz_Api->getFavizoneHost(), $fz_Api->getCheckInitFavizoneUrl(), $favizone_data_to_send), true);
        return $result;
    }

    /**
     * Get favizone session identifier
     * @param null $favizone_store_id
     * @return string
     */
    public function get_favizone_session_identifier($favizone_store_id = null)
    {
        if ($favizone_store_id === null) {
            $favizone_store_id = get_site()->blog_id;
        }
        $favizone_cookie_data = $_COOKIE['favizone_connection_identifier_' . $favizone_store_id];
        if ($_COOKIE['favizone_connection_identifier_' . $favizone_store_id] && !empty($favizone_cookie_data)) {
            return $_COOKIE['favizone_connection_identifier_' . $favizone_store_id];
        } else {
            return "anonymous";
        }
    }

    /**
     * Get favizone site access key
     * @param $favizone_site_id
     * @param $favizone_language
     * @return array|null|object
     */
    public function get_favizone_site_access_key($favizone_site_id, $favizone_language)
    {
        global $wpdb;
        if ($wpdb != null) {
            $favizone_table_name = $wpdb->prefix . 'favizone_recommender_access_key';
            $favizone_recommender_access_key = $wpdb->get_row("SELECT * FROM $favizone_table_name WHERE sites_id =" . $favizone_site_id . " and lang='" . $favizone_language . "'");
            if ($favizone_recommender_access_key) {
                return $favizone_recommender_access_key;
            }
        }
        return null;
    }

    /**
     * Get favizone exprot url
     * @param $favizone_blog_id
     * @param $favizone_language
     * @return string
     */
    public function get_favizone_exprot_url($favizone_blog_id, $favizone_language)
    {
        $favizone_access_key = $this->get_favizone_site_access_key($favizone_blog_id, $favizone_language)->access_key;
        $url = site_url();
        return $url . "?favizone_export_trigger=1&favizone_export_access_key=" . $favizone_access_key . "&favizone_export_site=" . $favizone_blog_id . "&favizone_export_lang=" . $favizone_language;
    }

    /**
     * Get favizone download url
     * @param $favizone_blog_id
     * @return string
     */
    public function get_favizone_download_url($favizone_blog_id)
    {
        //return plugin_dir_url(__FILE__) . "download.php?site=" . $favizone_blog_id;
        $url = site_url();
        return $url . "?favizone_download_trigger=1&favizone_export_download_site=" . $favizone_blog_id ;

    }

    /**
     * Get favizone open url
     * @param $favizone_blog_id
     * @return string
     */
    public function get_favizone_open_url($favizone_blog_id)
    {
        //return plugin_dir_url(__FILE__) . "/" . FavizoneApi::FAVIZONE_PLUGIN_NAME . '/favizoneexport/favizone-export-cataloge-' . $favizone_blog_id . '.xml';
        $url = site_url();
        return $url . "?favizone_open_trigger=1&favizone_export_open_site=" . $favizone_blog_id ;

    }

    /**
     * Get favizone category bread crumb
     * @return array
     */
    public function get_favizone_category_bread_crumb()
    {
        $args = array();
        $args = wp_parse_args($args, apply_filters('woocommerce_breadcrumb_defaults', array(
            'home' => _x('Home', 'breadcrumb', 'woocommerce'),
        )));

        $breadcrumbs = new WC_Breadcrumb();

        if (!empty($args['home'])) {
            $breadcrumbs->add_crumb($args['home'], apply_filters('woocommerce_breadcrumb_home_url', home_url()));
        }
        $categories = $breadcrumbs->generate();
        $result_categories = array();
        $path_category = "";
        $count = count($categories);
        foreach ($categories as $index => $category) {
            ($index != $count - 1) ? $path_category .= $category[0] . "/" : $path_category .= $category[0];
        }
        ($count == 1) ? $result_categories['isRoot'] = true : $result_categories['isRoot'] = false;
        $result_categories['level'] = $count - 1;
        $result_categories['idParent'] = implode("/", array_slice(explode("/", $path_category), 0, count($categories) - 1));
        $result_categories['path'] = $path_category;
        return $result_categories;
    }

    /**
     * Get favizone home category
     * @return array
     */
    public function get_favizone_home_category()
    {
        $args = array();
        $args = wp_parse_args($args, apply_filters('woocommerce_breadcrumb_defaults', array(
            'home' => _x('Home', 'breadcrumb', 'woocommerce'),
        )));
        $breadcrumbs = new WC_Breadcrumb();

        if (!empty($args['home'])) {
            $breadcrumbs->add_crumb($args['home'], apply_filters('woocommerce_breadcrumb_home_url', home_url()));
        }
        $categories = $breadcrumbs->generate();
        $result_categories = array();
        $path_category = "";
        $url_category = "";
        $count = count($categories);
        foreach ($categories as $index => $category) {
            ($index != $count - 1) ? $path_category .= $category[0] . "/" : $path_category .= $category[0];
            $url_category .= $category[1];
        }
        ($count == 1) ? $result_categories['isCategoryRoot'] = 1 : $result_categories['isRoot'] = 0;
        $result_categories['level'] = $count - 1;
        //$result_categories['idParent'] = implode("/",array_slice(explode("/",$pathCategory),0,count($categories)-1));
        $result_categories['nameCategory'] = $path_category;
        $result_categories['idCategory'] = $path_category;
        $result_categories['isoCode'] = explode('_', get_locale())[0];
        $result_categories['url'] = $url_category;
        $result_categories['homeCategory'] = reset($categories)[0];
        //return json_encode($result_categories);
        return $result_categories;
    }
}