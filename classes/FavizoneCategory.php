<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 30/11/2017
 * Time: 12:25
 */

require_once 'FavizoneApi.php';
require_once 'FavizoneSender.php';

/**
 * Class FavizoneCategory
 */
class FavizoneCategory
{
    /**
     * FavizoneCategory constructor.
     */
    public function __construct()
    {
    }

    /**
     * Favizone send category data
     * @param $favizone_store_id
     * @param $favizone_language
     */
    public function favizone_send_category_data($favizone_store_id, $favizone_language)
    {
        $fz_common = new FavizoneCommon();
        $favizone_category_data = $fz_common->get_favizone_home_category();
        $this->favizone_send_init_category($favizone_store_id, $favizone_language, $favizone_category_data);
    }

    /**
     * Favizone send init category
     * @param $favizone_store_id
     * @param $favizone_language
     * @param $favizone_category
     */
    private function favizone_send_init_category($favizone_store_id, $favizone_language, $favizone_category)
    {
        $fz_common = new FavizoneCommon();
        $favizone_auth_key = $fz_common->get_favizone_site_access_key($favizone_store_id, $favizone_language)->access_key;
        $favizone_data_to_send = array(
            "key" => $favizone_auth_key,
            "category" => $favizone_category
        );
        $fz_sender = new FavizoneSender();
        $fz_api = new FavizoneApi();
        $fz_sender->favizone_post_request($fz_api->getFavizoneHost(), $fz_api->getAddCategoryFavizoneUrl(), $favizone_data_to_send);
    }

}