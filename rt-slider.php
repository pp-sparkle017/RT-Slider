<?php

/*
 * Plugin Name: RT Slider
 * Plugin URI: #
 * Description: A demo on WordPress shortcode which includes a slider plugin with jQuery slick slider.
 * Version: 1.0.0
 * Author: Shweta Danej
 * Author URI: #
 * Text Domain: rts
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    return;
}

/**
 * Plugin file path
 *
 * @since 1.0.0
 * @var string RTS_FILE
 */
define('RTS_FILE', __FILE__);
/**
 * Plugin directory path
 *
 * @since 1.0.0
 * @var string RTS_DIR
 */
define('RTS_DIR', plugin_dir_path(__FILE__));
/**
 * Plugin directory url
 *
 * @var string RTS_URL
 * @since 1.0.0
 */
define('RTS_URL', plugin_dir_url(__FILE__));
/**
 * Plugin classes directory path
 *
 * @var string RTS_CLASSES
 * @since 1.0.0
 */
define('RTS_CLASSES', RTS_DIR . 'classes/');
/**
 * Plugin template directory
 *
 * @var string RTS_TEMPLATE
 * @since 1.0.0
 */
define('RTS_TEMPLATE', RTS_DIR . 'templates/');
/**
 * Plugin name
 *
 * @var string RTS_NAME
 * @since 1.0.0
 */
define('RTS_NAME', 'RT Slider');

add_action('plugins_loaded', 'rts_init');

if (!function_exists('rts_init')) {

    /**
     * Initialization.
     * 
     * @since 1.0.0
     */
    function rts_init() {
        $locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
        $locale = apply_filters('plugin_locale', $locale, 'rts');
        unload_textdomain('rts');
        load_textdomain('rts', RTS_DIR . 'languages/' . "rts-" . $locale . '.mo');
        load_plugin_textdomain('rts', false, RTS_DIR . 'languages');
        require_once( RTS_CLASSES . 'class.rts_main.php' );
    }
}