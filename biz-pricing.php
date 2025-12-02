<?php

/**
 * Plugin Name: Biz Pricing
 * Description: A pricing table plugin for WordPress to create and manage pricing tables easily.    
 * Version: 1.0.0
 * Author: nazmul hasan
 * License: GPL2
 * Text Domain: biz-pricing
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class biz_pricing{
    public function __construct(){
        add_action('plugins_loaded', array($this, 'plugins_loaded'));
        
    }
    public function plugins_loaded(){
        include_once plugin_dir_path(__FILE__) . 'admin/loaded.php';
        new biz_pricing_loader();
    }
}

new biz_pricing();