<?php
/**
 * BizzPlugin Options Framework - Main Framework Class
 * 
 * @package BizzPlugin_Options_Framework
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Framework Class
 */
class BizzPlugin_Framework {
    
    /**
     * Framework version
     */
    const VERSION = '1.0.1';
    
    /**
     * Single instance of the class
     */
    private static $instance = null;

    /**
     * Field type to CSS file mapping
     * Use this in class-bizzplugin-framework.php
     */
    private $field_css_map = array();
    
    /**
     * Registered options panels
     */
    private $panels = array();
    
    public $panel = null;

    /**
     * Framework URL
     */
    private $framework_url = '';
    
    /**
     * Framework path
     */
    private $framework_path = '';
    
    /**
     * Get single instance
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
    public function __construct() {
        $this->framework_path = dirname(__FILE__);
        $this->framework_url = $this->get_framework_url();
        
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Get framework URL
     */
    private function get_framework_url() {
        $path = wp_normalize_path($this->framework_path);
        $plugins_dir = wp_normalize_path(WP_PLUGIN_DIR);
        $themes_dir = wp_normalize_path(get_theme_root());
        
        if (strpos($path, $plugins_dir) !== false) {
            return plugin_dir_url(__FILE__);
        } elseif (strpos($path, $themes_dir) !== false) {
            $relative = str_replace($themes_dir, '', $path);
            return get_theme_root_uri() . $relative . '/';
        }
        
        return plugin_dir_url(__FILE__);
    }
    
    /**
     * Load dependencies
     */
    private function load_dependencies() {
        require_once $this->framework_path . '/includes/class-field-sanitizer.php';
        require_once $this->framework_path . '/includes/class-field-validator.php';
        require_once $this->framework_path . '/includes/class-ajax-handler.php';
        require_once $this->framework_path . '/includes/class-api-handler.php';
        require_once $this->framework_path . '/includes/class-webhook-handler.php';
        require_once $this->framework_path . '/class-bizzplugin-panel.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_bizzplugin_save_options', array($this, 'ajax_save_options'));
        add_action('wp_ajax_bizzplugin_reset_section', array($this, 'ajax_reset_section'));
        add_action('wp_ajax_bizzplugin_reset_all', array($this, 'ajax_reset_all'));
        add_action('wp_ajax_bizzplugin_test_webhook', array($this, 'ajax_test_webhook'));
        add_action('wp_ajax_bizzplugin_install_plugin', array($this, 'ajax_install_plugin'));
        add_action('wp_ajax_bizzplugin_activate_plugin', array($this, 'ajax_activate_plugin'));
        add_action('wp_ajax_bizzplugin_generate_api_key', array($this, 'ajax_generate_api_key'));
        add_action('wp_ajax_bizzplugin_delete_api_key', array($this, 'ajax_delete_api_key'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        
        // Allow plugins to extend framework
        do_action('bizzplugin_framework_loaded', $this);
    }
    
    /**
     * Enqueue assets
     */
    public function enqueue_assets($hook) {
        // Check if we're on a registered options page
        $is_options_page = false;
        foreach ($this->panels as $panel) {
            if ($panel->is_current_page($hook)) {
                $is_options_page = true;
                break;
            }
        }
        
        if (!$is_options_page) {
            return;
        }
        if(empty($panel->get_id())) return;
        
        // Enqueue WordPress media uploader
        wp_enqueue_media();
        
        // Enqueue color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Enqueue jQuery UI for datepicker (use WordPress built-in styles)
        wp_enqueue_script('jquery-ui-datepicker');
        
        // Framework CSS (includes datepicker styles)
        // wp_enqueue_style(
        //     'bizzplugin-framework-style',
        //     $this->framework_url . 'assets/css/framework.css',
        //     array(),
        //     self::VERSION
        // );

        /**
         * 
        assets/css/
        ├── framework.css              # মূল ফাইল (অপরিবর্তিত)
        ├── framework-common.css       # কমন CSS
        ├── fields/                    # ফিল্ড-ভিত্তিক CSS
        │   ├── text.css
        │   ├── textarea.css
        │   ├── select.css
        │   ├── checkbox.css
        │   ├── radio.css
        │   ├── switch.css
        │   ├── color.css
        │   ├── date.css
        │   ├── image.css
        │   ├── file.css
        │   ├── image-select.css
        │   ├── option-select.css
        │   ├── post-select.css
        │   ├── slider.css
        │   ├── repeater.css
        │   ├── plugins.css
        │   └── html.css
        └── components/
            └── api-section.css
        * // I have to add all thse files separately because they are required for different fields and components in the framework.
        */
        wp_enqueue_style(
            'bizzplugin-framework-common-style',
            $this->framework_url . 'assets/css/framework-common.css',
            array(),
            self::VERSION
        );
        
        $this->field_css_map = array(
            // Text-like fields
            'text'           => 'text.css',
            'email'          => 'text.css',
            'url'            => 'text.css',
            'number'         => 'text.css',
            'password'       => 'text.css',
            
            // Textarea
            'textarea'       => 'textarea.css',
            
            // Selection fields
            'select'         => 'select.css',
            'multi_select'   => 'select.css',
            'checkbox'       => 'checkbox.css',
            'checkbox_group' => 'checkbox.css',
            'radio'          => 'radio.css',
            
            // Toggle fields
            'on_off'         => 'switch.css',
            'switch'         => 'switch.css',
            
            // Picker fields
            'color'          => 'color.css',
            'date'           => 'date.css',
            
            // Media fields
            'image'          => 'image.css',
            'file'           => 'file.css',
            'image_select'   => 'image-select.css',
            
            // Other selection
            'option_select'  => 'option-select.css',
            'post_select'    => 'post-select.css',
            
            // Complex fields
            'slider'         => 'slider.css',
            'range'          => 'slider.css',
            'repeater'       => 'repeater.css',
            'plugins'        => 'plugins.css',
            
            // Display-only fields
            'html'           => 'html.css',
            'info'           => 'html.css',
            'notice'         => 'html.css',
            'heading'        => 'html.css',
            'divider'        => 'html.css',
            'link'           => 'html.css',
        );
        $css_map = apply_filters('bizzplugin_framework_field_css_map_' . $panel->get_id(), $this->field_css_map);
        $temp_file = '';
        foreach ($css_map as $field_type => $css_file) {
            if ($temp_file === $css_file) continue; //skip if already enqueued                
            wp_enqueue_style(
                'bizzplugin-framework-field-' . $field_type . '-style',
                $this->framework_url . 'assets/css/fields/' . $css_file,
                array(),
                self::VERSION
            );
            $temp_file = $css_file;
        }
        // Enqueue component-specific styles
        wp_enqueue_style(
            'bizzplugin-framework-component-api-section-style',
            $this->framework_url . 'assets/css/components/api-section.css',
            array(),
            self::VERSION
        );
        // Framework JS
        wp_enqueue_script(
            'bizzplugin-framework-script',
            $this->framework_url . 'assets/js/framework.js',
            array('jquery', 'wp-color-picker', 'jquery-ui-datepicker'),
            self::VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('bizzplugin-framework-script', 'bizzpluginFramework', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bizzplugin_framework_nonce'),
            'strings' => array(
                'saving' => __('Saving...', 'bizzplugin-framework'),
                'saved' => __('Settings Saved!', 'bizzplugin-framework'),
                'error' => __('Error saving settings', 'bizzplugin-framework'),
                'resetting' => __('Resetting...', 'bizzplugin-framework'),
                'reset_success' => __('Section Reset!', 'bizzplugin-framework'),
                'reset_all_success' => __('All settings reset successfully!', 'bizzplugin-framework'),
                'reset_error' => __('Error resetting section', 'bizzplugin-framework'),
                'confirm_reset' => __('Are you sure you want to reset this section to default values?', 'bizzplugin-framework'),
                'confirm_reset_all' => __('Are you sure you want to reset ALL settings to default values?', 'bizzplugin-framework'),
                'select_image' => __('Select Image', 'bizzplugin-framework'),
                'select_file' => __('Select File', 'bizzplugin-framework'),
                'use_image' => __('Use this image', 'bizzplugin-framework'),
                'use_file' => __('Use this file', 'bizzplugin-framework'),
                'webhook_url_required' => __('Please enter a webhook URL first.', 'bizzplugin-framework'),
                'testing' => __('Testing...', 'bizzplugin-framework'),
                'export_success' => __('Settings exported successfully!', 'bizzplugin-framework'),
                'import_success' => __('Settings imported successfully! Please save to apply changes.', 'bizzplugin-framework'),
                'import_error' => __('Error parsing import file.', 'bizzplugin-framework'),
                'import_invalid' => __('Invalid import file format.', 'bizzplugin-framework'),
                'import_panel_mismatch' => __('This export was created from a different panel. Do you want to continue importing?', 'bizzplugin-framework'),
                'installing' => __('Installing...', 'bizzplugin-framework'),
                'installed_inactive' => __('Installed (Inactive)', 'bizzplugin-framework'),
                'activate' => __('Activate', 'bizzplugin-framework'),
                'activating' => __('Activating...', 'bizzplugin-framework'),
                'active' => __('Active', 'bizzplugin-framework'),
                'activated' => __('Activated', 'bizzplugin-framework'),
                'install_error' => __('Error installing plugin', 'bizzplugin-framework'),
                'activate_error' => __('Error activating plugin', 'bizzplugin-framework'),
                'confirm_regenerate_api_key' => __('Are you sure you want to generate a new API key? The old key will be invalidated.', 'bizzplugin-framework'),
                'generating' => __('Generating...', 'bizzplugin-framework'),
                'regenerate_api_key' => __('Regenerate API Key', 'bizzplugin-framework'),
                'api_key_error' => __('Error generating API key', 'bizzplugin-framework'),
                'search_result_single' => __('1 section found', 'bizzplugin-framework'),
                'search_results_plural' => __('%d sections found', 'bizzplugin-framework'),
                'search_no_results' => __('No results found', 'bizzplugin-framework'),
            )
        ));
    }
    
    /**
     * Register a new options panel
     */
    public function create_panel($args) {
        
        $defaults = array(
            'id' => '',
            'title' => '',
            'menu_title' => '',
            'menu_slug' => '',
            'parent_slug' => '',
            'capability' => 'manage_options',
            'icon' => 'dashicons-admin-settings',
            'position' => null,
            'option_name' => '',
            'is_premium' => false,
            'sections' => array(),
            'route_namespace' => 'bizzplugin/v1',
            'enable_search' => true,
            'show_export_import' => true,
            'show_api' => true,
        );
        
        $args = wp_parse_args($args, $defaults);
        
        if (empty($args['id']) || empty($args['option_name'])) {
            return false;
        }
        
        $panel = new BizzPlugin_Panel($args, $this);

        $this->panels[$args['id']] = $panel;
        
        // Allow other plugins to add fields to this panel
        do_action('bizzplugin_panel_created', $panel, $args['id']);
        $this->panel = $panel;
        return $this->panel;//;
    }
    
    /**
     * Get a panel by ID
     */
    public function get_panel($panel_id) {
        return isset($this->panels[$panel_id]) ? $this->panels[$panel_id] : null;
    }
    
    /**
     * Get all panels
     */
    public function get_panels() {
        return $this->panels;
    }
    
    /**
     * AJAX save options
     */
    public function ajax_save_options() {
        check_ajax_referer('bizzplugin_framework_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'bizzplugin-framework')));
        }
        
        $option_name = isset($_POST['option_name']) ? sanitize_text_field(wp_unslash($_POST['option_name'])) : '';
        $panel_id = isset($_POST['panel_id']) ? sanitize_text_field(wp_unslash($_POST['panel_id'])) : '';
        $data = isset($_POST['data']) ? wp_unslash($_POST['data']) : array();
        
        if (empty($option_name) || empty($panel_id)) {
            wp_send_json_error(array('message' => __('Invalid request', 'bizzplugin-framework')));
        }
        
        $panel = $this->get_panel($panel_id);
        if (!$panel) {
            wp_send_json_error(array('message' => __('Panel not found', 'bizzplugin-framework')));
        }
        
        // Sanitize and validate data
        $ajax_handler = new BizzPlugin_Ajax_Handler();
        $sanitized_data = $ajax_handler->sanitize_options($data, $panel);
        $validation_result = $ajax_handler->validate_options($sanitized_data, $panel);
        
        if (is_wp_error($validation_result)) {
            wp_send_json_error(array(
                'message' => $validation_result->get_error_message(),
                'errors' => $validation_result->get_error_data()
            ));
        }
        
        // Save options
        $old_options = get_option($option_name, array());
        $saved = update_option($option_name, $sanitized_data);
        
        // Trigger webhook if configured
        do_action('bizzplugin_options_saved', $option_name, $sanitized_data, $old_options, $panel_id);
        
        wp_send_json_success(array(
            'message' => __('Settings saved successfully!', 'bizzplugin-framework'),
            'data' => $sanitized_data
        ));
    }
    
    /**
     * AJAX reset section
     */
    public function ajax_reset_section() {
        check_ajax_referer('bizzplugin_framework_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'bizzplugin-framework')));
        }
        
        $option_name = isset($_POST['option_name']) ? sanitize_text_field(wp_unslash($_POST['option_name'])) : '';
        $panel_id = isset($_POST['panel_id']) ? sanitize_text_field(wp_unslash($_POST['panel_id'])) : '';
        $section_id = isset($_POST['section_id']) ? sanitize_text_field(wp_unslash($_POST['section_id'])) : '';
        $subsection_id = isset($_POST['subsection_id']) ? sanitize_text_field(wp_unslash($_POST['subsection_id'])) : '';

        if (empty($option_name) || empty($panel_id) || empty($section_id)) {
            wp_send_json_error(array('message' => __('Invalid request', 'bizzplugin-framework')));
        }
        
        $panel = $this->get_panel($panel_id);
        if (!$panel) {
            wp_send_json_error(array('message' => __('Panel not found', 'bizzplugin-framework')));
        }
        
        // Get default values for the section
        $section_defaults = $panel->get_section_defaults($section_id, $subsection_id);
        
        // Get current options and merge with defaults
        $current_options = get_option($option_name, array());
        foreach ($section_defaults as $field_id => $default_value) {
            $current_options[$field_id] = $default_value;
        }
        
        update_option($option_name, $current_options);
        
        wp_send_json_success(array(
            'message' => __('Section reset successfully!', 'bizzplugin-framework'),
            'defaults' => $section_defaults
        ));
    }
    
    /**
     * AJAX reset all options
     */
    public function ajax_reset_all() {
        check_ajax_referer('bizzplugin_framework_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'bizzplugin-framework')));
        }
        
        $option_name = isset($_POST['option_name']) ? sanitize_text_field(wp_unslash($_POST['option_name'])) : '';
        $panel_id = isset($_POST['panel_id']) ? sanitize_text_field(wp_unslash($_POST['panel_id'])) : '';
        
        if (empty($option_name) || empty($panel_id)) {
            wp_send_json_error(array('message' => __('Invalid request', 'bizzplugin-framework')));
        }
        
        $panel = $this->get_panel($panel_id);
        if (!$panel) {
            wp_send_json_error(array('message' => __('Panel not found', 'bizzplugin-framework')));
        }
        
        // Get all default values
        $all_defaults = $panel->get_all_defaults();
        
        // Update options with defaults
        update_option($option_name, $all_defaults);
        
        wp_send_json_success(array(
            'message' => __('All settings reset successfully!', 'bizzplugin-framework'),
            'defaults' => $all_defaults
        ));
    }
    
    /**
     * AJAX test webhook
     */
    public function ajax_test_webhook() {
        check_ajax_referer('bizzplugin_framework_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'bizzplugin-framework')));
        }
        
        $option_name = isset($_POST['option_name']) ? sanitize_text_field(wp_unslash($_POST['option_name'])) : '';
        $webhook_url = isset($_POST['webhook_url']) ? esc_url_raw(wp_unslash($_POST['webhook_url'])) : '';
        
        if (empty($webhook_url)) {
            wp_send_json_error(array('message' => __('Webhook URL is required', 'bizzplugin-framework')));
        }
        
        // Validate URL format
        if (!filter_var($webhook_url, FILTER_VALIDATE_URL)) {
            wp_send_json_error(array('message' => __('Invalid webhook URL format', 'bizzplugin-framework')));
        }
        
        // Prepare test payload
        $payload = array(
            'event' => 'webhook_test',
            'option_name' => $option_name,
            'timestamp' => gmdate('c'),
            'site_url' => get_site_url(),
            'message' => __('This is a test webhook from BizzPlugin Options Framework', 'bizzplugin-framework'),
        );
        
        // Send test webhook
        $response = wp_remote_post($webhook_url, array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-BizzPlugin-Event' => 'webhook_test',
            ),
            'body' => wp_json_encode($payload),
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'message' => $response->get_error_message(),
                'payload' => $payload
            ));
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        wp_send_json_success(array(
            'message' => __('Webhook test sent successfully!', 'bizzplugin-framework'),
            'response_code' => $response_code,
            'response_body' => $response_body,
            'payload_sent' => $payload
        ));
    }
    
    /**
     * AJAX install plugin
     */
    public function ajax_install_plugin() {
        check_ajax_referer('bizzplugin_framework_nonce', 'nonce');
        
        if (!current_user_can('install_plugins')) {
            wp_send_json_error(array('message' => __('Permission denied', 'bizzplugin-framework')));
        }
        
        $slug = isset($_POST['slug']) ? sanitize_text_field(wp_unslash($_POST['slug'])) : '';
        
        if (empty($slug)) {
            wp_send_json_error(array('message' => __('Plugin slug is required', 'bizzplugin-framework')));
        }
        
        // Validate plugin slug format (lowercase letters, numbers, hyphens only)
        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            wp_send_json_error(array('message' => __('Invalid plugin slug format', 'bizzplugin-framework')));
        }
        
        // Include required files for plugin installation
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        
        // Get plugin info from WordPress.org
        $api = plugins_api('plugin_information', array(
            'slug' => $slug,
            'fields' => array(
                'short_description' => false,
                'sections' => false,
                'requires' => false,
                'rating' => false,
                'ratings' => false,
                'downloaded' => false,
                'last_updated' => false,
                'added' => false,
                'tags' => false,
                'compatibility' => false,
                'homepage' => false,
                'donate_link' => false,
            ),
        ));
        
        if (is_wp_error($api)) {
            wp_send_json_error(array('message' => $api->get_error_message()));
        }
        
        // Create a silent skin for the upgrader
        $skin = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader($skin);
        
        $result = $upgrader->install($api->download_link);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        if ($result === false) {
            wp_send_json_error(array('message' => __('Plugin installation failed', 'bizzplugin-framework')));
        }
        
        wp_send_json_success(array(
            'message' => __('Plugin installed successfully!', 'bizzplugin-framework'),
            'status' => 'installed'
        ));
    }
    
    /**
     * AJAX activate plugin
     */
    public function ajax_activate_plugin() {
        check_ajax_referer('bizzplugin_framework_nonce', 'nonce');
        
        if (!current_user_can('activate_plugins')) {
            wp_send_json_error(array('message' => __('Permission denied', 'bizzplugin-framework')));
        }
        
        $plugin_file = isset($_POST['file']) ? sanitize_text_field(wp_unslash($_POST['file'])) : '';
        
        if (empty($plugin_file)) {
            wp_send_json_error(array('message' => __('Plugin file is required', 'bizzplugin-framework')));
        }
        
        // Use WordPress validate_file() for comprehensive path validation
        // Returns 0 if valid, 1 if empty, 2 if contains '..' or './', 3 if absolute path
        $validation_result = validate_file($plugin_file);
        if ($validation_result !== 0) {
            wp_send_json_error(array('message' => __('Invalid plugin file path', 'bizzplugin-framework')));
        }
        
        // Additional check: ensure plugin file matches expected format (plugin-folder/plugin-file.php)
        if (!preg_match('/^[a-z0-9_-]+\/[a-z0-9_-]+\.php$/i', $plugin_file)) {
            wp_send_json_error(array('message' => __('Invalid plugin file format', 'bizzplugin-framework')));
        }
        
        // Include plugin functions if not available
        if (!function_exists('activate_plugin')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        
        // Verify the plugin exists before activating
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $installed_plugins = get_plugins();
        if (!array_key_exists($plugin_file, $installed_plugins)) {
            wp_send_json_error(array('message' => __('Plugin not found', 'bizzplugin-framework')));
        }
        
        $result = activate_plugin($plugin_file);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        wp_send_json_success(array(
            'message' => __('Plugin activated successfully!', 'bizzplugin-framework'),
            'status' => 'active'
        ));
    }
    
    /**
     * AJAX handler for generating API key
     */
    public function ajax_generate_api_key() {
        check_ajax_referer('bizzplugin_framework_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'bizzplugin-framework')));
        }
        
        // Get panel ID from POST data
        $panel_id = isset($_POST['panel_id']) ? sanitize_text_field($_POST['panel_id']) : null;
        
        if (empty($panel_id)) {
            wp_send_json_error(array('message' => __('Panel ID is required', 'bizzplugin-framework')));
        }
        
        $api_key = BizzPlugin_API_Handler::generate_api_key($panel_id);
        
        wp_send_json_success(array(
            'message' => sprintf(__('API key generated successfully for panel "%s"!', 'bizzplugin-framework'), $panel_id),
            'api_key' => $api_key,
            'panel_id' => $panel_id
        ));
    }

    /**
     * AJAX handler for deleting API key
     */
    public function ajax_delete_api_key() {
        check_ajax_referer('bizzplugin_framework_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'bizzplugin-framework')));
        }
        
        // Get panel ID from POST data
        $panel_id = isset($_POST['panel_id']) ? sanitize_text_field($_POST['panel_id']) : null;
        
        if (empty($panel_id)) {
            wp_send_json_error(array('message' => __('Panel ID is required', 'bizzplugin-framework')));
        }
        
        $deleted = BizzPlugin_API_Handler::delete_api_key($panel_id);
        
        if ($deleted) {
            wp_send_json_success(array(
                'message' => sprintf(__('API key deleted successfully for panel "%s"!', 'bizzplugin-framework'), $panel_id),
                'panel_id' => $panel_id
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete API key', 'bizzplugin-framework')));
        }
    }
    
    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        $api_handler = new BizzPlugin_API_Handler($this);
        $api_handler->register_routes();
    }
    
    /**
     * Get framework URL
     */
    public function get_url() {
        return $this->framework_url;
    }
    
    /**
     * Get framework path
     */
    public function get_path() {
        return $this->framework_path;
    }

    public function __call($name, $arguments)
    {
        error_log( "Undefined method: " . $name . " has been call from " . __CLASS__ );
        return null;
    }
}

/**
 * Get framework instance
 */
function bizzplugin_framework() {
    return BizzPlugin_Framework::get_instance();
}
