<?php
/**
 * Created by PhpStorm.
 * User: Appsnsites
 * Date: 11/12/2017
 * Time: 10:25
 */

add_action('init', 'favizone_error_add_shortcode', 99);
/**
 * favizone error add shortcode
 */
function favizone_error_add_shortcode(){
    add_shortcode('favizone_error','insert_favizone_error_func');

}

/**
 * insert favizone error func
 * @param $favizone_atts
 * @return string
 */
function insert_favizone_error_func($favizone_atts) {
    $favizone_content_id ="";
    $favizone_content_class ="";
    extract( shortcode_atts( array(
        'favizone_content_id' => false,
        'favizone_content_class' => false
    ), $favizone_atts) );
    if($favizone_content_id || $favizone_content_class){
        $favizone_content_id =  $favizone_atts['favizone_content_id'];
        $favizone_content_class = $favizone_atts['favizone_content_class'];
        if(isset($favizone_content_id)){
            $favizone_html = "<div id='favizone_error_page_after_id_".$favizone_content_id."' class=''>".
                "</div>";
        ?>
            <script>
                jQuery(document).ready(function(){
                    console.log(jQuery('#<?php echo $favizone_content_id;?>'));
                jQuery('#<?php echo $favizone_content_id;?>').append("<?php echo $favizone_html;?>");
                });

            </script>
        <?php
        }
        if(isset($favizone_content_class)){
            //favizone_error_page_after_class_" .$contentclass.
            $favizone_html = "<div id='favizone_error_page_after_class_".$favizone_content_class."' class=''>".
                "</div>";
            ?>
            <script>
                jQuery(document).ready(function(){
                    console.log(jQuery('.<?php echo $favizone_content_class;?>'));
                jQuery('.<?php echo $favizone_content_class;?>').append("<?php echo $favizone_html;?>");
                });

            </script>
        <?php
        }
    }else {
        return '<div id="favizone_error_page" style="display: none">error</div>';
}
}



