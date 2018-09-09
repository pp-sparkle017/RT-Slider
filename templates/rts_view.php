<div class="wrap">
    <h1><?php esc_html_e(RTS_NAME, 'rts') ?></h1>
    <div id="sort_message"></div>
    <form method="post" id="rts_frm">
        <table class="form-table" id="tbl_images">
            <tr class="section_title">
                <td colspan="2"><?php esc_html_e("Slider Images", "rts"); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e("Choose Slider Images", "rts"); ?></th>
                <td><button id="upload-button" type="button" class="button">
                        <i class="dashicons dashicons-format-gallery"></i><?php esc_html_e(" Choose Images", 'rts') ?>
                    </button>
                    <button type="button" class="button button-secondary remove_images">
                        <i class="dashicons dashicons-trash"></i> <?php esc_html_e(" Remove Images", 'rts') ?>
                    </button>
                    <input id="image-url" type="hidden" name="urls" />
                    <input type='hidden' name='attachments' id='attachments' value=''>
                    <div id="image-prev">
                        <p><?php esc_html_e("No Slider Images", "rts"); ?></p>
                    </div>
                </td>
            </tr>
        </table>
        <table class="form-table" id="tbl_general_setting">
            <tr class="section_title">
                <td colspan="2"><?php esc_html_e("General settings", "rts"); ?></td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e("Items", "rts"); ?>
                </th>
                <td>
                    <?php
                    $items = array("Single", "Multiple");
                    foreach ($items as $key => $value) {
                        $selected = $settings['items'] == $key ? "checked" : "";
                        echo sprintf("<input type='radio' class='items' name='items' value='%s' %s> %s ", $key, $selected, $value);
                    }
                    ?>
                </td>
            </tr>
            <tr class="multiple_settings">
                <th>
                    <?php esc_html_e("Slide to show", "rts"); ?>
                </th>
                <td>
                    <input type="number" min="1" max="5" name="slide_to_show" value="<?php echo !empty($settings['slide_to_show']) ? esc_attr($settings['slide_to_show']) : 1; ?>">
                </td>
            </tr>
            <tr class="multiple_settings">
                <th>
                    <?php esc_html_e("Slide to scroll", "rts"); ?>
                </th>
                <td>
                    <input type="number" min="1" max="5" name="slide_to_scroll" value="<?php echo !empty($settings['slide_to_scroll']) ? esc_attr($settings['slide_to_scroll']) : 1; ?>">
                </td>
            </tr>
            <tr class="multiple_settings">
                <th>
                    <?php esc_html_e("Center Mode", "rts"); ?>
                </th>
                <td>
                    <?php
                    $center = array("No", "Yes");
                    foreach ($center as $key => $value) {
                        $selected = $settings['center_mode'] == $key ? "checked" : "";
                        echo sprintf("<input type='radio' name='center_mode' value='%s' %s> %s ", $key, $selected, $value);
                    }
                    ?>
                </td>
            </tr>
            <tr class="multiple_settings">
                <th>
                    <?php esc_html_e("Lazy Loading", "rts"); ?>
                </th>
                <td>
                    <?php
                    $lazy = array(0 => "No", "ondemand" => "Yes");
                    foreach ($lazy as $key => $value) {
                        $selected = $settings['lazy_load'] == $key ? "checked" : "";
                        echo sprintf("<input type='radio' name='lazy_load' value='%s' %s> %s ", $key, $selected, $value);
                    }
                    ?>
                </td>
            </tr>
            <tr id="fade">
                <th>
                    <?php esc_html_e("Fade", "rts"); ?>
                </th>
                <td>
                    <?php
                    $fade = array("No", "Yes");
                    foreach ($fade as $key => $value) {
                        $selected = $settings['fade'] == $key ? "checked" : "";
                        echo sprintf("<input type='radio' name='fade' value='%s' %s> %s ", $key, $selected, $value);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e("Auto Play", "rts"); ?>
                </th>
                <td>
                    <?php
                    $auto = array("No", "Yes");
                    foreach ($auto as $key => $value) {
                        $selected = $settings['autoplay'] == $key ? "checked" : "";
                        echo sprintf("<input type='radio' name='autoplay' value='%s' %s> %s ", $key, $selected, $value);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e("Speed", "rts"); ?>
                </th>
                <td>
                    <?php $speed = !empty($settings['speed']) ? $settings['speed'] : 600; ?>
                    <input type="number" name="speed" value="<?php echo esc_attr($speed) ?>">
                </td>
            </tr>
        </table>
        <table class="form-table" id="tbl_nav_settings">
            <tr class="section_title">
                <td colspan="2"><?php esc_html_e("Navigation and pagination settings", "rts"); ?></td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e("Bullets", "rts"); ?>
                </th>
                <td>
                    <?php
                    $bullets = array("No", "Yes");
                    foreach ($bullets as $key => $value) {
                        $selected = $settings['bullets'] == $key ? "checked" : "";
                        echo sprintf("<input type='radio' name='bullets' value='%s' %s> %s ", $key, $selected, $value);
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e("Arrows", "rts"); ?>
                </th>
                <td>
                    <?php
                    $arrows = array("No", "Yes");
                    foreach ($arrows as $key => $value) {
                        $selected = $settings['arrows'] == $key ? "checked" : "";
                        echo sprintf("<input type='radio' name='arrows' value='%s' %s> %s ", $key, $selected, $value);
                    }
                    ?>
                </td>
            </tr>

            <tr>
                <th>
                    <?php esc_html_e("Bullet Color", "rts"); ?>
                </th>
                <td>
                    <input type="text" name="bullet_color" class="colors" value="<?php echo esc_attr(!empty($settings['bullet_color']) ? $settings['bullet_color'] : "#000") ?>">
                </td>
            </tr>
            <tr>
                <th>
                    <?php esc_html_e("Arrow Color", "rts"); ?>
                </th>
                <td>
                    <input type="text" name="arrow_color" class="colors" value="<?php echo esc_attr(!empty($settings['arrow_color']) ? $settings['arrow_color'] : "#000") ?>">
                </td>
            </tr>
        </table>
        <div class="rt_info">
            <h3><?php _e("How to use ?","rts") ?></h3>
            <h4><?php _e("Add <b>[rt_slideshow]</b> in your post/page to display this slider.","rts") ?></h4>
        </div>
        <input type="submit" name="save_rt_slider" id="save_rt_slider" value="<?php echo esc_attr("Save Slider", "rts") ?>" class="button button-primary"/>
        <a id="delete_all_images" class="button button-primary"> <?php echo esc_attr("Delete All Images", "rts") ?> </a>
        
    </form>
    
    <div id="image-sort">
        <?php
        if ($slider_images) {
            asort($slider_images);
            ?>
            <?php
            echo '<ul id="slider_images">';
            foreach ($slider_images as $key => $value) {
                echo sprintf("<li id='%s'><div class='rt_delete'><a href='javascript:void(0);'>x</a></div><img class='image-preview' src='%s'></li>", $key, wp_get_attachment_url($key));
            }
            echo '</ul>';
            ?>
            <?php
        }
        ?>
        
    </div>
</div>