<?php

if (!class_exists("RTS_Main")) {

    class RTS_Main {

        function __construct() {
            add_action('admin_enqueue_scripts', array($this, 'enqueue_style_and_scripts'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_style_and_scripts'));
            add_action('wp_ajax_save_order', array($this, 'save_order'));
            add_action('wp_ajax_delete_image', array($this, 'delete_image'));
            add_shortcode('rt_slideshow', array($this, 'rt_slideshow'));
            add_action('wp_ajax_delete_all_images', array($this, 'delete_all_images'));
            add_action('init', array($this, 'add_slider_post_type'));
            add_filter('manage_rt_slider_posts_columns', array($this, 'add_new_column'));
            add_filter('manage_rt_slider_posts_custom_column', array($this, 'add_new_colum_data'), 10, 2);
            add_action('add_meta_boxes', array($this, "add_rts_metabox"));
            add_action('save_post', array($this, "save_slider"));
            add_filter('mce_external_plugins',array($this, "enqueue_visual_editor_button_script"));
            add_filter('mce_buttons', array($this,'register_slider_button_in_editor'));
            add_action('wp_ajax_get_all_sliders',array($this,'get_all_sliders'));
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
                    'confirm' => __("Are you sure ?", "rts")
                ));
            } else {
                wp_enqueue_style('slick-theme', RTS_URL . 'assets/slick/slick.css');
                wp_enqueue_style('slick', RTS_URL . 'assets/slick/slick-theme.css');
                wp_enqueue_script('slick-js-min', RTS_URL . 'assets/slick/slick.min.js', array('jquery'));
                wp_enqueue_script('rt_slider-js', RTS_URL . 'assets/js/rt_slider.js', array('jquery'));
            }
        }

        function add_slider_post_type(){
            $labels = array(
                'name' => __('Sliders', 'rts'),
                'singular_name' => __('Slider', 'rts'),
                'menu_name' => __('RT Slider', 'rts'),
                'name_admin_bar' => __('Sliders', 'rts'),
                'all_items' => __('Sliders', 'rts'),
                'add_new_item' => __('Add new slider', 'rts'),
                'add_new' => __('Add new slider', 'rts'),
                'new_item' => __('New Slider', 'rts'),
                'edit_item' => __('Edit Slider', 'rts'),
                'update_item' => __('Update Slider', 'rts'),
                'view_item' => __('View Slider', 'rts'),
                'view_items' => __('View Sliders', 'rts'),
                'search_items' => __('Search Slider', 'rts'),
                'not_found' => __('Not found', 'rts'),
                'not_found_in_trash' => __('Not found in Trash', 'rts'),
            );
            $args = array(
                'label' => __('RT Slider', 'rts'),
                'labels' => $labels,
                'show_ui' => true,
                'exclude_from_search' => true,
                'supports' => array('title'),
            );
            register_post_type('rt_slider', $args);
        }
        
        function add_new_column($columns){
            if (array_key_exists('date', $columns)) {
                $new = array();
                foreach ($columns as $key => $value) {
                    if ('date' === $key) {
                        $new['rts_shortcode'] = __('Shortcode', 'rts');
                    }
                    $new[$key] = $value;
                }
            }
            return $new;
        }
        
        function add_new_colum_data($column, $post_id) {
            switch ($column) {
                case 'rts_shortcode' :
                    $shortcode = "[rt_slideshow id='$post_id']";
                    echo !empty($shortcode) ? $shortcode : "-" ;
                    break;
            }
        }
        
        function add_rts_metabox(){
            add_meta_box('rts_metabox', __("RT Slider", "rts"), array($this, "display_rts_metabox"), array('rt_slider'), 'normal', 'high');
            add_meta_box('rts_metabox_shortcode', __("RT Slider Shortcode", "rts"), array($this, "display_rts_shortcode"), array('rt_slider'), 'side');
        }
        
        function display_rts_metabox(){
            global $post;
            $slider_images = get_post_meta($post->ID,'rt_slider_images',true);
            $settings = get_post_meta($post->ID,'rt_slider_options',true);
            include( RTS_TEMPLATE . 'rts_metabox.php' );
        }
        
        function display_rts_shortcode(){
            global $post;
            include( RTS_TEMPLATE . 'rts_shortcode.php' );
        }
        
        function save_slider($id){
            $post_type = get_post_type($id);
            if ("rt_slider" != $post_type) {
                return;
            }
            if (!empty($_POST['attachments'])) {
                $arr = $_POST['attachments'];
                $slider_images = get_post_meta($id,'rt_slider_images',true);
                if (!is_array($slider_images)) {
                    $slider_images = array();
                }
                foreach ($arr as $key => $value) {
                    $counter = count($slider_images);
                    $slider_images[$value] = $counter;
                    update_post_meta($id,'rt_slider_images', $slider_images);
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
                'speed' => !empty($_POST['speed']) ? $_POST['speed'] : 600,
                'slide_to_show' => !empty($_POST['slide_to_show']) ? (int)$_POST['slide_to_show'] : 1,
                'slide_to_scroll' => !empty($_POST['slide_to_scroll']) ? (int)$_POST['slide_to_scroll'] : 1,
                'bullet_color' => !empty($_POST['bullet_color']) ? $_POST['bullet_color'] : "#000",
                'arrow_color' => !empty($_POST['arrow_color']) ? $_POST['arrow_color'] : "#000",
                'width' => !empty($_POST['width']) ? $_POST['width'] : 1100,
                'height' => !empty($_POST['height']) ? $_POST['height'] : 300,
            );
            update_post_meta($id,'rt_slider_options', $settings);
        }
        
        function save_order() {
            global $post;
            if (!check_ajax_referer('reorder_nonce', 'security')) {
                return wp_send_json_error('Invalid Nonce');
            }
            if (!current_user_can('manage_options')) {
                return wp_send_json_error(__('You are not allow to do this !', 'rts'));
            }
            $order = $_POST['order'];
            $id = $_POST['id'];
            $o = explode(',', $order);
            $counter = 0;
            $slider_images = get_post_meta($id,'rt_slider_images',true);
            foreach ($o as $item_id) {
                $slider_images[$item_id] = $counter;
                update_post_meta($id,'rt_slider_images', $slider_images);
                $counter++;
            }
            wp_send_json_success('Order saved');
            die();
        }

        function delete_image() {
            $key = $_POST['key'];
            $id = $_POST['id'];
            $message = array();
            if (!empty($key)) {
                $slider_images = get_post_meta($id,'rt_slider_images',true);
                if (array_key_exists($key, $slider_images)) {
                    unset($slider_images[$key]);
                    update_post_meta($id,'rt_slider_images', $slider_images);
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

        function delete_all_images(){
            $id = $_POST['id'];
            update_post_meta($id,'rt_slider_images',array());
            echo json_encode(array('success'=>__("Images deleted","dp")));
            die;
        }
        
        function rt_slideshow($atts) {
            global $post;
            $html = "";
            if(!$atts){
                $html .= sprintf("<div class='shortcode_warning'><strong>%s</strong></div>", __("No slider found.", "rts"));
                return $html;
            }
            $id = $atts['id'];
            $settings = get_post_meta($id,'rt_slider_options',true);
            $slider_images = get_post_meta($id,'rt_slider_images',true);
            if ($slider_images) {
                asort($slider_images);
                $rtsettings = json_encode($settings);
                $slider_status = !empty($slider_images) ? "active" : "inactive";
                $html .= "<div id='rtsettings' data-settings='$rtsettings'></div>";
                $html .= '<div id="rtstatus" data-status="'.$slider_status.'"></div>';
                $html .= "<div class='rt_slider'>";
                foreach ($slider_images as $key => $value) {
                    if ($settings['items'] == true && $settings['lazy_load'] == "ondemand") {
                        $html .= sprintf("<div class='rt_image'><img data-lazy='%s'/></div>", wp_get_attachment_url($key));
                    } else {
                        $html .= sprintf("<div class='rt_image'><img src='%s'/></div>", wp_get_attachment_url($key));
                    }
                }
            } else {
                if (strpos($post->post_content, "[rt_slideshow id='$id']") !== false) {
                    $html .= sprintf("<div class='shortcode_warning'><strong>%s</strong></div>", __("No images found in the slider.", "rts"));
                }
            }
            $html .= "</div>";
            return $html;
        }
        function enqueue_visual_editor_button_script($plugin_array){
            $plugin_array["select_slider_toolbar_btn"] =  RTS_URL ."assets/js/visual_editor_button.js";
            return $plugin_array;
        }
        function register_slider_button_in_editor($buttons){
            array_push($buttons, "select_slider_btn");
            return $buttons;
        }
        function get_all_sliders(){
            $posts = get_posts(array('post_type' => 'rt_slider'));
            $sliders = array();
            $all_sliders = array();
            if($posts){
                foreach ($posts as $key => $value) {
                    $sliders['text'] = $value->post_title;
                    $sliders['value'] = "[rt_slideshow id='$value->ID']";
                    $all_sliders[] = $sliders;
                }
            }else{
                $sliders['text'] = "No sliders found";
                $sliders['value'] = "";
                $all_sliders[] = $sliders;
            }
            echo json_encode($all_sliders);
            die;
        }
        
    }

    new RTS_Main();
}