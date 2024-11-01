<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 06/12/2017
 * Time: 14:21
 */
add_action('create_product_cat', 'favizone_create_category', 10, 1);
add_action("edited_product_cat", "favizone_edit_category");
add_action("delete_product_cat", "favizone_delete_category");

function favizone_create_category($term_id) {
    $favizone_texonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : "";
    if($favizone_texonomy =='product_cat'){
        $favizone_term = get_term_by( 'id', $term_id, $favizone_texonomy );
        $favizone_name = isset($_POST['tag-name']) ? sanitize_text_field($_POST['tag-name']) : "";
        $favizone_thumbnail_id = isset($_POST['product_cat_thumbnail_id']) ? sanitize_text_field($_POST['product_cat_thumbnail_id']) : "";
        $favizone_path=[];
        $fz_common = new FavizoneCommon();
        $favizone_home =$fz_common->get_favizone_home_category()['nameCategory'];
        while ($favizone_term->parent != 0){
            $favizone_term = get_term_by( 'id', $favizone_term->parent, $favizone_texonomy );
            array_push($favizone_path,$favizone_term->name);
        }
        $favizone_category['nameCategory'] = $favizone_name;
        ($favizone_path)?$favizone_category['idCategory']=$favizone_home."/".implode("/",array_reverse($favizone_path))."/".$favizone_name : $favizone_category['idCategory']=$favizone_home."/".$favizone_name;
        ($favizone_path)? $favizone_category['idparent']=$favizone_home."/".implode("/",array_reverse($favizone_path)): $favizone_category['idparent']=$favizone_home;

        $favizone_category['level']=count(explode("/",$favizone_category['idCategory']))-1;
        ($favizone_category['level'] != 0)? $favizone_category['isCategoryRoot']=0 : $favizone_category['isCategoryRoot']=1;
        (!empty($favizone_thumbnail_id)) ? $favizone_category['image'] = wp_get_attachment_url( $favizone_thumbnail_id ) : $favizone_category['image']="";
        $favizone_category['url'] = get_term_link( $term_id , $favizone_texonomy);
        $favizone_shop_id = get_current_blog_id();
        $favizone_lang = get_locale();
        $favizone_auth_key = $fz_common->get_favizone_site_access_key($favizone_shop_id, $favizone_lang)->access_key;
        $fz_sender = new FavizoneSender();
        $fz_api = new FavizoneApi();
        if ($favizone_auth_key) {
            $favizone_data_to_send = array(
                "key" => $favizone_auth_key,
                "category" => $favizone_category
            ) ;
            $fz_sender->favizone_post_request($fz_api->getFavizoneHost(), $fz_api->getAddCategoryFavizoneUrl(), $favizone_data_to_send);
        }
    }

}


function favizone_edit_category($term_id)
{
    $favizone_texonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : "";
    if($favizone_texonomy =='product_cat'){
        $favizone_term = get_term_by( 'id', $term_id, $favizone_texonomy );
        $favizone_name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : "";
        $favizone_thumbnail_id = isset($_POST['product_cat_thumbnail_id']) ? sanitize_text_field($_POST['product_cat_thumbnail_id']) : "";
        $favizone_path=[];
        $fz_common = new FavizoneCommon();
        $favizone_home =$fz_common->get_favizone_home_category()['nameCategory'];
        while ($favizone_term->parent != 0){
            $favizone_term = get_term_by( 'id', $favizone_term->parent, $favizone_texonomy );
            array_push($favizone_path,$favizone_term->name);
        }
        $favizone_category['nameCategory'] = $favizone_name;
        ($favizone_path)?$favizone_category['idCategory']=$favizone_home."/".implode("/",array_reverse($favizone_path))."/".$favizone_name : $favizone_category['idCategory']=$favizone_home."/".$favizone_name;
        ($favizone_path)? $favizone_category['idparent']=$favizone_home."/".implode("/",array_reverse($favizone_path)): $favizone_category['idparent']=$favizone_home;

        $favizone_category['level']=count(explode("/",$favizone_category['idCategory']))-1;
        ($favizone_category['level'] != 0)? $favizone_category['isCategoryRoot']=0 : $favizone_category['isCategoryRoot']=1;
        (!empty($favizone_thumbnail_id)) ? $favizone_category['image'] = wp_get_attachment_url( $favizone_thumbnail_id ) : $favizone_category['image']="";
        $favizone_category['url'] = get_term_link( $term_id , $favizone_texonomy);
        $favizone_shop_id = get_current_blog_id();
        $favizone_lang = get_locale();
        $favizone_auth_key = $fz_common->get_favizone_site_access_key($favizone_shop_id, $favizone_lang)->access_key;
        $fz_sender = new FavizoneSender();
        $fz_api = new FavizoneApi();
        if ($favizone_auth_key) {
            $favizone_data_to_send = array(
                "key" => $favizone_auth_key,
                "category" => $favizone_category
            ) ;
            $fz_sender->favizone_post_request($fz_api->getFavizoneHost(), $fz_api->getUpdateCategoryFavizoneUrl(), $favizone_data_to_send);
        }
    }
}

function favizone_delete_category($favizone_term_id){
    $favizone_texonomy = isset($_POST['taxonomy']) ? sanitize_text_field($_POST['taxonomy']) : "";
    if($favizone_texonomy =='product_cat'){
        $favizone_term = get_term_by( 'id', $favizone_term_id, $favizone_texonomy );
        $favizone_name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : "";
        $favizone_path=[];
        $fz_common = new FavizoneCommon();
        $favizone_home =$fz_common->get_favizone_home_category()['nameCategory'];
        while ($favizone_term->parent != 0){
            $favizone_term = get_term_by( 'id', $favizone_term->parent, $favizone_texonomy );
            array_push($favizone_path,$favizone_term->name);
        }
        ($favizone_path)?$favizone_id_category=$favizone_home."/".implode("/",array_reverse($favizone_path))."/".$favizone_name : $favizone_id_category=$favizone_home."/".$favizone_name;
        $favizone_shop_id = get_current_blog_id();
        $favizone_lang = get_locale();
        $favizone_auth_key = $fz_common->get_favizone_site_access_key($favizone_shop_id, $favizone_lang)->access_key;
        $fz_sender = new FavizoneSender();
        $fz_api = new FavizoneApi();
        if ($favizone_auth_key) {
            $favizone_data_to_send = array(
                "key" => $favizone_auth_key,
                "id_category" => $favizone_id_category
            ) ;
            $fz_sender->favizone_post_request($fz_api->getFavizoneHost(), $fz_api->getDeleteCategoryFavizoneUrl(), $favizone_data_to_send);
        }
    }
}


