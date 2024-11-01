<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 23/11/2017
 * Time: 17:33
 */
require_once 'classes/FavizoneAccount.php';
require_once 'classes/FavizoneCommon.php';
require_once 'classes/FavizoneOrder.php';
require_once 'classes/FavizoneCategory.php';
if (!class_exists('WC_Integration_Favizone')) :
    class WC_Integration_Favizone extends WC_Integration
    {
        /**
         * @var string
         */
        private $favizone_locale = "";
        /**
         * @var int
         */
        private $favizone_shop_id;
        private $fz_common;

        /**
         * Init and hook in the integration.
         */
        public function __construct()
        {
            global $woocommerce;
            $this->id = 'favizone-tagging';
            $this->method_title = __('Favizone tagging', 'woocommerce-favizone-tagging');
            $this->favizone_locale = get_locale();
            if (is_multisite()) {
                $this->favizone_shop_id = get_site()->blog_id;
            } else {
                $this->favizone_shop_id = get_current_blog_id();
            }
            // Load the settings.
            $this->fz_common = new FavizoneCommon();
            $favizone_auth_key = $this->fz_common->get_favizone_site_access_key($this->favizone_shop_id, $this->favizone_locale)->access_key;
            //$this->method_description = __('Coming soon.', 'woocommerce-favizone-tagging');
            if (!isset($favizone_auth_key)) {
                $this->method_description = __('Implements the required account email for using Favizone tagging.', 'woocommerce-favizone-tagging');
                $this->init_form_fields();
                $this->init_settings();
                $this->account_email = $this->get_option('account_email');
                add_action('woocommerce_update_options_integration_' . $this->id, array($this, 'process_admin_options'));
            } else {
                $this->method_description = __('This is a export page.', 'woocommerce-favizone-tagging');
                $this->export_form_fields();
            }
            add_filter('plugin_action_links', array($this, 'favizone_register_action_links'), 10, 2);

        }

        /**
         * Initialize integration settings form fields.
         */
        public function init_form_fields()
        {
            $this->form_fields = array(
                'account_email' => array(
                    'title' => __('Account Email', 'woocommerce-favizone-tagging'),
                    'type' => 'text',
                    'description' => __('Enter with your account email.', 'woocommerce-favizone-tagging'),
                    'desc_tip' => true,
                    'default' => ''
                )
            );
        }

        /**
         * Initialize integration settings form fields.
         */
        public function export_form_fields()
        {

            $this->form_fields = array(
                // don't forget to put your other settings here
                'customize_button' => array(
                    'favizone_class' => 'link',
                    'title' => __('Export xml', 'woocommerce-favizone-tagging'),
                    'type' => 'button',
                    'url' => $this->fz_common->get_favizone_exprot_url($this->favizone_shop_id, $this->favizone_locale)
                ),
                'download' => array(
                    'favizone_class' => 'button_download',
                    'class' => 'woocommerce-BlankState-cta button-primary button',
                    'title' => __('Download', 'woocommerce-favizone-tagging'),
                    'type' => 'button',
                    'custom_attributes' => array(
                        'onclick' => "location.href='" . $this->fz_common->get_favizone_download_url($this->favizone_shop_id) . "'",
                    ),
                    'description' => __('Download xml file.', 'woocommerce-favizone-tagging'),
                    'desc_tip' => true,
                ),
                'open' => array(
                    'favizone_class' => 'button_open',
                    'class' => 'woocommerce-BlankState-cta button-primary button',
                    'title' => __('Open', 'woocommerce-favizone-tagging'),
                    'type' => 'button',
                    'custom_attributes' => array(
                        'href' => $this->fz_common->get_favizone_open_url($this->favizone_shop_id),
                    ),
                    'description' => __('Open the xml file in browser.', 'woocommerce-favizone-tagging'),
                    'desc_tip' => true,
                )
            );
        }


        /**
         * Validate the API key
         * @see validate_settings_fields()
         */
        public function validate_account_email_field($key)
        {
            // get the posted value
            $favizone_value = sanitize_text_field($_POST[$this->plugin_id . $this->id . '_' . $key]);
            if (isset($favizone_value)) {
                $this->errors[] = $key;
                $fz_account = new FavizoneAccount();
                $account = $fz_account->favizone_create_account($favizone_value, $this->favizone_locale);
                if ($account['status'] === 'success') {
                    $fz_api = new FavizoneApi();
                    $fz_sender = new FavizoneSender();
                    $favizone_result = $fz_sender->favizone_post_request($fz_api->getFavizoneHost(), $fz_api->getAddAccountFavizoneUrl(), $account);
                    $favizone_result = json_decode($favizone_result);
                    if ($favizone_result->status) {
                        $fz_account->favizone_insert_recommender_access_key($favizone_result->application_key, $this->favizone_locale, $this->favizone_shop_id, $favizone_result->reference);
                        $favizone_res = $this->fz_common->favizone_send_check_init_done($favizone_result->application_key);
                        if ($favizone_res['response'] === 'authorized' && $favizone_res['result'] === 'Zy,]Jm9QkJ') {
                            $fz_order = new FavizoneOrder();
                            $fz_order->favizone_sender_orders_data($this->favizone_shop_id, $this->favizone_locale);
                            $fz_category = new FavizoneCategory();
                            $fz_category->favizone_send_category_data($this->favizone_shop_id, $this->favizone_locale);
                            $this->method_description = __('This is a export page.', 'woocommerce-favizone-tagging');
                            $this->export_form_fields();
                        }
                    }
                }
            } else {
            }
            return $favizone_value;
        }



        /**
         * Generate Button HTML.
         *
         * @access public
         * @param mixed $favizone_key
         * @param mixed $favizone_data
         * @since 1.0.0
         * @return string
         */
        public function generate_button_html($favizone_key, $favizone_data)
        {
            $favizone_field = $this->plugin_id . $this->id . '_' . $favizone_key;
            $favizone_defaults = array(
                'class' => 'button-secondary',
                'title' => '',
                'url' => '',
                'type' => '',
            );
            $favizone_data = wp_parse_args($favizone_data, $favizone_defaults);
            ob_start();
            ?>
            <?php if ($favizone_data['favizone_class'] === 'link') {
            global $GLOBALS;
            $GLOBALS['hide_save_button'] = true;
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php echo esc_attr($favizone_field); ?>"><?php echo wp_kses_post($favizone_data['title']); ?> </label>
                </th>
                <td class="forminp">
                    <fieldset>
                        <a href="<?php echo $favizone_data['url']; ?>"
                           target="_blank"><?php echo $favizone_data['url']; ?></a>

                    </fieldset>
                </td>
            </tr>
        <?php } ?>
            <?php if ($favizone_data['favizone_class'] === 'button_download') { ?>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($favizone_data['title']); ?></span>
                    </legend>
                    <button class="<?php echo esc_attr($favizone_data['class']); ?>" type="button"
                            name="<?php echo esc_attr($favizone_field); ?>"
                            id="<?php echo esc_attr($favizone_field); ?>"
                            style="<?php echo esc_attr($favizone_data['css']); ?>" <?php echo $this->get_custom_attribute_html($favizone_data); ?>><?php echo wp_kses_post($favizone_data['title']); ?></button>
                    <?php echo $this->get_description_html($favizone_data); ?>
                </fieldset>
            </td>
            <td></td>

        <?php } ?>
            <?php if ($favizone_data['favizone_class'] === 'button_open') { ?>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post($favizone_data['title']); ?></span>
                    </legend>
                    <a href="<?php echo $favizone_data['custom_attributes']['href']; ?>" target="_blank"
                       class="<?php echo esc_attr($favizone_data['class']); ?>" type="button"
                       name="<?php echo esc_attr($favizone_field); ?>" id="<?php echo esc_attr($favizone_field); ?>"
                       style="<?php echo esc_attr($favizone_data['css']); ?>" <?php echo $this->get_custom_attribute_html($favizone_data); ?>><?php echo wp_kses_post($favizone_data['title']); ?></a>
                    <?php echo $this->get_description_html($favizone_data); ?>
                </fieldset>
            </td>
        <?php } ?>
            <?php
            return ob_get_clean();
        }

        /**
         * Registers action links for the plugin.
         *
         * Add a shortcut link to the settings page.
         *
         * @since 1.0.0
         * @param array $links Array of already defined links
         * @param string $plugin_file The plugin base name
         * @return array
         */
        public function favizone_register_action_links($links, $plugin_file)
        {
            if ($plugin_file === WC_Favizone::get_instance()->get_plugin_name()) {
                $url = admin_url('admin.php?page=wc-settings&tab=integration&section=favizone-tagging');
                $links[] = '<a href="' . esc_attr($url) . '">' . esc_html__('Settings') . '</a>';
            }
            return $links;
        }
    }
endif;