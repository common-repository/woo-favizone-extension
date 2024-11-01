<?php
/**
 * Plugin Name: woo-favizone-tagging
 * Plugin URI: http://wordpress.org/plugins/woo-favizone-extension
 * Description: Deliver your customers personalized shopping experiences, at every touch point, across every device.
 * Version: 1.1.1
 * Author: Favizone
 * Author URI: http://favizone.com/
 * Developer: Favizone
 * Developer URI: http://favizone.com/
 *
 * WC requires at least: 3.2.3
 * WC tested up to: 3.2.6
 *
 * Copyright: © 2016-2017 Favizone.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/*	Copyright 2016 Favizone

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 3, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Check if WooCommerce is active
 **/
require_once 'classes/FavizoneApi.php';

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    if (!class_exists('WC_Integration_Favizone')) :
        class WC_Favizone
        {
            /**
             * The plugin base name.
             *
             * @since 1.0.0
             * @var string
             */
            protected $plugin_name = '';
            /**
             * The working instance of the plugin.
             *
             * @since 1.0.0
             * @var WC_Favizone|null
             */
            private static $instance = null;

            /**
             * The array of templates that this plugin tracks.
             */
            protected $templates;

            /**
             * Gets the working instance of the plugin.
             *
             * @since 1.0.0
             * @return WC_Favizone|null
             */
            public static function get_instance()
            {
                if (null === self::$instance) {
                    self::$instance = new WC_Favizone();
                }

                return self::$instance;
            }

            /**
             * Construct the plugin.
             */
            public function __construct()
            {
                $this->templates = array();
                $this->plugin_name = plugin_basename(__FILE__);
                add_action('plugins_loaded', array($this, 'init'));
            }

            /**
             * Initialize the plugin.
             */
            public function init()
            {
                // Checks if WooCommerce is installed.
                if (class_exists('WC_Integration')) {
                    // Include our integration class.
                    include_once 'WC_Integration_Favizone.php';
                    // Register the integration.
                    add_filter('woocommerce_integrations', array($this, 'add_integration'));
                    require_once 'view/templates/hook/FavizoneUserHiddenElement.php';
                    require_once 'view/templates/hook/FavizoneProductHiddenElement.php';
                    require_once 'view/templates/hook/FavizoneCategoryHiddenElement.php';
                    require_once 'view/templates/hook/FavizoneCartHiddenElement.php';
                    require_once 'view/templates/hook/FavizoneSearchHiddenElement.php';
                    require_once 'view/templates/hook/FavizoneIntegrationElement.php';
                    require_once 'view/templates/hook/FavizoneLoopProductHiddenIdentifierElement.php';

                    //hook recommender
                    require_once 'view/templates/hook/recommenders/FavizoneShopElement.php';
                    require_once 'view/templates/hook/FavizoneHomeElement.php';
                    require_once 'view/templates/hook/recommenders/FavizoneCategoryElement.php';
                    require_once 'view/templates/hook/recommenders/FavizoneProductElement.php';
                    require_once 'view/templates/hook/recommenders/FavizoneCartElement.php';
                    require_once 'view/templates/hook/recommenders/FavizoneAfterCheckOutElement.php';
                    require_once 'view/templates/hook/recommenders/FavizoneSearchElement.php';
                    require_once 'view/templates/hook/FavizoneErrorElement.php';
                    require_once 'view/templates/hook/FavizoneOtherElement.php';

                    //hook admin
                    require_once 'view/templates/adminhook/FavizoneProduct.php';
                    require_once 'view/templates/adminhook/FavizoneCategory.php';

                    require_once 'classes/FavizoneExportHook.php';
                    require_once 'classes/FavizoneDownloadHook.php';
                    require_once 'classes/FavizoneOpenHook.php';


                } else {
                    // throw an admin error if you like
                }

            }

            /**
             * Add a new integration to WooCommerce.
             */
            public function add_integration($integrations)
            {
                $integrations[] = 'WC_Integration_Favizone';
                return $integrations;
            }

            /**
             * Getter for the plugin base name.
             *
             * @since 1.0.0
             * @return string
             */
            public function get_plugin_name()
            {
                return $this->plugin_name;
            }

        }

        /**
         * Redirect users after add to cart.
         */


        $WC_Integration_favizone = new WC_Favizone();

        register_activation_hook(__FILE__, 'favizone_create_db');
        function favizone_create_db()
        {
            global $wpdb;
            $favizone_charset_collate = $wpdb->get_charset_collate();
            $favizone_table_name = $wpdb->prefix . 'favizone_recommender_access_key';
            $favizone_sql = "CREATE TABLE $favizone_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		sites_id varchar(255) NOT NULL,
		lang varchar(255) NOT NULL,
		access_key varchar(255) NOT NULL,
		shop_id varchar(255) NOT NULL,
		ab_test BOOLEAN  NOT NULL DEFAULT 0,
		ab_diff INT NOT NULL DEFAULT '0',
		UNIQUE KEY id (id)
	) $favizone_charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($favizone_sql);
        }
    endif;

}
