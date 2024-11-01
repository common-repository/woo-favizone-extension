<?php
/**
 * Created by PhpStorm.
 * User: Appsnsites
 * Date: 11/12/2017
 * Time: 10:25
 */

add_action('init', 'favizone_other_add_shortcode', 99);
/**
 * favizone other add shortcode
 */
function favizone_other_add_shortcode()
{
    add_shortcode('favizone_other', 'insert_favizone_other_func');
}

/**
 * insert favizone other func
 * @param $favizone_atts
 * @return string
 */
function insert_favizone_other_func($favizone_atts)
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
            //favizone_other_page_after_id_" .$contentid.
            $html = "<div id='favizone_other_page_after_id_" . $favizone_content_id . "' class=''>" .
                "</div>";
            ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('#<?php echo $favizone_content_id;?>').append("<?php echo $html;?>");
                });
            </script>
            <?php
        }
        if (isset($favizone_content_class)) {
            //favizone_other_page_after_class_" .$contentclass.
            $html = "<div id='favizone_other_page_after_class_" . $favizone_content_class . "' class=''>" .
                "</div>";
            ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('.<?php echo $favizone_content_class;?>').append("<?php echo $html;?>");
                });
            </script>
            <?php
        }
    } else {
        return '<div id="favizone_other_page" style="display: none"></div>';
    }
}



