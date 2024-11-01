<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 07/11/2017
 * Time: 11:38
 */

/**
 * Class FavizoneAccount
 */
class FavizoneAccount
{
    /**
     * FavizoneAccount constructor.
     */
    public function __construct()
    {
        require_once('FavizoneApi.php');
        require_once('FavizoneCommon.php');
    }

    /**
     * Favizone create account
     * @param $email
     * @param $language
     * @return array
     */
    public function favizone_create_account($email, $language)
    {
        $fz_common = new FavizoneCommon();
        $fz_api = new FavizoneApi();
        $favizone_account_data = array();
        $favizone_account_data["email"] = $email;
        $favizone_account_data["cms_name"] = "wordpress";
        $favizone_account_data["cms_version"] = $fz_common->favizone_wordpress_version();
        $favizone_account_data["shop_url"] = site_url();

        if (is_multisite()) {
            $favizone_account_data["shop_identifier"] = get_site()->blog_id;
            $favizone_account_data["shop_name"] = get_site()->blogname;
            $favizone_account_data["language_identifier"] = get_site()->lang_id;
        } else {
            $favizone_account_data["shop_identifier"] = get_current_blog_id();
            $favizone_account_data["shop_name"] = get_bloginfo('name', 'raw');
            $favizone_account_data["language_identifier"] = "";
        }
        $favizone_account_data["language"] = explode('_', $language)[0];
        $favizone_account_data["local"] = $language;
        $favizone_account_data["timezone"] = get_option('timezone_string');
        $countries_obj = new WC_Countries();
        $default_country = $countries_obj->get_base_country();
        $favizone_account_data["country"] = $default_country;
        $favizone_account_data["customTag"] = true;
        $favizone_account_data["request_url"] = $fz_api->getFavizoneHost() . $fz_api->getAddAccountFavizoneUrl();
        $favizone_account_data["status"] = "success";

        return $favizone_account_data;
    }

    /**
     * Favizone insert recommender access key
     * @param $favizone_application_key
     * @param $favizone_lang
     * @param $favizone_shop_id
     */
    public function favizone_insert_recommender_access_key($favizone_application_key, $favizone_lang, $favizone_shop_id, $favizone_reference)
    {
        global $wpdb;
        $favizone_table_name = $wpdb->prefix . 'favizone_recommender_access_key';
        $favizone_recommender_access_key = $wpdb->get_row("SELECT * FROM $favizone_table_name WHERE sites_id =" . $favizone_shop_id . " and lang='" . $favizone_lang . "'");
        if (!$favizone_recommender_access_key) {
            $wpdb->insert(
                $favizone_table_name,
                array(
                    'sites_id' => $favizone_shop_id,
                    'access_key' => $favizone_application_key,
                    'shop_id' => $favizone_reference,
                    'lang' => $favizone_lang,
                    'ab_test' => false,
                    'ab_diff' => 0
                )
            );
        }
    }
}
