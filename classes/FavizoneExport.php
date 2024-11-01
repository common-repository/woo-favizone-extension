<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 16/11/2017
 * Time: 14:37
 */

require_once "FavizoneCommon.php";
require_once "FavizoneProduct.php";
require_once "FavizoneApi.php";
require_once str_replace("/", "\\", WP_PLUGIN_DIR) . "/" . FavizoneApi::FAVIZONE_PLUGIN_NAME . "/model/FavizoneMetaProduct.php";

/**
 * Class FavizoneExport
 */
class FavizoneExport
{
    /**
     * FavizoneExport constructor.
     */
    public function __construct()
    {

    }

    /**
     * Favizone export
     * @param $favizone_access_key
     * @param $favizone_site
     * @param $favizone_language
     * @return string
     */
    public function favizone_export($favizone_access_key, $favizone_site, $favizone_language)
    {
        $fz_common = new FavizoneCommon();
        $favizone_access_key_data = $fz_common->get_favizone_site_access_key($favizone_site, $favizone_language)->access_key;
        if ($favizone_access_key === $favizone_access_key_data) {
            $favizone_file_xml = str_replace("/", "\\", WP_PLUGIN_DIR) . '\\' . FavizoneApi::FAVIZONE_PLUGIN_NAME . '\favizoneexport\favizone-export-cataloge-' . $favizone_site . '.xml';
            if (file_exists($favizone_file_xml)) {
                unlink($favizone_file_xml);
            } else {
                $file = str_replace("/", "\\", WP_PLUGIN_DIR) . '\\' . FavizoneApi::FAVIZONE_PLUGIN_NAME . '\favizoneexport';
                (!file_exists($file)) ? mkdir($file) : true;
            }
            $this->favizone_export_product_data(1, $favizone_file_xml, $favizone_site, $favizone_language);
            return "success  " . $favizone_access_key . " " . $favizone_site;
        } else {
            return "error ";

        }
    }

    /**
     * Favizone export product data
     * @param int $favizone_paged
     * @param $favizone_file_xml
     * @param $favizone_site
     * @param $favizone_lang
     * @return array
     */
    function favizone_export_product_data($favizone_paged = 1, $favizone_file_xml, $favizone_site, $favizone_lang)
    {
        $fz_product = new FavizoneProduct();
        $favizone_products_collection = array();
        $favizone_loop = new WP_Query(array(
                'post_type' => array('product', 'product_variation'),
                'posts_per_page' => $fz_product->favizone_limit,
                'paged' => $favizone_paged)
        );
        while ($favizone_loop->have_posts()) : $favizone_loop->the_post();
            global $product;
            $fz_metap_roduct = new FavizoneMetaProduct();
            $favizone_product_data = $fz_metap_roduct->favizone_load_product($product);
            array_push($favizone_products_collection, $favizone_product_data);
        endwhile;
        wp_reset_query();
        $this->favizone_export_xml($favizone_products_collection, $favizone_site, $favizone_lang, $favizone_file_xml);
        if ($favizone_loop->have_posts())
            $this->favizone_export_product_data($favizone_paged + 1, $favizone_file_xml, $favizone_site, $favizone_lang);

        return $favizone_products_collection;
    }

    /**
     * Favizone export xml
     * @param $favizone_products
     * @param $favizone_id_shop
     * @param $favizone_iso_code
     * @param $favizone_file_xml
     */
    public function favizone_export_xml($favizone_products, $favizone_id_shop, $favizone_iso_code, $favizone_file_xml)
    {
        $favizone_dom = new DomDocument('1.0', 'utf-8');
        if (file_exists($favizone_file_xml)) {

            $favizone_dom->load($favizone_file_xml);
            $favizone_catalog = $favizone_dom->getElementById("catalog");
        } else {
            $favizone_catalog = $favizone_dom->appendChild($favizone_dom->createElement('catalog'));
        }
        try {
            $favizone_country = $favizone_dom->createAttribute('country');
            $favizone_country->appendChild($favizone_dom->createTextNode($favizone_iso_code));
            $favizone_catalog->appendChild($favizone_country);
            $id_shop = $favizone_dom->createAttribute('idShop');
            $id_shop->appendChild($favizone_dom->createTextNode($favizone_id_shop));
            $favizone_catalog->appendChild($id_shop);
            $favizone_catalog->setAttribute('xml:id', 'catalog');
            foreach ($favizone_products as $favizone_product) {
                $favizone_product_element = $favizone_dom->createElement('product');
                //add id_product
                if (isset($favizone_product['identifier'])) {
                    $favizone_identifier = $favizone_product['identifier'];
                    $favizone_id_product = $favizone_dom->createElement('id_product');
                    $favizone_cdata_id_product = $favizone_id_product->ownerDocument->createCDATASection($favizone_identifier);
                    $favizone_id_product->appendChild($favizone_cdata_id_product);
                    $favizone_product_element->appendChild($favizone_id_product);
                }
                //add name_product
                if (isset($favizone_product['title'])) {
                    $favizone_title = $favizone_product['title'];
                    $favizone_name_product = $favizone_dom->createElement('name_product');
                    $favizone_cdata_name_product = $favizone_name_product->ownerDocument->createCDATASection($favizone_title);
                    $favizone_name_product->appendChild($favizone_cdata_name_product);
                    $favizone_product_element->appendChild($favizone_name_product);
                }
                //add reference_product
                if (isset($favizone_product['reference'])) {
                    $favizone_reference = $favizone_product['reference'];
                    $favizone_reference_product = $favizone_dom->createElement('reference_product');
                    $favizone_cdata_reference_product = $favizone_reference_product->ownerDocument->createCDATASection($favizone_reference);
                    $favizone_reference_product->appendChild($favizone_cdata_reference_product);
                    $favizone_product_element->appendChild($favizone_reference_product);
                }
                //add lang
                if (isset($favizone_product['lang'])) {
                    $favizone_lang = $favizone_product['lang'];
                    $favizone_manufacturer = $favizone_dom->createElement('lang');
                    $favizone_cdata_manufacturer = $favizone_manufacturer->ownerDocument->createCDATASection($favizone_lang);
                    $favizone_manufacturer->appendChild($favizone_cdata_manufacturer);
                    $favizone_product_element->appendChild($favizone_manufacturer);
                }
                //categories Names
                if (isset($favizone_product['categoriesNames'])) {
                    $favizone_categories_names = $favizone_product['categoriesNames'];
                    if (is_array($favizone_categories_names) && !empty($favizone_categories_names)) {
                        $favizone_categories_names_element = $favizone_dom->createElement('categoriesNames');
                        foreach ($favizone_categories_names as $index => $favizone_category_name) {
                            $favizone_sub_category = $favizone_dom->createElement('category');
                            $favizone_cdata_sub_category = $favizone_sub_category->ownerDocument->createCDATASection($favizone_category_name);
                            $favizone_sub_category->appendChild($favizone_cdata_sub_category);
                            $favizone_categories_names_element->appendChild($favizone_sub_category);
                        }
                        $favizone_product_element->appendChild($favizone_categories_names_element);
                    }
                }
                //categories
                if (isset($favizone_product['categories'])) {
                    $favizone_categories = $favizone_product['categories'];
                    if (is_array($favizone_categories) && !empty($favizone_categories)) {
                        $favizone_categories_element = $favizone_dom->createElement('categories');
                        foreach ($favizone_categories as $index => $favizone_category) {
                            $favizone_sub_category = $favizone_dom->createElement('category');
                            $favizone_cdata_sub_category = $favizone_sub_category->ownerDocument->createCDATASection($favizone_category);
                            $favizone_sub_category->appendChild($favizone_cdata_sub_category);
                            $favizone_categories_element->appendChild($favizone_sub_category);
                        }
                        $favizone_product_element->appendChild($favizone_categories_element);
                    }
                }
                //tags
                if (isset($favizone_product['tags'])) {
                    $favizone_tags = $favizone_product['tags'];
                    if (is_array($favizone_tags) && !empty($favizone_tags)) {
                        $favizone_tags_element = $favizone_dom->createElement('tags');
                        foreach ($favizone_tags as $index => $favizone_tag) {
                            $favizone_sub_tag = $favizone_dom->createElement('tag');
                            $favizone_cdata_sub_tag = $favizone_sub_tag->ownerDocument->createCDATASection($favizone_tag);
                            $favizone_sub_tag->appendChild($favizone_cdata_sub_tag);
                            $favizone_tags_element->appendChild($favizone_sub_tag);
                        }
                        $favizone_product_element->appendChild($favizone_tags_element);
                    }
                }
                //hasDeclination
                if (isset($favizone_product['hasDeclination'])) {
                    $favizone_has_declination = $favizone_product['hasDeclination'];
                    $favizone_has_declination_element = $favizone_dom->createElement('hasDeclination');
                    $favizone_cdata_has_declination = $favizone_has_declination_element->ownerDocument->createCDATASection($favizone_has_declination);
                    $favizone_has_declination_element->appendChild($favizone_cdata_has_declination);
                    $favizone_product_element->appendChild($favizone_has_declination_element);
                }
                if (isset($favizone_product['facets'])) {
                    $favizone_facets = $favizone_product['facets'];
                    if (is_array($favizone_facets) && !empty($favizone_facets)) {
                        $favizone_facets_element = $favizone_dom->createElement('facets');
                        foreach ($favizone_facets as $favizone_facet) {
                            if (is_array($favizone_facet) && !empty($favizone_facet)) {
                                foreach ($favizone_facet as $index => $favizone_fa) {
                                    $favizone_sub_facets_element = $favizone_dom->createElement($this->removeSpecialCharacters($index));
                                    foreach ($favizone_fa as $favizone_sub_facet) {
                                        $favizone_sub_facet_element = $favizone_dom->
                                        createElement('sub_' . $this->removeSpecialCharacters($index));
                                        $favizone_cdata_sub_facet = $favizone_sub_facet_element->ownerDocument->createCDATASection($favizone_sub_facet);
                                        $favizone_sub_facet_element->appendChild($favizone_cdata_sub_facet);
                                        $favizone_sub_facets_element->appendChild($favizone_sub_facet_element);
                                    }
                                    $favizone_facets_element->appendChild($favizone_sub_facets_element);
                                }

                            }
                        }
                        $favizone_product_element->appendChild($favizone_facets_element);
                    }
                }

                //stock
                if (isset($favizone_product['stock'])) {
                    $favizone_stock = $favizone_product['stock'];
                    $favizone_stock_element = $favizone_dom->createElement('stock');
                    $favizone_cdata_stock = $favizone_stock_element->ownerDocument->createCDATASection($favizone_stock);
                    $favizone_stock_element->appendChild($favizone_cdata_stock);
                    $favizone_product_element->appendChild($favizone_stock_element);
                }
                //quantity
                if (isset($favizone_product['quantity'])) {
                    $favizone_quantity = $favizone_product['quantity'];
                    $favizone_quantity_element = $favizone_dom->createElement('quantity');
                    $favizone_cdata_quantity = $favizone_quantity_element->ownerDocument->createCDATASection($favizone_quantity);
                    $favizone_quantity_element->appendChild($favizone_cdata_quantity);
                    $favizone_product_element->appendChild($favizone_quantity_element);
                }
                //available_for_order
                if (isset($favizone_product['available_for_order'])) {
                    $favizone_available_for_order = $favizone_product['available_for_order'];
                    $favizone_available_for_order_element = $favizone_dom->createElement('available_for_order');
                    $favizone_cdata_available_for_order = $favizone_available_for_order_element->ownerDocument
                        ->createCDATASection($favizone_available_for_order);
                    $favizone_available_for_order_element->appendChild($favizone_cdata_available_for_order);
                    $favizone_product_element->appendChild($favizone_available_for_order_element);
                }
                //active
                if (isset($favizone_product['active'])) {
                    $favizone_active = $favizone_product['active'];
                    $favizone_active_element = $favizone_dom->createElement('active');
                    $favizone_cdata_active = $favizone_active_element->ownerDocument->createCDATASection($favizone_active);
                    $favizone_active_element->appendChild($favizone_cdata_active);
                    $favizone_product_element->appendChild($favizone_active_element);
                }
                //brand
                if (isset($favizone_product['brand'])) {
                    $favizone_brand = $favizone_product['brand'];
                    $favizone_brand_element = $favizone_dom->createElement('brand');
                    $favizone_cdata_brand = $favizone_brand_element->ownerDocument->createCDATASection($favizone_brand);
                    $favizone_brand_element->appendChild($favizone_cdata_brand);
                    $favizone_product_element->appendChild($favizone_brand_element);
                }
                //price
                if (isset($favizone_product['price'])) {
                    $favizone_price = $favizone_product['price'];
                    $favizone_price_element = $favizone_dom->createElement('price');
                    $favizone_cdata_price = $favizone_price_element->ownerDocument->createCDATASection($favizone_price);
                    $favizone_price_element->appendChild($favizone_cdata_price);
                    $favizone_product_element->appendChild($favizone_price_element);
                }
                //wholesale_price
                if (isset($favizone_product['wholesale_price'])) {
                    $favizone_wholesale_price = $favizone_product['wholesale_price'];
                    $favizone_wholesale_price_element = $favizone_dom->createElement('wholesale_price');
                    $favizone_cdata_wholesale_price = $favizone_wholesale_price_element->ownerDocument->createCDATASection($favizone_wholesale_price);
                    $favizone_wholesale_price_element->appendChild($favizone_cdata_wholesale_price);
                    $favizone_product_element->appendChild($favizone_wholesale_price_element);
                }
                //currency
                if (isset($product['currency'])) {
                    $favizone_currency = $favizone_product['currency'];
                    $favizone_currency_element = $favizone_dom->createElement('currency');
                    $favizone_cdata_currency = $favizone_currency_element->ownerDocument->createCDATASection($favizone_currency);
                    $favizone_currency_element->appendChild($favizone_cdata_currency);
                    $favizone_product_element->appendChild($favizone_currency_element);
                }
                //id_shop
                if (isset($favizone_product['id_shop'])) {
                    $favizone_id_shop = $favizone_product['id_shop'];
                    $favizone_id_shop_element = $favizone_dom->createElement('id_shop');
                    $favizone_cdata_id_shop = $favizone_id_shop_element->ownerDocument->createCDATASection($favizone_id_shop);
                    $favizone_id_shop_element->appendChild($favizone_cdata_id_shop);
                    $favizone_product_element->appendChild($favizone_id_shop_element);
                }
                if (isset($favizone_product['url'])) {
                    $favizone_url = $favizone_product['url'];
                    //add url
                    $favizone_url_product = $favizone_dom->createElement('url');
                    $favizone_cdata_url_product = $favizone_url_product->ownerDocument->createCDATASection($favizone_url);
                    $favizone_url_product->appendChild($favizone_cdata_url_product);
                    $favizone_product_element->appendChild($favizone_url_product);
                }
                if (isset($favizone_product['cover'])) {
                    $favizone_cover = $favizone_product['cover'];
                    //add cover
                    $favizone_image_product = $favizone_dom->createElement('cover');
                    $favizone_cdata_image_product = $favizone_image_product->ownerDocument->createCDATASection($favizone_cover);
                    $favizone_image_product->appendChild($favizone_cdata_image_product);
                    $favizone_product_element->appendChild($favizone_image_product);
                }
                //add home cover
                if (isset($favizone_product['home_cover'])) {
                    $favizone_home_cover = $favizone_product['home_cover'];
                    $favizone_home_cover_product = $favizone_dom->createElement('home_cover');
                    $favizone_cdata_home_cover_product = $favizone_home_cover_product->ownerDocument->createCDATASection($favizone_home_cover);
                    $favizone_home_cover_product->appendChild($favizone_cdata_home_cover_product);
                    $favizone_product_element->appendChild($favizone_home_cover_product);
                }
                //published_date
                if (isset($favizone_product['published_date'])) {
                    $favizone_published_date = $favizone_product['published_date'];
                    $favizone_published_date_element = $favizone_dom->createElement('published_date');
                    $favizone_cdata_published_date = $favizone_published_date_element->ownerDocument->createCDATASection($favizone_published_date);
                    $favizone_published_date_element->appendChild($favizone_cdata_published_date);
                    $favizone_product_element->appendChild($favizone_published_date_element);
                }

                //isReduced
                if (isset($favizone_product['isReduced'])) {
                    $favizone_is_reduced = $favizone_product['isReduced'];
                    $favizone_is_reduced_element = $favizone_dom->createElement('isReduced');
                    $favizone_cdata_is_reduced = $favizone_is_reduced_element->ownerDocument->createCDATASection($favizone_is_reduced);
                    $favizone_is_reduced_element->appendChild($favizone_cdata_is_reduced);
                    $favizone_product_element->appendChild($favizone_is_reduced_element);
                }

                //reduction
                if (isset($favizone_product['reduction'])) {
                    $favizone_reduction = $favizone_product['reduction'];
                    $favizone_reduction_element = $favizone_dom->createElement('reduction');
                    $favizone_cdata_reduction = $favizone_reduction_element->ownerDocument->createCDATASection($favizone_reduction);
                    $favizone_reduction_element->appendChild($favizone_cdata_reduction);
                    $favizone_product_element->appendChild($favizone_reduction_element);
                }

                //reduction_type
                if (isset($favizone_product['reduction_type'])) {
                    $favizone_reduction_type = $favizone_product['reduction_type'];
                    $favizone_reduction_type_element = $favizone_dom->createElement('reduction_type');
                    $favizone_cdata_reduction_type = $favizone_reduction_type_element->ownerDocument->createCDATASection($favizone_reduction_type);
                    $favizone_reduction_type_element->appendChild($favizone_cdata_reduction_type);
                    $favizone_product_element->appendChild($favizone_reduction_type_element);
                }

                //reduction_tax
                if (isset($favizone_product['reduction_tax'])) {
                    $favizone_reduction_tax = $favizone_product['reduction_tax'];
                    $favizone_reduction_tax_element = $favizone_dom->createElement('reduction_tax');
                    $favizone_cdata_reduction_tax = $favizone_reduction_tax_element->ownerDocument->createCDATASection($favizone_reduction_tax);
                    $favizone_reduction_tax_element->appendChild($favizone_cdata_reduction_tax);
                    $favizone_product_element->appendChild($favizone_reduction_tax_element);
                }

                //reduction_tax
                if (isset($favizone_product['price_without_reduction'])) {
                    $favizone_price_without_reduction = $favizone_product['price_without_reduction'];
                    $favizone_price_without_reduction_element = $favizone_dom->createElement('price_without_reduction');
                    $favizone_cdata_price_without_reduction = $favizone_price_without_reduction_element->ownerDocument
                        ->createCDATASection($favizone_price_without_reduction);
                    $favizone_price_without_reduction_element->appendChild($favizone_cdata_price_without_reduction);
                    $favizone_product_element->appendChild($favizone_price_without_reduction_element);
                }
                //description
                if (isset($favizone_product['description'])) {
                    $favizone_description = $favizone_product['description'];
                    $favizone_description_element = $favizone_dom->createElement('description');
                    $favizone_cdata_description = $favizone_description_element->ownerDocument->createCDATASection($favizone_description);
                    $favizone_description_element->appendChild($favizone_cdata_description);
                    $favizone_product_element->appendChild($favizone_description_element);
                }

                //shortDescription
                if (isset($favizone_product['shortDescription'])) {
                    $favizone_short_description = $favizone_product['shortDescription'];
                    $favizone_short_description_element = $favizone_dom->createElement('shortDescription');
                    $favizone_cdata_short_description = $favizone_short_description_element
                        ->ownerDocument->createCDATASection($favizone_short_description);
                    $favizone_short_description_element->appendChild($favizone_cdata_short_description);
                    $favizone_product_element->appendChild($favizone_short_description_element);
                }

                $favizone_catalog->appendChild($favizone_product_element);
            }
            $favizone_dom->formatOutput = true; // set the formatOutput attribute of domDocument to true
            // save XML as string or file

            $favizone_dom->save($favizone_file_xml); // save as file

        } catch (Exception $e) {
            echo "$e";
        }
    }

    /**
     * Remove Special Characters
     * @param String $str str
     * @return mixed|string
     */
    public function removeSpecialCharacters($str)
    {
        $charset = 'utf-8';
        $regex = '#&([A-Za-z])(?:acute|cedil|caron|circ|' .
            'grave|orn|ring|slash|th|tilde|uml);#';
        $secondRegex = "([' \" ? / .  + * ? [ ^ ] $ ( ) { } = ! < > | : -])";
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace(
            $regex,
            '\1',
            $str
        );
        // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&([A-Za-z]{2})(?:lig);#', '\1', $str);
        // supprime les autres caractères
        $str = preg_replace('#&[^;]+;#', '', $str);
        // supprime les autres caractères
        $str = str_replace(' ', '_', $str);
        // supprime les autres caractères
        $str = preg_replace($secondRegex, '', $str);
        $str = str_replace("'", "", $str);
        $str = str_replace(array('/', '\\'), '_', $str);
        return $str;
    }

    /**
     * Remove Special Characters To Categories
     * @param string $str str
     * @return mixed|string
     */
    public function removeSpecialCharactersToCategories($str)
    {
        $charset = 'utf-8';
        $regex = '#&([A-Za-z])(?:acute|cedil|caron|' .
            'circ|grave|orn|ring|slash|th|tilde|uml);#';
        $secondRegex = "([' \" ? / .  + * ? [ ^ ] $ ( ) { } = ! < > | : -])";
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace($regex, '\1', $str);
        // pour les ligatures e.g. '&oelig;'
        $str = preg_replace('#&([A-Za-z]{2})(?:lig);#', '\1', $str);
        // supprime les autres caractères
        $str = preg_replace('#&[^;]+;#', '', $str);
        // supprime les autres caractères
        $str = str_replace(' ', '_', $str);
        // supprime les autres caractères
        $str = preg_replace("(^[1-9]*)", '', $str);
        $str = str_replace("'", "", $str);
        $str = preg_replace($secondRegex, '', $str);
        return $str;
    }

}
