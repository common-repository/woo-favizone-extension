<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 22/11/2017
 * Time: 12:10
 */

/**
 * Class FavizoneMetaProduct
 */
class FavizoneMetaProduct
{
    /**
     * @var array
     */
    private $favizone_product_data = array();
    /**
     * @var string
     */
    private $favizone_category_names = "";
    /**
     * @var array
     */
    private $favizone_categories_names = array();
    /**
     * @var array
     */
    private $favizone_categories_ids = array();
    /**
     * @var array
     */
    private $favizone_tags = array();
    /**
     * @var string
     */
    private $favizone_brands = "";
    /**
     * @var array
     */
    private $favizone_attributes = array();

    /**
     * Favizone load product
     * @param $favizone_product
     * @return array|bool
     */
    public function favizone_load_product($favizone_product)
    {
        $this->favizone_init_data_product();
        if($favizone_product->is_type('simple')){
            $this->setFavizoneProductData("shop_id", get_current_blog_id());
            $this->setFavizoneProductData("identifier", $favizone_product->get_id());
            $this->setFavizoneProductData("refecence", get_post_meta($favizone_product->get_id(), '_sku', true ));
            //description
            $favizone_description = $favizone_product->get_description();
            ($favizone_description !== '' ) ? $this->setFavizoneProductData("description", $favizone_description) : true;
            //shortDescription
            $favizone_short_description = $favizone_product->get_short_description();
            ($favizone_short_description !== '' ) ? $this->setFavizoneProductData("shortDescription", $favizone_short_description) : true;
            //price
            $favizone_price = $favizone_product->get_price();
            ($favizone_price !== "") ? $this->setFavizoneProductData("price", $favizone_product->get_price()) : true;
            //currency
            $favizone_currency = get_woocommerce_currency();
            (!empty($favizone_currency)) ? $this->setFavizoneProductData("currency", $favizone_currency) : true;
            //url
            $favizone_url = $favizone_product->get_permalink();
            ($favizone_url !== "") ? $this->setFavizoneProductData("url", $favizone_url) : true;
            //cover
            $favizone_image_size = apply_filters( 'single_product_archive_thumbnail_size', 'shop_catalog' );
            $favizone_cover = get_the_post_thumbnail_url( $favizone_product->get_id(), 'post-thumbnail' );
            if($favizone_cover !== false)
                $this->setFavizoneProductData("cover", $favizone_cover);
            $favizone_thumbnail = get_the_post_thumbnail_url( $favizone_product->get_id(), $favizone_image_size );
            if($favizone_thumbnail !== false)
                $this->setFavizoneProductData("home_cover", $favizone_thumbnail);
            //categories
            $favizone_terms = get_the_terms( $favizone_product->get_id(), 'product_cat' );
            if($favizone_terms){
                $fz_common = new FavizoneCommon();
                $home=  $fz_common->get_favizone_home_category()['homeCategory'];
                $this->favizone_generate_categories($favizone_terms, $home);
                (!empty($this->favizone_categories_names)) ? $this->setFavizoneProductData("categoriesNames", $this->favizone_categories_names) : true;
                (!empty($this->favizone_categories_ids)) ? $this->setFavizoneProductData("categories", $this->favizone_categories_ids) : true;
            }
            //quantity
            ($favizone_product->get_stock_quantity()!== null) ? $this->setFavizoneProductData("quantity", $favizone_product->get_stock_quantity()):$this->setFavizoneProductData("quantity", 0);
            //stock
            if($favizone_product->get_stock_status() === "instock"){
                $this->setFavizoneProductData("stock", true);
            }else{
                $this->setFavizoneProductData("stock", false);
            }
            //tags
            $this->get_favizone_tags_product($favizone_product->get_tag_ids());
            (!empty($this->favizone_tags)) ? $this->setFavizoneProductData("tags", $this->favizone_tags) : true;
            //lang
            $favizone_lang = get_locale();
            (!empty($favizone_lang)) ? $this->setFavizoneProductData("lang", $favizone_lang) : true;
            //title
            $favizone_title = $favizone_product->get_title();
            (!empty($favizone_title)) ? $this->setFavizoneProductData("title", $favizone_title) : true;
            //status
            $favizone_status = $favizone_product->get_status();
            if(!empty($favizone_status) ) {
                if($favizone_status === "publish"){
                    $this->setFavizoneProductData("active", true);
                }else{
                    $this->setFavizoneProductData("active", false);
                }
            }
            //brand
            $this->get_favizone_brand(get_the_terms($favizone_product->get_id(),'brand'));
            (!empty($this->favizone_brands))? $this->setFavizoneProductData("brand", $this->favizone_brands) : true;
            //created_at
            $favizone_date_created = $favizone_product->get_date_created()->date("Y/m/d g:i:s A");
            (!empty($favizone_date_created)) ? $this->setFavizoneProductData("created_at", $favizone_date_created) : true;
            $favizone_sale_price = $favizone_product->get_sale_price();
            if(!empty($favizone_sale_price)){
                $favizone_the_post_id = get_the_ID();
                $favizone_sale_price_dates_from    = ( $date = get_post_meta( $favizone_the_post_id, '_sale_price_dates_from', true ) ) ? date_i18n( 'Y-m-d', $date ) : '';
                $favizone_sale_price_dates_to    = ( $date = get_post_meta( $favizone_the_post_id, '_sale_price_dates_to', true ) ) ? date_i18n( 'Y-m-d', $date ) : '';
                $favizone_price_without_reduction = $favizone_product->get_regular_price();
                $this->setFavizoneProductData("isReduced", true);
                $this->setFavizoneProductData("sale_price", $favizone_product->get_sale_price());
                (!empty($favizone_price_without_reduction))? $this->setFavizoneProductData("price_without_reduction",$favizone_price_without_reduction):true;
                $this->setFavizoneProductData("reduction", $favizone_product->get_regular_price() - $favizone_product->get_sale_price());
              (!empty($favizone_sale_price_dates_from))? $this->setFavizoneProductData("reduction_expiry_date_from",$favizone_sale_price_dates_from):true;
                (!empty($favizone_sale_price_dates_to))? $this->setFavizoneProductData("reduction_expiry_date_to",$favizone_sale_price_dates_to):true;
                //$p2 = wc_get_price_including_tax( $product, array('price' => $product->get_price() ) );
                //$p1 = wc_get_price_including_tax( $product, array('price' => $product->get_regular_price() ) );
            }
            $favizone_attributes = $favizone_product->get_attributes();
            $this->setFavizoneProductData("hasDeclination",false);
            if(!empty($favizone_attributes)){
                $this->setFavizoneProductData("hasDeclination",true);
                $this->get_favizone_attributes($favizone_attributes);
                $this->setFavizoneProductData("facets",$this->favizone_attributes);
            }
            return $this->favizone_product_data;
        }else if($favizone_product->is_type('variable')){
            $this->setFavizoneProductData("shop_id", get_current_blog_id());
            $this->setFavizoneProductData("identifier", $favizone_product->get_id());
            $this->setFavizoneProductData("refecence", get_post_meta($favizone_product->get_id(), '_sku', true ));
            //description
            $favizone_description = $favizone_product->get_description();
            ($favizone_description !== '' ) ? $this->setFavizoneProductData("description", $favizone_description) : true;
            //shortDescription
            $favizone_short_description = $favizone_product->get_short_description();
            ($favizone_short_description !== '' ) ? $this->setFavizoneProductData("shortDescription", $favizone_short_description) : true;
            //price
            $favizone_price = $favizone_product->get_price();
            ($favizone_price !== "") ? $this->setFavizoneProductData("price", $favizone_product->get_price()) : true;
            //currency
            $favizone_currency = get_woocommerce_currency();
            (!empty($favizone_currency)) ? $this->setFavizoneProductData("currency", $favizone_currency) : true;
            //url
            $favizone_url = $favizone_product->get_permalink();
            ($favizone_url !== "") ? $this->setFavizoneProductData("url", $favizone_url) : true;
            //cover
            $favizone_image_size = apply_filters( 'single_product_archive_thumbnail_size', 'shop_catalog' );
            $favizone_cover = get_the_post_thumbnail_url( $favizone_product->get_id(), 'post-thumbnail' );
            if($favizone_cover !== false)
                $this->setFavizoneProductData("cover", $favizone_cover);
            $favizone_thumbnail = get_the_post_thumbnail_url( $favizone_product->get_id(), $favizone_image_size );
            if($favizone_thumbnail !== false)
                $this->setFavizoneProductData("home_cover", $favizone_thumbnail);

            //categories
            $favizone_terms = get_the_terms( $favizone_product->get_id(), 'product_cat' );
            if($favizone_terms){
                $fz_common = new FavizoneCommon();
                $home=  $fz_common->get_favizone_home_category()['homeCategory'];
                $this->favizone_generate_categories($favizone_terms, $home);
                (!empty($this->favizone_categories_names)) ? $this->setFavizoneProductData("categoriesNames", $this->favizone_categories_names) : true;
                (!empty($this->favizone_categories_ids)) ? $this->setFavizoneProductData("categories", $this->favizone_categories_ids) : true;
            }
            //quantity
            ($favizone_product->get_stock_quantity()!== null) ? $this->setFavizoneProductData("quantity", $favizone_product->get_stock_quantity()):$this->setFavizoneProductData("quantity", 0);
            //stock
            if($favizone_product->get_stock_status() === "instock"){
                $this->setFavizoneProductData("stock", true);
            }else{
                $this->setFavizoneProductData("stock", false);
            }
            //tags
            $this->get_favizone_tags_product($favizone_product->get_tag_ids());
            (!empty($this->favizone_tags)) ? $this->setFavizoneProductData("tags", $this->favizone_tags) : true;
            //lang
            $favizone_lang = get_locale();
            (!empty($favizone_lang)) ? $this->setFavizoneProductData("lang", $favizone_lang) : true;
            //title
            $favizone_title = $favizone_product->get_title();
            (!empty($favizone_title)) ? $this->setFavizoneProductData("title", $favizone_title) : true;
            //status
            $favizone_status = $favizone_product->get_status();
            if(!empty($favizone_status) ) {
                if($favizone_status === "publish"){
                    $this->setFavizoneProductData("active", true);
                }else{
                    $this->setFavizoneProductData("active", false);
                }
            }
            //brand
            $this->get_favizone_brand(get_the_terms($favizone_product->get_id(),'brand'));
            (!empty($this->favizone_brands))? $this->setFavizoneProductData("brand", $this->favizone_brands) : true;
            //created_at
            $favizone_date_created = $favizone_product->get_date_created()->date("Y/m/d g:i:s A");
            (!empty($favizone_date_created)) ? $this->setFavizoneProductData("created_at", $favizone_date_created) : true;
            $this->setFavizoneProductData("sale_price", $favizone_product->get_sale_price());

            $favizone_attributes = $favizone_product->get_attributes();
            $this->setFavizoneProductData("hasDeclination",false);
            if(!empty($favizone_attributes)){
                $this->setFavizoneProductData("hasDeclination",true);
                $this->get_favizone_attributes($favizone_attributes);
                $this->setFavizoneProductData("facets",$this->favizone_attributes);
            }
            return $this->favizone_product_data;
        }
    return false;
    }

    /**
     * Set Favizone Product Data
     * @param $attr
     * @param $value
     */
    public function setFavizoneProductData($attr, $value){
        $this->favizone_product_data[$attr] = $value;
    }

    /**
     * Favizone generate categories
     * @param $favizone_terms
     */
    private function favizone_generate_categories($favizone_terms,$home)
    {
        foreach ($favizone_terms as $favizone_term) {
            $this->favizone_category_names ="";
            $this->favizone_generate_categories_names($favizone_term );
            $this->favizone_category_names = $home."/". $this->favizone_category_names ;
            // $cat =explode("/", $this->categoryNames);
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
            $this->favizone_generate_categories_ids($this->get_favizone_parent_path($favizone_cat));
        }
    }

    /**
     * Get favizone parent path
     * @param $favizone_path
     * @return string
     */
    public function get_favizone_parent_path($favizone_path){
        $favizone_terms = explode( "/", $favizone_path );
        array_splice( $favizone_terms, -1 );

        return implode( "/", $favizone_terms );
    }

    /**
     * Get favizone tags product
     * @param $tag_ids
     */
    private function get_favizone_tags_product($tag_ids)
    {
        /*foreach ($tag_ids as $tag_id) {
            array_push($this->tags, get_tag($tag_id)->name);
        }*/
        $terms = get_the_terms( get_the_ID(), 'product_tag' );
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
            foreach ( $terms as $term ) {
                array_push($this->favizone_tags, $term->name);
            }
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
        if(!is_wp_error($favizone_terms_brand)){
            if($favizone_terms_brand){
                foreach ($favizone_terms_brand as $favizone_term){
                    ($this->favizone_brands !== "") ? $this->favizone_brands = $this->favizone_brands." , ".$favizone_term->name : $this->favizone_brands = $favizone_term->name;
                }
            }
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
                foreach ($favizone_terms as $term){
                    array_push($favizone_values, $term->name);
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
}