<?php

if (!class_exists("RTS_Main")) {

    class RTS_Main {

        function __construct() {
            add_action('admin_menu', array($this, 'create_admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_style_and_scripts'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_style_and_scripts'));
            add_action('init', array($this, 'save_images'));
            add_action('wp_ajax_save_order', array($this, 'save_order'));
            add_action('wp_ajax_delete_image', array($this, 'delete_image'));
            add_shortcode('rt_slideshow', array($this, 'rt_slideshow'));
            add_action('wp_ajax_delete_all_images', array($this, 'delete_all_images'));
        }

        function create_admin_menu() {
            add_submenu_page('options-general.php', RTS_NAME, RTS_NAME, 'manage_options', 'rts_settings', array($this, 'load_template'));
        }

        function enqueue_style_and_scripts() {
            wp_enqueue_style('rts_style', RTS_URL . 'assets/css/rts_style.css');
            if (is_admin()) {
                wp_enqueue_media();
                wp_enqueue_style('colorcss', RTS_URL . 'assets/css/jquery.minicolors.css');
                wp_enqueue_script('colorjs', RTS_URL . 'assets/js/jquery.minicolors.js', array('jquery'));
                wp_enqueue_script('rts_js', RTS_URL . 'assets/js/rts.js', array('jquery', 'jquery-ui-sortable'));
                wp_localize_script('rts_js', 'rts_obj', array(
                    'security' => wp_create_nonce('reorder_nonce'),
                    'select_images' => __('Choose Slider Images', 'rts'),
                    'add' => __('Add', 'rts'),
                    'success' => __('Image order has been changed', 'rts'),
                    'error' => __('There was an error while savingsort order', 'rts'),
                    'confirm' => __("Are you sure you want to remove all images ?", "rts")
                ));
            } else {
                wp_enqueue_style('slick-theme', RTS_URL . 'assets/slick/slick.css');
                wp_enqueue_style('slick', RTS_URL . 'assets/slick/slick-theme.css');
                wp_enqueue_script('slick-js-min', RTS_URL . 'assets/slick/slick.min.js', array('jquery'));
                wp_enqueue_script('rt_slider-js', RTS_URL . 'assets/js/rt_slider.js', array('jquery'));
                $settings = get_option('rt_slider_options');
                $slider_images = get_option('rt_slider_images');
                wp_localize_script('rt_slider-js', 'rts_obj', array(
                    'slider' => $settings,
                    'slider_status' => !empty($slider_images) ? "active" : "inactive",
                ));
            }
        }

        function load_template() {
            $slider_images = get_option('rt_slider_images');
            $settings = get_option('rt_slider_options');
            include( RTS_TEMPLATE . 'rts_view.php' );
        }

        function save_images() {
            if (isset($_POST['save_rt_slider'])) {
                if (!empty($_POST['attachments'])) {
                    $arr = $_POST['attachments'];
                    $slider_images = get_option('rt_slider_images');
                    if (!is_array($slider_images)) {
                        $slider_images = array();
                    }
                    foreach ($arr as $key => $value) {
                        $counter = count($slider_images);
                        $slider_images[$value] = $counter;
                        update_option('rt_slider_images', $slider_images);
                    }
                }
                $settings = array(
                    'items' => !empty($_POST['items']) ? true : false,
                    'center_mode' => !empty($_POST['center_mode']) ? true : false,
                    'lazy_load' => !empty($_POST['lazy_load']) ? "ondemand" : false,
                    'autoplay' => !empty($_POST['autoplay']) ? true : false,
                    'fade' => !empty($_POST['fade']) ? true : false,
                    'bullets' => !empty($_POST['bullets']) ? true : false,
                    'arrows' => !empty($_POST['arrows']) ? true : false,
                    'speed' => $_POST['speed'],
                    'slide_to_show' => (int)$_POST['slide_to_show'],
                    'slide_to_scroll' => (int)$_POST['slide_to_scroll'],
                    'bullet_color' => $_POST['bullet_color'],
                    'arrow_color' => $_POST['arrow_color'],
                );
                update_option('rt_slider_options', $settings);
            }
        }

        function save_order() {
            if (!check_ajax_referer('reorder_nonce', 'security')) {
                return wp_send_json_error('Invalid Nonce');
            }
            if (!current_user_can('manage_options')) {
                return wp_send_json_error(__('You are not allow to do this !', 'rts'));
            }
            $order = $_POST['order'];
            $o = explode(',', $order);
            $counter = 0;
            $slider_images = get_option('rt_slider_images');
            foreach ($o as $item_id) {
                $slider_images[$item_id] = $counter;
                update_option('rt_slider_images', $slider_images);
                $counter++;
            }
            wp_send_json_success('Order saved');
            die();
        }

        function delete_image() {
            $key = $_POST['key'];
            $message = array();
            if (!empty($key)) {
                $slider_images = get_option('rt_slider_images');
                if (array_key_exists($key, $slider_images)) {
                    unset($slider_images[$key]);
                    update_option('rt_slider_images', $slider_images);
                    $message['msg'] = __("Image deleted");
                    $message['status'] = true;
                } else {
                    $message['msg'] = __("Image not found or it can not be deleted");
                    $message['status'] = false;
                }
            }
            echo json_encode($message);
            die;
        }

        function rt_slideshow() {
            $html = "<div class='rt_slider'>";
            $settings = get_option('rt_slider_options');
            $slider_images = get_option('rt_slider_images');
            if ($slider_images) {
                asort($slider_images);
                foreach ($slider_images as $key => $value) {
                    if ($settings['items'] == true && $settings['lazy_load'] == "ondemand") {
                        $html .= sprintf("<div class='rt_image'><img data-lazy='%s'/></div>", wp_get_attachment_url($key));
                    } else {
                        $html .= sprintf("<div class='rt_image'><img src='%s'/></div>", wp_get_attachment_url($key));
                    }
                }
            } else {
                global $post;
                if (strpos($post->post_content, '[rt_slideshow]') !== false) {
                    $html .= sprintf("<div class='shortcode_warning'><strong>%s</strong>%s</div>", __("Warning! ", "rts"), __("This slider was removed.", "rts"));
                }
            }
            $html .= "</div>";
            return $html;
        }
        function delete_all_images(){
            update_option('rt_slider_images', '');
            echo json_encode(array('success'=>__("Images deleted","dp")));
            die;
        }
    }

    new RTS_Main();
}