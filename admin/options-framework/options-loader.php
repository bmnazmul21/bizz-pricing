<?php
/**
 * BizzPlugin Options Framework - Loader
 * 
 * This file loads the BizzPlugin Options Framework.
 * Include this file in your plugin to use the framework.
 * 
 * @package BizzPlugin_Options_Framework
 * @version 1.0.0
 * 
 * Usage:
 * require_once plugin_dir_path(__FILE__) . 'options-framework/options-loader.php';
 */

if (!defined('ABSPATH')) {
    exit;
}

// Prevent double loading
if ( ! defined('BIZZPLUGIN_FRAMEWORK_VERSION')) {
    define('BIZZPLUGIN_FRAMEWORK_VERSION', '1.0.0');
    define('BIZZPLUGIN_FRAMEWORK_PATH', dirname(__FILE__));
    define('BIZZPLUGIN_FRAMEWORK_URL', plugin_dir_url(__FILE__));

    // Load the main framework class
    require_once BIZZPLUGIN_FRAMEWORK_PATH . '/class-bizzplugin-framework.php';

    /**
     * Initialize the framework
     */
    function bizzplugin_framework_init() {
        return bizzplugin_framework();
    }

    // Initialize on plugins_loaded hook
    add_action('plugins_loaded', 'bizzplugin_framework_init', 5);
}


