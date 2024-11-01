<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 09/11/2017
 * Time: 10:40
 */

/**
 * Class FavizoneProduct
 */
class FavizoneProduct
{
    public $favizone_product_data = array();

    public $favizone_limit = 20;

    private $favizone_category_names = "";
    private $favizone_categories_names = array();
    private $favizone_categories_ids = array();
    private $favizone_tags = array();
    private $favizone_brands = "";
    private $favizone_attributes = array();

    /**
     * FavizoneProduct constructor.
     */
    public function __construct()
    {
    }
    public function set_favizone_product_data($attr, $value){
        $this->favizone_product_data[$attr] = $value;
    }

    /**
     * Favizone tagging product data
     * @param int $favizone_paged
     * @return array
     */
    function favizone_tagging_product_data($favizone_paged = 1) {
        $favizone_products_collection = array();
        $loop = new WP_Query( array(
            'post_type' => array('product', 'product_variation'),
            'posts_per_page' => $this->favizone_limit ,
            'paged'=>$favizone_paged)
        );
        while ( $loop->have_posts() ) : $loop->the_post();
            $this->favizone_init_data_product();
            $theid = get_the_ID();
            $favizone_product = new WC_Product($theid);

            $this->set_favizone_product_data("shop_id", get_current_blog_id());
            $this->set_favizone_product_data("identifier", $theid);
            $this->set_favizone_product_data("refecence", get_post_meta($theid, '_sku', true ));
            //description
            $favizone_description = $favizone_product->get_description();
            ($favizone_description !== '' ) ? $this->set_favizone_product_data("description", $favizone_description) : true;
            //shortDescription
            $favizone_short_description = $favizone_product->get_short_description();
            ($favizone_short_description !== '' ) ? $this->set_favizone_product_data("shortDescription", $favizone_short_description) : true;
            //price
            $favizone_price = $favizone_product->get_price();
            ($favizone_price !== "") ? $this->set_favizone_product_data("price", $favizone_product->get_price()) : true;
            //currency
            $favizone_currency = get_woocommerce_currency();
            (!empty($favizone_currency)) ? $this->set_favizone_product_data("currency", $favizone_currency) : true;
            //url
            $favizone_url = $favizone_product->get_permalink();
            ($favizone_url !== "") ? $this->set_favizone_product_data("url", $favizone_url) : true;
            //cover
            $favizone_image_size = apply_filters( 'single_product_archive_thumbnail_size', 'shop_catalog' );
            $favizone_cover = get_the_post_thumbnail_url( $theid, 'post-thumbnail' );
            if($favizone_cover !== false)
                $this->set_favizone_product_data("cover", $favizone_cover);
            $favizone_thumbnail = get_the_post_thumbnail_url( $theid, $favizone_image_size );
            if($favizone_thumbnail !== false)
                $this->set_favizone_product_data("cover_thumbnail", $favizone_thumbnail);

            //categories
            $favizone_terms = get_the_terms( $theid, 'product_cat' );
            $this->favizone_generate_categories($favizone_terms);
            (!empty($this->favizone_categories_names)) ? $this->set_favizone_product_data("categoriesNames", $this->favizone_categories_names) : true;
            (!empty($this->favizone_categories_ids)) ? $this->set_favizone_product_data("categories", $this->favizone_categories_ids) : true;

            //quantity
            ($favizone_product->get_stock_quantity()!== null) ? $this->set_favizone_product_data("quantity", $favizone_product->get_stock_quantity()):$this->set_favizone_product_data("quantity", 0);
            //stock
            if($favizone_product->get_stock_status() === "instock"){
                $this->set_favizone_product_data("stock", true);
            }else{
                $this->set_favizone_product_data("stock", false);
            }
            //tags
            $this->get_favizone_tgs_product($favizone_product->get_tag_ids());
            (!empty($this->favizone_tags)) ? $this->set_favizone_product_data("tags", $this->favizone_tags) : true;
            //lang
            $favizone_lang = get_locale();
            (!empty($favizone_lang)) ? $this->set_favizone_product_data("lang", $favizone_lang) : true;
            //title
            $favizone_title = $favizone_product->get_title();
            (!empty($favizone_title)) ? $this->set_favizone_product_data("title", $favizone_title) : true;
            //status
            $favizone_status = $favizone_product->get_status();
            if(!empty($favizone_status) ) {
                if($favizone_status === "publish"){
                    $this->set_favizone_product_data("active", true);
                }else{
                    $this->set_favizone_product_data("active", false);

                }
            }
            //brand
            $this->get_favizone_brand(get_the_terms($theid,'brand'));
            (!empty($this->favizone_brands))? $this->set_favizone_product_data("brand", $this->favizone_brands) : true;
            //created_at
            $favizone_date_created = $favizone_product->get_date_created()->date("Y/m/d g:i:s A");
            (!empty($favizone_date_created)) ? $this->set_favizone_product_data("created_at", $favizone_date_created) : true;

            $this->set_favizone_product_data("sale_price", $favizone_product->get_sale_price());
            $favizone_attributes = $favizone_product->get_attributes();
            if(!empty($favizone_attributes)){
                $this->get_favizone_attributes($favizone_attributes);
                $this->set_favizone_product_data("facets",$this->favizone_attributes);
            }
            array_push($favizone_products_collection, $this->favizone_product_data);
        endwhile; wp_reset_query();

        if($loop->have_posts())
            $this->favizone_tagging_product_data($favizone_paged+1);
        return $favizone_products_collection;
    }
    /**
     * Favizone generate categories
     * @param $favizone_terms
     */
    private function favizone_generate_categories($favizone_terms)
    {
        foreach ($favizone_terms as $term) {
            $this->favizone_category_names ="";
            $this->favizone_generate_categories_names($term);
            $this->favizone_generate_categories_ids($this->favizone_category_names);
            array_push($this->favizone_categories_names,$this->favizone_category_names );
        }

    }

    /**
     * Favizone generate categories names
     * @param $favizone_term
     */
    private function favizone_generate_categories_names($favizone_term)
    {
        if($this->favizone_category_names !== "")
            $this->favizone_category_names = $favizone_term->slug."/".$this->favizone_category_names;
        else
            $this->favizone_category_names = $favizone_term->slug;

        if($favizone_term->parent !== 0){
            $parent =  get_term_by( "id", $favizone_term->parent, 'product_cat' );
            $this->favizone_generate_categories_names($parent);
        }
    }

    /**
     * Favizone generate categories ids
     * @param $favizone_cat
     */
    private function favizone_generate_categories_ids($favizone_cat)
    {
        if(!in_array($favizone_cat,$this->favizone_categories_ids)){
                array_push($this->favizone_categories_ids,$favizone_cat);
        }
        if(strpos($favizone_cat,"/")){
            $this->favizone_generate_categories_ids($this->get_favizone_Parent_path($favizone_cat));
        }
    }

    /**
     * Get favizone Parent path
     * @param $favizone_path
     * @return string
     */
    public function get_favizone_Parent_path($favizone_path){
        $favizone_terms = explode( "/", $favizone_path );
        array_splice( $favizone_terms, -1 );
        return implode( "/", $favizone_terms );
    }

    /**
     * Get favizone tgs product
     * @param $favizone_tag_ids
     */
    private function get_favizone_tgs_product($favizone_tag_ids)
    {
        foreach ($favizone_tag_ids as $favizone_tag_id){
            array_push($this->favizone_tags, get_tag($favizone_tag_id)->name);
        }
    }

    /**
     * Favizone init data product
     */
    private function favizone_init_data_product()
    {
        $this->favizone_product_data = array();
        $this->favizone_categories_names = array();
        $this->favizone_categories_ids = array();
        $this->favizone_tags = array();
        $this->favizone_brands = "";
        $this->favizone_attributes = array();
    }

    /**
     * Get favizone brand
     * @param $favizone_terms_brand
     */
    private function get_favizone_brand($favizone_terms_brand)
    {
        foreach ($favizone_terms_brand as $favizone_term){
            ($this->favizone_brands !== "") ? $this->favizone_brands = $this->favizone_brands." , ".$favizone_term->name : $this->favizone_brands = $favizone_term->name;
        }
    }

    /**
     * Get favizone attributes
     * @param $favizone_attributes
     */
    private function get_favizone_attributes($favizone_attributes)
    {
        foreach ($favizone_attributes as $favizone_attribute){
            $favizone_values = array();
            $favizone_terms = $favizone_attribute->get_terms();
            if(!empty($favizone_terms) || $favizone_terms !== null){
                foreach ($favizone_terms as $favizone_term){
                    array_push($favizone_values, $favizone_term->name);
                }
            }else {
                $favizone_options = $favizone_attribute->get_options();
                foreach ($favizone_options as $favizone_option){
                    array_push($favizone_values, $favizone_option);
                }
            }
            array_push($this->favizone_attributes,array(str_replace("pa_", "", $favizone_attribute->get_name())=>$favizone_values));
        }
    }

    /**
     * Favizone update tagging product data
     * @param $favizone_product
     * @param $favizone_site
     * @param $favizone_language
     * @param $favizone_operation_key
     */
    public function favizone_update_tagging_product_data($favizone_product, $favizone_site, $favizone_language, $favizone_operation_key){
        $fz_common = new FavizoneCommon();
        $favizone_auth_key = $fz_common->get_favizone_site_access_key($favizone_site, $favizone_language)->access_key;
        $fz_meta_product = new FavizoneMetaProduct();
        $fz_sender = new FavizoneSender();
        $fz_api = new FavizoneApi();

        if ($favizone_auth_key) {
            switch ($favizone_operation_key) {
                case "update":
                    $favizone_prod = $fz_meta_product->favizone_load_product($favizone_product);
                    $favizone_data_to_send =  array("key" => $favizone_auth_key, "product" => json_encode($favizone_prod));
                    $fz_sender->favizone_post_request($fz_api->getFavizoneHost(), $fz_api->getUpdateProductFavizoneUrl(), $favizone_data_to_send);
                    break;
                case "add":
                    $favizone_prod = $fz_meta_product->favizone_load_product($favizone_product);
                    $favizone_data_to_send =  array("key" => $favizone_auth_key, "product" => json_encode($favizone_prod));
                    $fz_sender->favizone_post_request($fz_api->getFavizoneHost(), $fz_api->getAddProductFavizoneUrl(), $favizone_data_to_send);
                    break;
                case "delete":
                    $favizone_data_to_send =  array("key" => $favizone_auth_key, "product" => $favizone_product->get_id());
                    $fz_sender->favizone_post_request($fz_api->getFavizoneHost(), $fz_api->getDeleteProductFavizoneUrl(), $favizone_data_to_send);
                    break;
            }
        }
    }
}