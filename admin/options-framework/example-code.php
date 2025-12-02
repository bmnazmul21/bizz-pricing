<?php
/**
 * BizzPlugin Options Framework - Example Code
 * 
 * This file demonstrates how to use the BizzPlugin Options Framework
 * in your own plugin. Copy this file and the options-framework folder
 * to your plugin directory.
 * 
 * NOTE: This file is for reference only. Do NOT include this file directly.
 * Instead, use this as a template for your own plugin implementation.
 * 
 * @package BizzPlugin_Options_Framework
 * @version 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * USAGE INSTRUCTIONS
 * ============================================================================
 * 
 * 1. Copy the 'options-framework' folder to your plugin directory
 * 
 * 2. In your main plugin file, include the framework loader:
 *    require_once plugin_dir_path(__FILE__) . 'options-framework/options-loader.php';
 * 
 * 3. Use the code below as a template for creating your settings panel
 * 
 * ============================================================================
 */


// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BIZZPLUGIN_PLUGIN_VERSION', '1.0.0.123');
define('BIZZPLUGIN_PLUGIN_FILE', __FILE__);
define('BIZZPLUGIN_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BIZZPLUGIN_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load the options framework
require_once BIZZPLUGIN_PLUGIN_PATH . 'options-framework/options-loader.php';
require_once BIZZPLUGIN_PLUGIN_PATH . 'app/functions.php';

/**
 * Main Plugin Class
 */
class BizzPlugin_Option_Framework {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Premium status - Change this to true when premium is active
     */
    private $is_premium = false;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init_settings'), 10);

        
        // Filter for premium status
        add_filter('bizzplugin_is_premium', array($this, 'check_premium_status'), 10, 2);
        // add_filter( 'bizzplugin_panel_config', array($this, 'modify_panel_config'), 10, 2 );
        
        // // Example: Panel-specific filters for individual panels
        // // Use these filters when you have multiple panels and need different configurations
        // add_filter( 'bizzplugin_panel_config_bizzplugin_sample', array($this, 'modify_sample_panel_config'), 10, 2 );
        // add_filter( 'bizzplugin_panel_config_bizzplugin_secondary', array($this, 'modify_secondary_panel_config'), 10, 2 );
        add_action('admin_notices', function() {
            if ( class_exists( 'BizzPlugin_Framework' ) ) {
                echo '<div class="notice notice-error"><p>';
                echo esc_html__( 'BizzPlugin Option Framework requires the BizzPlugin Framework to be installed and activated.', 'bizzplugin-framework' );
                echo '</p></div>';
            }
        });
    }
    
    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('bizzplugin-framework', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    /**
     * Check premium status
     */
    public function check_premium_status($is_premium, $panel_id) {
        // You can add license checking logic here
        return $this->is_premium;
    }
    
    /**
     * Initialize settings panel
     */
    public function init_settings() {

        // dd(get_option('bizzplugin_demo2_options'));
        $framework2 = bizzplugin_framework();
        
        $demo_panel2 = $framework2->create_panel(array(
            'id' => 'bizzplugin_demo2',
            'title' => __('Demo2 Panel (Chainable API)', 'bizzplugin-framework'),
            'menu_title' => __('Demo Panel22', 'bizzplugin-framework'),
            'menu_slug' => 'bizzplugin-demo2',
            'capability' => 'manage_options',
            'icon' => 'dashicons-welcome-widgets-menus',
            'position' => 51,
            'option_name' => 'bizzplugin_demo2_options',
            'is_premium' => false,
            'sections' => $this->get_sections(),
            // 'route_namespace' => 'saiful/2323',
        ));
        $demo_panel2->add_route_namespace('saiful/v2');

        $demo_panel2->add_recommended_plugin(array(
                'slug' => 'woo-product-table',
                'name' => '%%%%%%%%%Woo Product TableSingle',
                'description' => __('Display WooCommerce products in a table layout.', 'bizzplugin-framework'),
                'thumbnail' => 'https://ps.w.org/woo-product-table/assets/icon-256x256.gif',
                'author' => 'Bizzplugin',
                'file' => 'woo-product-table/woo-product-table.php',
                'url' => 'https://wordpress.org/plugins/woo-product-table/',
            ));
        $demo_panel2->add_recommended_plugins(array(
            array(
                'slug' => 'woo-product-table',
                'name' => 'Woo Product TableSingledddd',
                'description' => __('Display WooCommerce products in a table layout.', 'bizzplugin-framework'),
                'thumbnail' => 'https://ps.w.org/woo-product-table/assets/icon-256x256.gif',
                'author' => 'Bizzplugin',
                'file' => 'woo-product-table/woo-product-table.php',
                'url' => 'https://wordpress.org/plugins/woo-product-table/',
            ),
            array(
                'slug' => 'woo-product-table',
                'name' => 'Woo Product TableSingleffffffffff',
                'description' => __('Display WooCommerce products in a table layout.', 'bizzplugin-framework'),
                'thumbnail' => 'https://ps.w.org/woo-product-table/assets/icon-256x256.gif',
                'author' => 'Bizzplugin',
                'file' => 'woo-product-table/woo-product-table.php',
                'url' => 'https://wordpress.org/plugins/woo-product-table/',
            ),
        ));

        // dd($demo_panel2);
        // $demo_panel3 = $framework2->create_panel(array(
        //     'id' => 'bizzplugin_demo3',
        //     'title' => __('Demo3 Panel (Chainable API)', 'bizzplugin-framework'),
        //     'menu_title' => __('Demo3 Panel23', 'bizzplugin-framework'),
        //     'menu_slug' => 'bizzplugin-demo3',
        //     'capability' => 'manage_options',
        //     'icon' => 'dashicons-welcome-widgets-menus',
        //     'position' => 51,
        //     'option_name' => 'bizzplugin_demo3_options',
        //     'is_premium' => true,
        //     'sections' => $this->get_sections(),
        // ));
        // $demo_panel244 = $framework2->create_panel(array(
        //     'id' => 'bizzplugin_demo2444',
        //     'title' => __('Demo2 Panel (Chainable API)', 'bizzplugin-framework'),
        //     'menu_title' => __('Demo Panel2244', 'bizzplugin-framework'),
        //     'menu_slug' => 'bizzplugin-demo2444',
        //     'capability' => 'manage_options',
        //     'icon' => 'dashicons-welcome-widgets-menus',
        //     'position' => 51,
        //     'option_name' => 'bizzplugin_demo244_options',
        //     'is_premium' => false,
        //     'sections' => $this->get_sections(),
        // ));
        $demo_panel2->set_panel_config( $this->panel_config() );
        // Get framework instance
        $framework = bizzplugin_framework();
        
        // ================================================================
        // EXAMPLE 1: Traditional approach with sections array
        // This approach is still fully supported for backward compatibility
        // ================================================================
        // $framework->create_panel(array(
        //     'id' => 'bizzplugin_sample',
        //     'title' => __('BizzPlugin Settings', 'bizzplugin-framework'),
        //     'menu_title' => __('BizzPlugin', 'bizzplugin-framework'),
        //     'menu_slug' => 'bizzplugin-settings',
        //     'capability' => 'manage_options',
        //     'icon' => 'dashicons-admin-settings',
        //     'position' => 50,
        //     'option_name' => 'bizzplugin_options',
        //     'is_premium' => $this->is_premium,
        //     'sections' => $this->get_sections(),
        // ));

        // ================================================================
        // EXAMPLE 2: New Chainable API approach (Recommended)
        // Create panel first, then configure using chainable methods
        // Each panel instance is independent - no conflicts with other plugins
        // ================================================================
        $demo_panel = $framework->create_panel(array(
            'id' => 'bizzplugin_demo',
            'title' => __('Demo Panel (Chainable API)', 'bizzplugin-framework'),
            'menu_title' => __('Demo Panel', 'bizzplugin-framework'),
            'menu_slug' => 'bizzplugin-demo',
            'capability' => 'manage_options',
            'icon' => 'dashicons-welcome-widgets-menus',
            'position' => 51,
            'option_name' => 'bizzplugin_demo_options',
            'is_premium' => false,
            // 'sections' => $this->get_sections(),
        ));
        
        // Configure panel using chainable methods
        // $demo_panel
        //     ->set_logo(BIZZPLUGIN_PLUGIN_URL . 'assets/imgs/min-max-logo.png')
        //     ->set_version(BIZZPLUGIN_PLUGIN_VERSION)
        //     ->set_panel_title(__('Demo Panel - Chainable API', 'bizzplugin-framework'))
        //     ->set_premium(false)
        //     ->set_footer_text(__('Built with BizzPlugin Framework', 'bizzplugin-framework'));
        
        $demo_panel->set_panel_config( $this->panel_config() );

        // Add sections using chainable method
        $demo_panel->add_section(array(
            'id' => 'demo_general',
            'title' => __('General Settings', 'bizzplugin-framework'),
            'description' => __('Configure general settings for your plugin.', 'bizzplugin-framework'),
            'icon' => 'dashicons dashicons-admin-generic',
            'fields' => array(
                array(
                    'id' => 'demo_site_name',
                    'type' => 'text',
                    'title' => __('Site Name', 'bizzplugin-framework'),
                    'description' => __('Enter your site name.', 'bizzplugin-framework'),
                    'default' => get_bloginfo('name'),
                    'placeholder' => __('Enter site name...', 'bizzplugin-framework'),
                ),
                array(
                    'id' => 'demo_enable_feature',
                    'type' => 'switch',
                    'title' => __('Enable Feature', 'bizzplugin-framework'),
                    'description' => __('Toggle this feature on or off.', 'bizzplugin-framework'),
                    'default' => '1',
                ),
            ),
        ));
        
        // Add another field to existing section using add_field()
        $demo_panel->add_field('demo_general', array(
            'id' => 'demo_color',
            'type' => 'color',
            'title' => __('Theme Color', 'bizzplugin-framework'),
            'description' => __('Select your theme color.', 'bizzplugin-framework'),
            'default' => '#2271b1',
        ));
        
        // Add a subsection to existing section
        $demo_panel->add_subsection('demo_general', array(
            'id' => 'demo_advanced',
            'title' => __('Advanced Options', 'bizzplugin-framework'),
            'description' => __('Advanced configuration options.', 'bizzplugin-framework'),
            'fields' => array(
                array(
                    'id' => 'demo_debug_mode',
                    'type' => 'checkbox',
                    'title' => __('Debug Mode', 'bizzplugin-framework'),
                    'description' => __('Enable debug mode for troubleshooting.', 'bizzplugin-framework'),
                    'default' => '0',
                    'label' => __('Enable debug mode', 'bizzplugin-framework'),
                ),
            ),
        ));
        
        // Add field to the subsection
        $demo_panel->add_subsection_field('demo_general', 'demo_advanced', array(
            'id' => 'demo_log_level',
            'type' => 'select',
            'title' => __('Log Level', 'bizzplugin-framework'),
            'description' => __('Select the logging level.', 'bizzplugin-framework'),
            'default' => 'error',
            'options' => array(
                'error' => __('Errors Only', 'bizzplugin-framework'),
                'warning' => __('Warnings & Errors', 'bizzplugin-framework'),
                'info' => __('All Messages', 'bizzplugin-framework'),
            ),
            'dependency' => array(
                'field' => 'demo_debug_mode',
                'value' => '1',
            ),
        ));
        
        // Add another section
        $demo_panel->add_section(array(
            'id' => 'demo_appearance',
            'title' => __('Appearance', 'bizzplugin-framework'),
            'description' => __('Customize the appearance of your site.', 'bizzplugin-framework'),
            'icon' => 'dashicons dashicons-admin-appearance',
            'fields' => array(
                array(
                    'id' => 'demo_layout',
                    'type' => 'image_select',
                    'title' => __('Layout Style', 'bizzplugin-framework'),
                    'description' => __('Select your preferred layout.', 'bizzplugin-framework'),
                    'default' => 'sidebar-right',
                    'options' => array(
                        'sidebar-left' => BIZZPLUGIN_PLUGIN_URL . 'options-framework/assets/images/sidebar-left.svg',
                        'no-sidebar' => BIZZPLUGIN_PLUGIN_URL . 'options-framework/assets/images/no-sidebar.svg',
                        'sidebar-right' => BIZZPLUGIN_PLUGIN_URL . 'options-framework/assets/images/sidebar-right.svg',
                    ),
                ),
                array(
                    'id' => 'demo_slider',
                    'type' => 'slider',
                    'title' => __('Content Width', 'bizzplugin-framework'),
                    'description' => __('Set the content width.', 'bizzplugin-framework'),
                    'default' => 1200,
                    'min' => 800,
                    'max' => 1600,
                    'step' => 10,
                    'unit' => 'px',
                ),
            ),
        ));
        
        // Set resource links for sidebar
        // $demo_panel->add_resources(array(
        //     array(
        //         'icon' => 'dashicons dashicons-book',
        //         'title' => __('Documentation', 'bizzplugin-framework'),
        //         'url' => 'https://github.com/codersaiful/bizzplugin-option-framework',
        //     ),
        //     array(
        //         'icon' => 'dashicons dashicons-sos',
        //         'title' => __('Get Support', 'bizzplugin-framework'),
        //         'url' => 'https://github.com/codersaiful/bizzplugin-option-framework/issues',
        //     ),
        //     array(
        //         'icon' => 'dashicons dashicons-star-filled',
        //         'title' => __('Rate Plugin', 'bizzplugin-framework'),
        //         'url' => 'https://wordpress.org/support/plugin/bizzplugin-option-framework/reviews/',
        //     ),
        // ));
        
        // Add individual resource
        $demo_panel->add_resource(array(
            'icon' => 'dashicons dashicons-facebook',
            'title' => __('Facebook Community', 'bizzplugin-framework'),
            'url' => 'https://facebook.com/groups/bizzplugin',
        ));
        
        // Set recommended plugins
        $demo_panel->add_recommended_plugins(array(
            array(
                'slug' => 'woo-product-table',
                'name' => 'Woo Product TableSingle',
                'description' => __('Display WooCommerce products in a table layout.', 'bizzplugin-framework'),
                'thumbnail' => 'https://ps.w.org/woo-product-table/assets/icon-256x256.gif',
                'author' => 'Bizzplugin',
                'file' => 'woo-product-table/woo-product-table.php',
                'url' => 'https://wordpress.org/plugins/woo-product-table/',
            ),
        ));
        
        // // Add individual recommended plugin
        $demo_panel->add_recommended_plugin(array(
            'slug' => 'elementor',
            'name' => 'Elementor Page Builder',
            'description' => __('The most advanced page builder.', 'bizzplugin-framework'),
            'thumbnail' => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
            'author' => 'Elementor.com',
            'file' => 'elementor/elementor.php',
            'url' => 'https://wordpress.org/plugins/elementor/',
        ));
        
        // ================================================================
        // EXAMPLE 3: Secondary panel using traditional approach
        // This shows that both approaches work together
        // ================================================================
        // $framework->create_panel(array(
        //     'id' => 'bizzplugin_secondary',
        //     'title' => __('BizzPlugin Secondary', 'bizzplugin-framework'),
        //     'menu_title' => __('BizzPlugin Secondary', 'bizzplugin-framework'),
        //     'menu_slug' => 'bizzplugin-settings_secondary',
        //     'capability' => 'manage_options',
        //     'icon' => 'dashicons-admin-settings',
        //     'position' => 52,
        //     'option_name' => 'options_secondary',
        //     'is_premium' => $this->is_premium,
        //     'sections' => $this->get_sections(),
        // ));
    }
    
    /**
     * Get all sections and fields
     */
    private function get_sections() {
        return array(
            //Getting Started Section
            array(
                'id' => 'getting_started',
                'title' => __('Getting Started', 'bizzplugin-framework'),
                'description' => __('Welcome to the BizzPlugin Option Framework! Use this panel to configure your plugin settings easily.', 'bizzplugin-framework'),
                'icon' => 'dashicons dashicons-welcome-learn-more',
                // 'hide_reset_button' => true,
                'fields' => array(
                    array(
                        'id' => 'welcome_message',
                        'type' => 'info',
                        'title' => __('Welcome to BizzPlugin!', 'bizzplugin-framework'),
                        'description' => __('Thank you for choosing BizzPlugin Option Framework. This framework provides a simple and efficient way to manage your plugin settings with a modern interface, AJAX saving, REST API support, and more.', 'bizzplugin-framework'),
                    ),
                    array(
                        'id' => 'documentation_link',
                        'type' => 'link',
                        'title' => __('Documentation', 'bizzplugin-framework'),
                        'description' => __('For detailed documentation and guides, please visit our <a href="https://example.com/docs" target="_blank" rel="noopener noreferrer">Documentation</a>.', 'bizzplugin-framework'),
                    ),
                    //sample html field
                    array(
                        'id' => 'html_field',
                        'type' => 'html',
                        'title' => __('HTML Field', 'bizzplugin-framework'),
                        'description' => __('This is a sample HTML field. You can add any custom HTML content here.', 'bizzplugin-framework'),
                        'default' => '<div style="padding:10px; background-color:#f1f1f1; border:1px solid #ccc;">This is a custom HTML content area. You can include images, links, or any other HTML elements here to enhance your settings panel.</div>',
                    ),
                    // Slider example
                    array(
                        'id' => 'example_slider',
                        'type' => 'slider',
                        'title' => __('Slider Example', 'bizzplugin-framework'),
                        'description' => __('This is a slider field example. Drag the slider to select a value. The selected value is displayed in real-time.', 'bizzplugin-framework'),
                        'default' => 50,
                        'min' => 0,
                        'max' => 1000,
                        'step' => 5,
                        'unit' => 'px',
                    ),
                    //option_select field
                    array(
                        'id' => 'example_option_select',
                        'type' => 'option_select',
                        'title' => __('Option Select Example', 'bizzplugin-framework'),
                        'description' => __('This is an option select field example. Choose an option from the dropdown to see how it works.', 'bizzplugin-framework'),
                        'default' => 'option_2',
                        'options' => array(
                            'option_1' => __('Option 1', 'bizzplugin-framework'),
                            'option_2' => __('Option 2', 'bizzplugin-framework'),
                            'option_3' => __('Option 3', 'bizzplugin-framework'),
                        ),
                    ),
                ),
            ),
            // Basic Settings Section
            array(
                'id' => 'basic',
                'title' => __('Basic Settings', 'bizzplugin-framework'),
                'description' => __('Configure the basic settings for your plugin.', 'bizzplugin-framework'),
                'icon' => 'dashicons dashicons-admin-settings',
                'fields' => array(
                    array(
                        'id' => 'text_field',
                        'type' => 'text',
                        'title' => __('Text Field', 'bizzplugin-framework'),
                        'description' => __('A simple text input field.', 'bizzplugin-framework'),
                        'default' => 'abc',
                        'placeholder' => __('Enter text...', 'bizzplugin-framework'),
                        'premium' => true,
                        
                    ),
                    array(
                        'id' => 'number_field',
                        'type' => 'number',
                        'title' => __('Number Field', 'bizzplugin-framework'),
                        'description' => __('A simple number input field.', 'bizzplugin-framework'),
                        'default' => 10,
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ),
                    //image upload field
                    array(
                        'id' => 'image_field',
                        'type' => 'image',
                        'title' => __('Image Field', 'bizzplugin-framework'),
                        'description' => __('A simple image upload field.', 'bizzplugin-framework'),
                        'default' => '',
                    ),

                    array(
                        'id' => 'textarea_field',
                        'type' => 'textarea',
                        'title' => __('Textarea Field', 'bizzplugin-framework'),
                        'description' => __('A simple textarea input field.', 'bizzplugin-framework'),
                        'default' => 'This is Texxt area field. You can write multiple lines of text here.',
                        'rows' => 5,
                        'placeholder' => __('Enter text...', 'bizzplugin-framework'),
                    ),
                    array(
                        'id' => 'checkbox_field',
                        'type' => 'checkbox',
                        'title' => __('Checkbox Field', 'bizzplugin-framework'),
                        'description' => __('A simple checkbox field.', 'bizzplugin-framework'),
                        'default' => '1',
                        'label' => __('Check me!', 'bizzplugin-framework'),
                    ),
                    array(
                        'id' => 'select_field',
                        'type' => 'select',
                        'title' => __('Select Field', 'bizzplugin-framework'),
                        'description' => __('A simple select dropdown field.', 'bizzplugin-framework'),
                        'default' => 'option_2',
                        'options' => array(
                            'option_1' => __('Option 1', 'bizzplugin-framework'),
                            'option_2' => __('Option 2', 'bizzplugin-framework'),
                            'option_3' => __('Option 3', 'bizzplugin-framework'),
                        ),
                    ),
                    array(
                        'id' => 'radio_field',
                        'type' => 'radio',
                        'title' => __('Radio Field', 'bizzplugin-framework'),
                        'description' => __('A simple radio button field.', 'bizzplugin-framework'),
                        'default' => 'yes',
                        'options' => array(
                            'yes' => __('Yes', 'bizzplugin-framework'),
                            'no' => __('No', 'bizzplugin-framework'),
                        ),
                    ),
                    // Slider field
                    array(
                        'id' => 'slider_field',
                        'type' => 'slider',
                        'title' => __('Slider Field', 'bizzplugin-framework'),
                        'description' => __('A slider field to select a value within a range.', 'bizzplugin-framework'),
                        'default' => 25,
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                        'unit' => 'px',
                        'dependency' => array(
                            'field' => 'radio_field',
                            'value' => 'yes',
                        ),
                    ),
                ),
                'subsections' => array(
                    array(
                        'id' => 'sub_basic_1',
                        'title' => __('Sub Basic 1', 'bizzplugin-framework'),
                        'description' => __('This is the first subsection under Basic Settings.', 'bizzplugin-framework'),
                        'fields' => array(
                            array(
                                'id' => 'sub_text_field',
                                'type' => 'text',
                                'title' => __('Sub Text Field', 'bizzplugin-framework'),
                                'description' => __('A text field in the subsection.', 'bizzplugin-framework'),
                                'default' => 'Subsection Text',
                                'placeholder' => __('Enter text...', 'bizzplugin-framework'),
                            ),
                            array(
                                'id' => 'sub_number_field',
                                'type' => 'number',
                                'title' => __('Sub Number Field', 'bizzplugin-framework'),
                                'description' => __('A number field in the subsection.', 'bizzplugin-framework'),
                                'default' => 5,
                                'min' => 1,
                                'max' => 10,
                                'step' => 1,
                            ),
                            array(
                                'id' => 'sub_checkbox_field',
                                'type' => 'checkbox',
                                'title' => __('Sub Checkbox Field', 'bizzplugin-framework'),
                                'description' => __('A checkbox field in the subsection.', 'bizzplugin-framework'),
                                'default' => '1',
                                'label' => __('Check me!', 'bizzplugin-framework'),
                            ),
                        ),
                    ),
                    array(
                        'id' => 'sub_basic_2',
                        'title' => __('Sub Basic 2', 'bizzplugin-framework'),
                        'description' => __('This is the second subsection under Basic Settings.', 'bizzplugin-framework'),
                        'fields' => array(
                            array(
                                'id' => 'sub_textarea_field',
                                'type' => 'textarea',
                                'title' => __('Sub Textarea Field', 'bizzplugin-framework'),
                                'description' => __('A textarea field in the subsection.', 'bizzplugin-framework'),
                                'default' => 'This is a textarea in subsection.',
                                'rows' => 4,
                                'placeholder' => __('Enter text...', 'bizzplugin-framework'),
                            ),
                            array(
                                'id' => 'sub_select_field',
                                'type' => 'select',
                                'title' => __('Sub Select Field', 'bizzplugin-framework'),
                                'description' => __('A select dropdown in the subsection.', 'bizzplugin-framework'),
                                'default' => 'option_1',
                                'options' => array(
                                    'option_1' => __('Option 1', 'bizzplugin-framework'),
                                    'option_2' => __('Option 2', 'bizzplugin-framework'),
                                    'option_3' => __('Option 3', 'bizzplugin-framework'),
                                ),
                            ),
                            array(
                                'id' => 'sub_radio_field',
                                'type' => 'radio',
                                'title' => __('Sub Radio Field', 'bizzplugin-framework'),
                                'description' => __('A radio button field in the subsection.', 'bizzplugin-framework'),
                                'default' => 'yes',
                                'options' => array(
                                    'yes' => __('Yes', 'bizzplugin-framework'),
                                    'no' => __('No', 'bizzplugin-framework'),
                                ),
                            ),
                        ),
                    ),
                    array(
                        'id' => 'sub_basic_3',
                        'title' => __('Sub Basic 3', 'bizzplugin-framework'),
                        'description' => __('This is the third subsection under Basic Settings.', 'bizzplugin-framework'),
                        'fields' => array(
                            array(
                                'id' => 'sub_color_field',
                                'type' => 'color',
                                'title' => __('Sub Color Field', 'bizzplugin-framework'),
                                'description' => __('A color picker field in the subsection.', 'bizzplugin-framework'),
                                'default' => '#ff0000',
                            ),
                            array(
                                'id' => 'sub_date_field',
                                'type' => 'date',
                                'title' => __('Sub Date Field', 'bizzplugin-framework'),
                                'description' => __('A date picker field in the subsection.', 'bizzplugin-framework'),
                                'default' => '',
                                'placeholder' => __('Select date...', 'bizzplugin-framework'),
                            ),
                            array(
                                'id' => 'sub_file_field',
                                'type' => 'file',
                                'title' => __('Sub File Field', 'bizzplugin-framework'),
                                'description' => __('A file upload field in the subsection.', 'bizzplugin-framework'),
                                'default' => '',
                            ),
                            //image field
                            array(
                                'id' => 'sub_image_field',
                                'type' => 'image',
                                'title' => __('Sub Image Field', 'bizzplugin-framework'),
                                'description' => __('An image upload field in the subsection.', 'bizzplugin-framework'),
                                'default' => '',
                            ),
                            //color field
                            array(
                                'id' => 'sub_color_field_2',
                                'type' => 'color',
                                'title' => __('Sub Color Field 2', 'bizzplugin-framework'),
                                'description' => __('Another color picker field in the subsection.', 'bizzplugin-framework'),
                                'default' => '#00ff00',
                            ),
                        ),
                    ),
                    array(
                        'id' => 'sub_basic_4',
                        'title' => __('Sub Basic 4', 'bizzplugin-framework'),
                        'description' => __('This is the fourth subsection under Basic Settings.', 'bizzplugin-framework'),
                        'fields' => array(
                            array(
                                'id' => 'sub_password_field',
                                'type' => 'password',
                                'title' => __('Sub Password Field', 'bizzplugin-framework'),
                                'description' => __('A password input field in the subsection.', 'bizzplugin-framework'),
                                'default' => '',
                                'placeholder' => __('Enter password...', 'bizzplugin-framework'),
                            ),
                            array(
                                'id' => 'sub_number_field_2',
                                'type' => 'number',
                                'title' => __('Sub Number Field 2', 'bizzplugin-framework'),
                                'description' => __('Another number field in the subsection.', 'bizzplugin-framework'),
                                'default' => 10,
                                'min' => 5,
                                'max' => 20,
                                'step' => 1,
                            ),
                            //dropdown
                            array(
                                'id' => 'sub_dropdown_field',
                                'type' => 'select',
                                'title' => __('Sub Dropdown Field', 'bizzplugin-framework'),
                                'description' => __('A dropdown select field in the subsection.', 'bizzplugin-framework'),
                                'default' => 'option_3',
                                'options' => array(
                                    'option_1' => __('Option 1', 'bizzplugin-framework'),
                                    'option_2' => __('Option 2', 'bizzplugin-framework'),
                                    'option_3' => __('Option 3', 'bizzplugin-framework'),
                                ),
                            ),
                        ),
                    ),
                ),
            ),

            // General Settings Section
            array(
                'id' => 'general',
                'title' => __('General Settings', 'bizzplugin-framework'),
                'description' => __('Configure the basic settings for your plugin.', 'bizzplugin-framework'),
                'icon' => 'dashicons dashicons-admin-generic',
                'fields' => array(
                    array(
                        'id' => 'site_title',
                        'type' => 'text',
                        'title' => __('Site Title', 'bizzplugin-framework'),
                        'description' => __('Enter your site title here.', 'bizzplugin-framework'),
                        'default' => get_bloginfo('name'),
                        'placeholder' => __('Enter site title...', 'bizzplugin-framework'),
                    ),
                    //on off switch
                    array(
                        'id' => 'ss_site_on_off',
                        'type' => 'switch', // Using the new 'switch' alias (also supports 'on_off')
                        'title' => __('Site On/Off', 'bizzplugin-framework'),
                        'description' => __('Toggle the site on or off.', 'bizzplugin-framework'),
                        'default' => 'on',
                    ),
                    array(
                        'id' => 'site_description',
                        'type' => 'textarea',
                        'title' => __('Site Description', 'bizzplugin-framework'),
                        'description' => __('Enter a brief description of your site.', 'bizzplugin-framework'),
                        'default' => get_bloginfo('description'),
                        'rows' => 4,
                        'placeholder' => __('Enter site description...', 'bizzplugin-framework'),
                    ),
                    array(
                        'id' => 'admin_email',
                        'type' => 'email',
                        'title' => __('Admin Email', 'bizzplugin-framework'),
                        'description' => __('The admin email address for notifications.', 'bizzplugin-framework'),
                        'default' => get_option('admin_email'),
                        'placeholder' => 'admin@example.com',
                    ),
                    array(
                        'id' => 'site_url',
                        'type' => 'url',
                        'title' => __('Site URL', 'bizzplugin-framework'),
                        'description' => __('Your website URL.', 'bizzplugin-framework'),
                        'default' => home_url(),
                        'placeholder' => 'https://example.com',
                    ),
                    array(
                        'id' => 'posts_per_page',
                        'type' => 'number',
                        'title' => __('Posts Per Page', 'bizzplugin-framework'),
                        'description' => __('Number of posts to display per page.', 'bizzplugin-framework'),
                        'default' => 10,
                        'min' => 1,
                        'max' => 100,
                        'step' => 1,
                    ),
                ),
            ),
            
            // Appearance Section
            array(
                'id' => 'appearance',
                'title' => __('Appearance', 'bizzplugin-framework'),
                'description' => __('Customize the visual appearance of your site.', 'bizzplugin-framework'),
                'icon' => 'dashicons dashicons-admin-appearance',
                'fields' => array(
                    array(
                        'id' => 'primary_color',
                        'type' => 'color',
                        'title' => __('Primary Color', 'bizzplugin-framework'),
                        'description' => __('Select the primary color for your site.', 'bizzplugin-framework'),
                        'default' => '#2271b1',
                    ),
                    array(
                        'id' => 'secondary_color',
                        'type' => 'color',
                        'title' => __('Secondary Color', 'bizzplugin-framework'),
                        'description' => __('Select the secondary color for your site.', 'bizzplugin-framework'),
                        'default' => '#135e96',
                    ),
                    array(
                        'id' => 'layout_style',
                        'type' => 'select',
                        'title' => __('Layout Style', 'bizzplugin-framework'),
                        'description' => __('Choose the layout style for your site.', 'bizzplugin-framework'),
                        'default' => 'full-width',
                        'options' => array(
                            'full-width' => __('Full Width', 'bizzplugin-framework'),
                            'boxed' => __('Boxed', 'bizzplugin-framework'),
                            'framed' => __('Framed', 'bizzplugin-framework'),
                        ),
                    ),
                    array(
                        'id' => 'layout_image',
                        'type' => 'image_select',
                        'title' => __('Layout Template', 'bizzplugin-framework'),
                        'description' => __('Select a layout template visually.', 'bizzplugin-framework'),
                        'default' => 'sidebar-right',
                        'options' => array(
                            'sidebar-left' => BIZZPLUGIN_PLUGIN_URL . 'options-framework/assets/images/sidebar-left.svg',
                            'no-sidebar' => BIZZPLUGIN_PLUGIN_URL . 'options-framework/assets/images/no-sidebar.svg',
                            'sidebar-right' => BIZZPLUGIN_PLUGIN_URL . 'options-framework/assets/images/sidebar-right.svg',
                        ),
                    ),
                    array(
                        'id' => 'logo_image',
                        'type' => 'image',
                        'title' => __('Logo Image', 'bizzplugin-framework'),
                        'description' => __('Upload your site logo.', 'bizzplugin-framework'),
                        'default' => '',
                    ),
                ),
                'subsections' => array(
                    array(
                        'id' => 'typography',
                        'title' => __('Typography Settings', 'bizzplugin-framework'),
                        'description' => __('Configure font settings.', 'bizzplugin-framework'),
                        'fields' => array(
                            array(
                                'id' => 'body_font',
                                'type' => 'select',
                                'title' => __('Body Font', 'bizzplugin-framework'),
                                'description' => __('Select the font for body text.', 'bizzplugin-framework'),
                                'default' => 'system',
                                'options' => array(
                                    'system' => __('System Default', 'bizzplugin-framework'),
                                    'roboto' => 'Roboto',
                                    'open-sans' => 'Open Sans',
                                    'lato' => 'Lato',
                                    'montserrat' => 'Montserrat',
                                ),
                            ),
                            array(
                                'id' => 'font_size',
                                'type' => 'number',
                                'title' => __('Base Font Size (px)', 'bizzplugin-framework'),
                                'description' => __('Set the base font size in pixels.', 'bizzplugin-framework'),
                                'default' => 16,
                                'min' => 12,
                                'max' => 24,
                            ),
                        ),
                    ),
                ),
            ),
            
            // Features Section
            array(
                'id' => 'features',
                'title' => __('Features', 'bizzplugin-framework'),
                'description' => __('Enable or disable various features.', 'bizzplugin-framework'),
                'icon' => 'dashicons dashicons-plugins-checked',
                'fields' => array(
                    array(
                        'id' => 'enable_feature_1',
                        'type' => 'on_off',
                        'title' => __('Enable Feature One', 'bizzplugin-framework'),
                        'description' => __('Turn on/off the first feature.', 'bizzplugin-framework'),
                        'default' => '1',
                        'on_label' => __('Enabled', 'bizzplugin-framework'),
                        'off_label' => __('Disabled', 'bizzplugin-framework'),
                    ),
                    array(
                        'id' => 'feature_1_mode',
                        'type' => 'radio',
                        'title' => __('Feature One Mode', 'bizzplugin-framework'),
                        'description' => __('This field appears when Feature One is enabled.', 'bizzplugin-framework'),
                        'default' => 'standard',
                        'options' => array(
                            'standard' => __('Standard Mode', 'bizzplugin-framework'),
                            'advanced' => __('Advanced Mode', 'bizzplugin-framework'),
                            'expert' => __('Expert Mode', 'bizzplugin-framework'),
                        ),
                        'dependency' => array(
                            'field' => 'enable_feature_1',
                            'value' => '1',
                        ),
                    ),
                    array(
                        'id' => 'enable_feature_2',
                        'type' => 'checkbox',
                        'title' => __('Enable Feature Two', 'bizzplugin-framework'),
                        'description' => __('Check to enable the second feature.', 'bizzplugin-framework'),
                        'default' => '0',
                        'label' => __('Yes, enable this feature', 'bizzplugin-framework'),
                    ),
                    array(
                        'id' => 'enabled_modules',
                        'type' => 'checkbox_group',
                        'title' => __('Enabled Modules', 'bizzplugin-framework'),
                        'description' => __('Select which modules to enable.', 'bizzplugin-framework'),
                        'default' => array('module_1', 'module_2'),
                        'options' => array(
                            'module_1' => __('Module One', 'bizzplugin-framework'),
                            'module_2' => __('Module Two', 'bizzplugin-framework'),
                            'module_3' => __('Module Three', 'bizzplugin-framework'),
                            'module_4' => __('Module Four', 'bizzplugin-framework'),
                        ),
                    ),
                    array(
                        'id' => 'selected_categories',
                        'type' => 'multi_select',
                        'title' => __('Select Categories', 'bizzplugin-framework'),
                        'description' => __('Select multiple categories.', 'bizzplugin-framework'),
                        'default' => array(),
                        'options' => $this->get_category_options(),
                    ),
                ),
            ),
            
            // Content Section
            array(
                'id' => 'content',
                'title' => __('Content', 'bizzplugin-framework'),
                'description' => __('Content related settings.', 'bizzplugin-framework'),
                'icon' => 'dashicons dashicons-admin-post',
                'fields' => array(
                    array(
                        'id' => 'featured_post',
                        'type' => 'post_select',
                        'title' => __('Featured Post', 'bizzplugin-framework'),
                        'description' => __('Select a post to feature.', 'bizzplugin-framework'),
                        'post_type' => 'post',
                        'default' => '',
                    ),
                    array(
                        'id' => 'featured_pages',
                        'type' => 'post_select',
                        'title' => __('Featured Pages', 'bizzplugin-framework'),
                        'description' => __('Select multiple pages to feature.', 'bizzplugin-framework'),
                        'post_type' => 'page',
                        'multiple' => true,
                        'default' => array(),
                    ),
                    array(
                        'id' => 'publish_date',
                        'type' => 'date',
                        'title' => __('Publish Date', 'bizzplugin-framework'),
                        'description' => __('Select a date.', 'bizzplugin-framework'),
                        'default' => '',
                        'placeholder' => __('Select date...', 'bizzplugin-framework'),
                    ),
                    array(
                        'id' => 'attachment_file',
                        'type' => 'file',
                        'title' => __('Attachment File', 'bizzplugin-framework'),
                        'description' => __('Upload a file attachment.', 'bizzplugin-framework'),
                        'default' => '',
                    ),
                ),
            ),
            
            // Advanced Section
            array(
                'id' => 'advanced',
                'title' => __('Advanced', 'bizzplugin-framework'),
                'description' => __('Advanced settings for developers.', 'bizzplugin-framework'),
                'icon' => 'dashicons dashicons-admin-tools',
                'fields' => array(
                    array(
                        'id' => 'custom_css',
                        'type' => 'textarea',
                        'title' => __('Custom CSS', 'bizzplugin-framework'),
                        'description' => __('Add your custom CSS code here.', 'bizzplugin-framework'),
                        'default' => '',
                        'rows' => 10,
                        'placeholder' => '/* Your custom CSS */',
                    ),
                    array(
                        'id' => 'api_key',
                        'type' => 'password',
                        'title' => __('API Key', 'bizzplugin-framework'),
                        'description' => __('Enter your API key here.', 'bizzplugin-framework'),
                        'default' => '',
                        'placeholder' => __('Enter API key...', 'bizzplugin-framework'),
                    ),
                    array(
                        'id' => 'debug_mode',
                        'type' => 'on_off',
                        'title' => __('Debug Mode', 'bizzplugin-framework'),
                        'description' => __('Enable debug mode for troubleshooting.', 'bizzplugin-framework'),
                        'default' => '0',
                    ),
                    array(
                        'id' => 'debug_log_level',
                        'type' => 'select',
                        'title' => __('Debug Log Level', 'bizzplugin-framework'),
                        'description' => __('Select the logging level when debug mode is enabled.', 'bizzplugin-framework'),
                        'default' => 'error',
                        'options' => array(
                            'error' => __('Errors Only', 'bizzplugin-framework'),
                            'warning' => __('Warnings & Errors', 'bizzplugin-framework'),
                            'info' => __('Info, Warnings & Errors', 'bizzplugin-framework'),
                            'debug' => __('All (Debug)', 'bizzplugin-framework'),
                        ),
                        'dependency' => array(
                            'field' => 'debug_mode',
                            'value' => '1',
                        ),
                    ),
                ),
            ),
            
            // Premium Section
            array(
                'id' => 'premium',
                'title' => __('Premium Features', 'bizzplugin-framework'),
                'description' => __('Exclusive features available in the premium version.', 'bizzplugin-framework'),
                'icon' => 'dashicons dashicons-star-filled',
                'fields' => array(
                    array(
                        'id' => 'premium_feature_1',
                        'type' => 'on_off',
                        'title' => __('Advanced Analytics', 'bizzplugin-framework'),
                        'description' => __('Enable advanced analytics and reporting.', 'bizzplugin-framework'),
                        'default' => '0',
                        'premium' => true,
                    ),
                    array(
                        'id' => 'premium_feature_2',
                        'type' => 'select',
                        'title' => __('Premium Layout', 'bizzplugin-framework'),
                        'description' => __('Select a premium layout template.', 'bizzplugin-framework'),
                        'default' => 'premium_1',
                        'options' => array(
                            'premium_1' => __('Premium Layout 1', 'bizzplugin-framework'),
                            'premium_2' => __('Premium Layout 2', 'bizzplugin-framework'),
                            'premium_3' => __('Premium Layout 3', 'bizzplugin-framework'),
                        ),
                        'premium' => true,
                    ),
                    array(
                        'id' => 'premium_text',
                        'type' => 'text',
                        'title' => __('Premium Custom Text', 'bizzplugin-framework'),
                        'description' => __('This field is only available in premium version.', 'bizzplugin-framework'),
                        'default' => '',
                        'placeholder' => __('Enter premium text...', 'bizzplugin-framework'),
                        'premium' => true,
                    ),
                    array(
                        'id' => 'upgrade_notice',
                        'type' => 'html',
                        'title' => __('Upgrade Notice', 'bizzplugin-framework'),
                        'content' => '<div class="bizzplugin-notice bizzplugin-notice-info">
                            <p><strong>' . esc_html__('Want more features?', 'bizzplugin-framework') . '</strong></p>
                            <p>' . esc_html__('Upgrade to Premium to unlock all features including advanced analytics, premium layouts, priority support, and more!', 'bizzplugin-framework') . '</p>
                            <p><a href="#" class="button button-primary">' . esc_html__('Upgrade to Premium', 'bizzplugin-framework') . '</a></p>
                        </div>',
                    ),
                ),
            ),
            
            // Repeater Section - Example of repeater field
            array(
                'id' => 'repeater_demo',
                'title' => __('Repeater Field', 'bizzplugin-framework'),
                'description' => __('Repeater field allows you to add multiple sets of data. You can add, remove, and reorder items.', 'bizzplugin-framework'),
                'icon' => 'dashicons dashicons-list-view',
                'fields' => array(
                    array(
                        'id' => 'team_members',
                        'type' => 'repeater',
                        'title' => __('Team Members', 'bizzplugin-framework'),
                        'description' => __('Add your team members information. You can add multiple members, reorder them by dragging, and remove them.', 'bizzplugin-framework'),
                        'button_text' => __('Add Team Member', 'bizzplugin-framework'),
                        'max_items' => 10,
                        'min_items' => 0,
                        'sortable' => true,
                        'fields' => array(
                            array(
                                'id' => 'name',
                                'type' => 'text',
                                'title' => __('Name', 'bizzplugin-framework'),
                                'description' => __('Enter team member name.', 'bizzplugin-framework'),
                                'placeholder' => __('Enter name...', 'bizzplugin-framework'),
                            ),
                            array(
                                'id' => 'email',
                                'type' => 'email',
                                'title' => __('Email', 'bizzplugin-framework'),
                                'description' => __('Enter email address.', 'bizzplugin-framework'),
                                'placeholder' => 'email@example.com',
                            ),
                            array(
                                'id' => 'position',
                                'type' => 'text',
                                'title' => __('Position', 'bizzplugin-framework'),
                                'description' => __('Job title or position.', 'bizzplugin-framework'),
                                'placeholder' => __('e.g. Developer', 'bizzplugin-framework'),
                            ),
                            array(
                                'id' => 'image',
                                'type' => 'image',
                                'title' => __('Photo', 'bizzplugin-framework'),
                                'description' => __('Upload team member photo.', 'bizzplugin-framework'),
                            ),
                        ),
                        'default' => array(
                            array(
                                'name' => 'John Doe',
                                'email' => 'john.doe@example.com',
                                'position' => 'Developer',
                                'image' => '',
                            ),
                            array(
                                'name' => 'Jane Smith',
                                'email' => 'jane.smith@example.com',
                                'position' => 'Designer',
                                'image' => '',
                            ),
                        ),
                    ),
                    array(
                        'id' => 'social_links',
                        'type' => 'repeater',
                        'title' => __('Social Links', 'bizzplugin-framework'),
                        'description' => __('Add social media links. Minimum 1 link required.', 'bizzplugin-framework'),
                        'button_text' => __('Add Social Link', 'bizzplugin-framework'),
                        'max_items' => 5,
                        'min_items' => 1,
                        'sortable' => true,
                        'fields' => array(
                            array(
                                'id' => 'platform',
                                'type' => 'select',
                                'title' => __('Platform', 'bizzplugin-framework'),
                                'options' => array(
                                    'facebook' => __('Facebook', 'bizzplugin-framework'),
                                    'twitter' => __('Twitter/X', 'bizzplugin-framework'),
                                    'instagram' => __('Instagram', 'bizzplugin-framework'),
                                    'linkedin' => __('LinkedIn', 'bizzplugin-framework'),
                                    'youtube' => __('YouTube', 'bizzplugin-framework'),
                                ),
                            ),
                            array(
                                'id' => 'url',
                                'type' => 'url',
                                'title' => __('Profile URL', 'bizzplugin-framework'),
                                'placeholder' => 'https://',
                            ),
                        ),
                        'default' => array(
                            array(
                                'platform' => 'facebook',
                                'url' => 'https://facebook.com/codersaiful',
                            ),
                        ),
                    ),
                ),
            ),
            
            // Recommended Plugins Section
            array(
                'id' => 'recommended_plugins',
                'title' => __('Recommended Plugins', 'bizzplugin-framework'),
                'description' => __('Enhance your WordPress site with these recommended plugins. Install and activate them directly from here.', 'bizzplugin-framework'),
                'icon' => 'dashicons dashicons-plugins-checked',
                'fields' => array(
                    array(
                        'id' => 'recommended_plugins_info',
                        'type' => 'html',
                        'title' => __('About Recommended Plugins', 'bizzplugin-framework'),
                        'content' => '<div class="bizzplugin-notice bizzplugin-notice-info">
                            <p><strong>' . esc_html__('Discover Amazing Plugins!', 'bizzplugin-framework') . '</strong></p>
                            <p>' . esc_html__('These plugins are carefully selected to work perfectly with your setup. Install them with a single click!', 'bizzplugin-framework') . '</p>
                        </div>',
                    ),
                    array(
                        'id' => 'recommended_plugins_list',
                        'type' => 'plugins',
                        'title' => __('Recommended Plugins', 'bizzplugin-framework'),
                        'description' => __('Click Install to download and install the plugin, then click Activate to enable it.', 'bizzplugin-framework'),
                        'plugins' => $this->get_recommended_plugins(),
                    ),
                ),
            ),
        );
    }
    
    /**
     * Get category options for select field
     */
    private function get_category_options() {
        $options = array();
        $categories = get_categories(array('hide_empty' => false));
        
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }
        
        return $options;
    }
    /**
     * Get recommended plugins
     *
     * @param [type] $config
     * @return void
     */
    private function get_recommended_plugins() {
        return array(
            array(
                'slug' => 'woo-product-table',
                'name' => 'Woo Product Table (MullArr)',
                'description' => __('A powerful plugin to display WooCommerce products in a table layout.', 'bizzplugin-framework'),
                'thumbnail' => 'https://ps.w.org/woo-product-table/assets/icon-256x256.gif',
                'author' => 'Bizzplugin',
                'file' => 'woo-product-table/woo-product-table.php',
                'url' => 'https://wordpress.org/plugins/woo-product-table/',
            ),
            
            // array(
            //     'slug' => 'woo-min-max-quantity-step-control-single',
            //     'name' => 'Min Max Control for WooCommerce (MullArr)',
            //     'description' => __('A plugin to control minimum and maximum quantity steps for WooCommerce products.', 'bizzplugin-framework'),
            //     'thumbnail' => 'https://ps.w.org/woo-min-max-quantity-step-control-single/assets/icon-256x256.png',
            //     'author' => 'Bizzplugin',
            //     'file' => 'woo-min-max-quantity-step-control-single/wcmmq.php',
            //     'url' => 'https://wordpress.org/plugins/woo-min-max-quantity-step-control-single/',
            // ),

            // array(
            //     'slug' => 'elementor',
            //     'name' => 'Elementor',
            //     'description' => __('The most advanced frontend drag & drop page builder. Create high-end, pixel perfect websites at record speeds.', 'bizzplugin-framework'),
            //     'thumbnail' => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
            //     'author' => 'Elementor.com',
            //     'file' => 'elementor/elementor.php',
            //     'url' => 'https://wordpress.org/plugins/elementor/',
            // ),
            // array(
            //     'slug' => 'contact-form-7',
            //     'name' => 'Contact Form 7',
            //     'description' => __('Just another contact form plugin. Simple but flexible.', 'bizzplugin-framework'),
            //     'thumbnail' => 'https://ps.w.org/contact-form-7/assets/icon-256x256.png',
            //     'author' => 'Takayuki Miyoshi',
            //     'file' => 'contact-form-7/wp-contact-form-7.php',
            //     'url' => 'https://wordpress.org/plugins/contact-form-7/',
            // ),
            // array(
            //     'slug' => 'elementor',
            //     'name' => 'Elementor',
            //     'description' => __('The most advanced frontend drag & drop page builder. Create high-end, pixel perfect websites at record speeds.', 'bizzplugin-framework'),
            //     'thumbnail' => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
            //     'author' => 'Elementor.com',
            //     'file' => 'elementor/elementor.php',
            //     'url' => 'https://wordpress.org/plugins/elementor/',
            // ),
            // array(
            //     'slug' => 'contact-form-7',
            //     'name' => 'Contact Form 7',
            //     'description' => __('Just another contact form plugin. Simple but flexible.', 'bizzplugin-framework'),
            //     'thumbnail' => 'https://ps.w.org/contact-form-7/assets/icon-256x256.png',
            //     'author' => 'Takayuki Miyoshi',
            //     'file' => 'contact-form-7/wp-contact-form-7.php',
            //     'url' => 'https://wordpress.org/plugins/contact-form-7/',
            // ),
            // array(
            //     'slug' => 'woocommerce',
            //     'name' => 'WooCommerce',
            //     'description' => __('WooCommerce is the world\'s most popular open-source eCommerce solution.', 'bizzplugin-framework'),
            //     'thumbnail' => 'https://ps.w.org/woocommerce/assets/icon-256x256.png',
            //     'author' => 'Automattic',
            //     'file' => 'woocommerce/woocommerce.php',
            //     'url' => 'https://wordpress.org/plugins/woocommerce/',
            // ),
        );
    }

    public function panel_config(){
        return array(
            'title' => __('Bizzplugin Option Framework', 'bizzplugin-framework'),
            'logo' => BIZZPLUGIN_PLUGIN_URL . 'assets/imgs/min-max-logo.png',
            'is_premium' => false,
            'version' => BIZZPLUGIN_PLUGIN_VERSION,
            'recommended_plugins' => $this->get_recommended_plugins(),
            'resources' => array(
                array(
                    'icon' => 'dashicons dashicons-book',
                    'title' => __('Documentation (MullArr)', 'bizzplugin-framework'),
                    'url' => 'https://bizzplugin.com/docs/bizzplugin-option-framework/',
                ),
                // array(
                //     'icon' => 'dashicons dashicons-sos',
                //     'title' => __('Support', 'bizzplugin-framework'),
                //     'url' => 'https://bizzplugin.com/support/',
                // ),
                // array(
                //     'icon' => 'dashicons dashicons-cart',
                //     'title' => __('Upgrade to Premium', 'bizzplugin-framework'),
                //     'url' => 'https://bizzplugin.com/plugins/bizzplugin-option-framework/',
                // ),
            ),
            'footer_text' => __('Powered by Bizzplugin', 'bizzplugin-framework'),
        );
    }
    /**
     * Modify panel configuration
     */
    public function modify_panel_config($config) {
        // Example: Change the panel title
        $config['title'] = __('Min Max Control', 'bizzplugin-framework');
        //min max control logo from assets/imgs
        $config['logo'] = BIZZPLUGIN_PLUGIN_URL . 'assets/imgs/min-max-logo.png';
        $config['is_premium'] = true;
        //version 
        $config['version'] = BIZZPLUGIN_PLUGIN_VERSION;
        $config['recommended_plugins'] = $this->get_recommended_plugins();
        //resource links
        $config['resources'] = array(
            array(
                'icon' => 'dashicons dashicons-book',
                'title' => __('Documentation', 'bizzplugin-framework'),
                'url' => 'https://bizzplugin.com/docs/min-max-quantity-step-control/',
            ),
            array(
                'icon' => 'dashicons dashicons-sos',
                'title' => __('Support', 'bizzplugin-framework'),
                'url' => 'https://bizzplugin.com/support/',
            ),
            array(
                'icon' => 'dashicons dashicons-cart',
                'title' => __('Upgrade to Premium', 'bizzplugin-framework'),
                'url' => 'https://bizzplugin.com/plugins/min-max-quantity-step-control-woocommerce/',
            ),
            //Premium Pricing Table
            array(
                'icon' => 'dashicons dashicons-star-filled',
                'title' => __('Premium Pricing', 'bizzplugin-framework'),
                'url' => 'https://bizzplugin.com/pricing/',
            ),
        );
        $config['footer_text'] = __('Powered by Bizzplugin', 'bizzplugin-framework');
        // echo "<pre>";
        // print_r($config);
        // echo "</pre>";
        return $config;
    }
    
    /**
     * Modify panel configuration for 'bizzplugin_sample' panel only
     * 
     * This filter only applies to the 'bizzplugin_sample' panel.
     * Use this pattern when you have multiple panels and need individual configuration.
     * 
     * Available panel-specific filters:
     * - bizzplugin_panel_config_{$panel_id} - Configure panel branding/config
     * - bizzplugin_section_fields_{$panel_id} - Add/modify fields for a specific panel
     * - bizzplugin_panel_sections_{$panel_id} - Add/modify sections for a specific panel
     * - bizzplugin_is_premium_{$panel_id} - Set premium status for a specific panel
     * 
     * @param array $config Panel configuration array
     * @param string $panel_id The panel ID (available for consistency)
     * @return array Modified configuration
     */
    public function modify_sample_panel_config($config, $panel_id) {
        // Example: This will only affect 'bizzplugin_sample' panel
        // $config['custom_key'] = 'value_for_sample_only';
        return $config;
    }
    
    /**
     * Modify panel configuration for 'bizzplugin_secondary' panel only
     * 
     * @param array $config Panel configuration array
     * @param string $panel_id The panel ID (available for consistency)
     * @return array Modified configuration
     */
    public function modify_secondary_panel_config($config, $panel_id) {
        // Example: This will only affect 'bizzplugin_secondary' panel
        //is_premium true
        $config['is_premium'] = false;
        // You can have completely different branding/config for this panel
        $config['title'] = __('Secondary Panel Title', 'bizzplugin-framework');
        //resource links
        $config['resources'] = array(
            array(
                'icon' => 'dashicons dashicons-book',
                'title' => __('Documentation', 'bizzplugin-framework'),
                'url' => 'https://bizzplugin.com/docs/min-max-quantity-step-control/',
            ),
            array(
                'icon' => 'dashicons dashicons-sos',
                'title' => __('ONNY SUPPORT', 'bizzplugin-framework'),
                'url' => 'https://bizzplugin.com/support/',
            ),
            array(
                'icon' => 'dashicons dashicons-cart',
                'title' => __('Upgrade to Premium', 'bizzplugin-framework'),
                'url' => 'https://bizzplugin.com/plugins/min-max-quantity-step-control-woocommerce/',
            ),
        );
        // $config['logo'] = BIZZPLUGIN_PLUGIN_URL . 'assets/imgs/secondary-logo.png';
        return $config;
    }
}

// Initialize the plugin
BizzPlugin_Option_Framework::get_instance();

/**
 * Helper function to get plugin options
 * * @param string $key Option key
 * @param mixed $default Default value if option not set
 * @return mixed Option value or default
 * Need to develop this function to get specific option value by key
 */
function bizzplugin_get_option($key, $default = '') {
    $options = get_option('bizzplugin_options', array());
    
    if (empty($key)) {
        return $options;
    }
    
    return isset($options[$key]) ? $options[$key] : $default;
}
