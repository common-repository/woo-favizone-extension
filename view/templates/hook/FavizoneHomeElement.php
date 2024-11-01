<?php
/**
 * Created by PhpStorm.
 * User: Rami Boukadida
 * Date: 06/12/2017
 * Time: 10:34
 */


add_action('init', 'favizone_home_add_shortcode', 99);
/**
 * favizone home add shortcode
 */
function favizone_home_add_shortcode()
{
    add_shortcode('favizone_home', 'insert_favizone_home_func');

}

/**
 * Insert favizone home func
 * @param $favizone_atts
 * @return string
 */
function insert_favizone_home_func($favizone_atts)
{
    $favizone_content_id = "";
    $favizone_content_class = "";
    extract(shortcode_atts(array(
        'favizone_content_id' => false,
        'favizone_content_class' => false
    ), $favizone_atts));
    if ($favizone_content_id || $favizone_content_class) {
        $favizone_content_id = $favizone_atts['favizone_content_id'];
        $favizone_content_class = $favizone_atts['favizone_content_class'];
        if (isset($favizone_content_id)) {
            //favizone_home_page_after_id_-" .$contentid
            $favizone_html = "<div id='favizone_home_page_after_id_" . $favizone_content_id . "' class=''>" .
                "</div>";
            ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('#<?php echo $favizone_content_id;?>').append("<?php echo $favizone_html;?>");
                });
            </script>
            <?php
        }
        if (isset($favizone_content_class)) {
            //favizone_home_page_after_class_" .$contentclass
            $favizone_html = "<div id='favizone_home_page_after_class_" . $favizone_content_class . "' class=''>" .
                "</div>";
            ?>
            <script>
                jQuery(document).ready(function () {
                    console.log(jQuery('.<?php echo $favizone_content_class;?>'));
                    jQuery("<?php echo $favizone_html;?>").insertAfter(".<?php echo $favizone_content_class;?>");
                });
            </script>
            <?php
        }
    } else {
        return '<div id="favizone_home_page"></div>';
    }
}



