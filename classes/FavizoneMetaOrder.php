<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 10/11/2017
 * Time: 17:45
 */

/**
 * Class FavizoneMetaOrder
 */
class FavizoneMetaOrder
{
    /**
     * Favizone load order data
     * @param $favizone_order
     * @return array
     */
    public function favizone_load_order_data($favizone_order)
    {
        $favizone_orders_events = array();
        foreach ($favizone_order->get_items() as $favizone_item) {
            array_push(
                $favizone_orders_events,
                strtotime($favizone_order->order_date) . " favizone_xxx " . $favizone_order->customer_id . " confirm " . $favizone_item->get_product_id() . " " . $favizone_item->get_total() . " " . $favizone_item->get_quantity()
            );
        }
        return $favizone_orders_events;
    }

}