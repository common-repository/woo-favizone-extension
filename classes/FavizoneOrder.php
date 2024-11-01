<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 09/11/2017
 * Time: 11:40
 */
require_once 'FavizoneCommon.php';
require_once 'FavizoneApi.php';
require_once "FavizoneMetaOrder.php";
require_once 'FavizoneSender.php';

/**
 * Class FavizoneOrder
 */
class FavizoneOrder
{

    private $favizone_limit = 2;
    private $favizone_count_all_orders;

    private $favizone_offset = 1;

    /**
     * FavizoneOrder constructor.
     */
    public function __construct()
    {
    }

    private function get_count_orders_pages()
    {
        return ceil($this->favizone_count_all_orders = count(wc_get_orders(array(
            'limit' => -1,
            'return' => 'ids',
        )))/$this->favizone_limit);
    }

    /**
     * Favizone sender orders data
     * @param $favizone_store_id
     * @param $favizone_language
     * @return string
     */
    public function favizone_sender_orders_data($favizone_store_id, $favizone_language)
    {
        $favizone_pages = $this->get_count_orders_pages();
        $favizone_init_done = false;
        while ($this->favizone_offset <= $favizone_pages && !$favizone_init_done) {
            $args = array(
                'limit' => $this->favizone_limit,
                'paged' => $this->favizone_offset
            );

            $favizone_orders = wc_get_orders($args);
            $favizone_order_collection = array();
            foreach ($favizone_orders as $order) {
                $fz_meta_order = new FavizoneMetaOrder();
                $favizone_order = $fz_meta_order->favizone_load_order_data($order);
                $favizone_order_collection = array_merge($favizone_order_collection, $favizone_order);
            }
            $this->favizone_offset = $this->favizone_offset + 1;
            if($this->favizone_offset>$favizone_pages && $favizone_init_done === false){
                $this->favizone_offset = $favizone_pages;
                $favizone_init_done = true;
            }
            if(count($favizone_order_collection)){
                $this->favizone_send_init_order($favizone_order_collection, $favizone_store_id, $favizone_language, $favizone_init_done);
            }

        }
        return "ok";
    }

    /**
     * Favizone send init order
     * @param $favizone_order_collection
     * @param $favizone_store_id
     * @param $favizone_language
     * @param bool $favizone_init_done
     */
    private function favizone_send_init_order($favizone_order_collection, $favizone_store_id, $favizone_language, $favizone_init_done = false)
    {
        $fz_common = new FavizoneCommon();
        $favizone_access_key = $fz_common->get_favizone_site_access_key($favizone_store_id, $favizone_language)->access_key;
        $favizone_data_to_send = array(
            "key" => $favizone_access_key,
            "init_done" => $favizone_init_done,
            "orders" => $favizone_order_collection
        );
        $fz_sender = new FavizoneSender();
        $fz_api = new FavizoneApi();
        $fz_sender->favizone_post_request($fz_api->getFavizoneHost(), $fz_api->getInitOrderFavizoneUrl(), $favizone_data_to_send);
    }
}