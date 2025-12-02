# Examples

This document provides complete, working code examples for common use cases.

## Basic Plugin Setup

### Minimal Plugin Example

```php
<?php
/**
 * Plugin Name: My Simple Plugin
 * Description: A simple plugin using BizzPlugin Options Framework
 * Version: 1.0.0
 * Text Domain: my-simple-plugin
 */

if (!defined('ABSPATH')) exit;

define('MSP_PATH', plugin_dir_path(__FILE__));
define('MSP_URL', plugin_dir_url(__FILE__));

// Load the options framework
require_once MSP_PATH . 'options-framework/options-loader.php';

// Initialize settings
add_action('init', function() {
    $framework = bizzplugin_framework();
    
    $framework->create_panel(array(
        'id'          => 'my_simple_plugin',
        'title'       => __('My Simple Plugin', 'my-simple-plugin'),
        'menu_title'  => __('Simple Plugin', 'my-simple-plugin'),
        'menu_slug'   => 'my-simple-plugin',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-generic',
        'position'    => 80,
        'option_name' => 'msp_options',
        'sections'    => array(
            array(
                'id'     => 'general',
                'title'  => __('General Settings', 'my-simple-plugin'),
                'icon'   => 'dashicons dashicons-admin-generic',
                'fields' => array(
                    array(
                        'id'          => 'welcome_text',
                        'type'        => 'text',
                        'title'       => __('Welcome Text', 'my-simple-plugin'),
                        'description' => __('Enter text to display.', 'my-simple-plugin'),
                        'default'     => 'Hello, World!',
                    ),
                    array(
                        'id'          => 'show_welcome',
                        'type'        => 'switch',
                        'title'       => __('Show Welcome Message', 'my-simple-plugin'),
                        'default'     => '1',
                    ),
                ),
            ),
        ),
    ));
});

// Helper function to get options
function msp_get_option($key, $default = '') {
    $options = get_option('msp_options', array());
    return isset($options[$key]) ? $options[$key] : $default;
}

// Shortcode example
add_shortcode('msp_welcome', function() {
    if (msp_get_option('show_welcome', '1') !== '1') {
        return '';
    }
    return '<div class="msp-welcome">' . esc_html(msp_get_option('welcome_text', 'Hello, World!')) . '</div>';
});
```

---

## Complete Plugin with Chainable API

```php
<?php
/**
 * Plugin Name: Advanced Plugin
 * Description: Plugin with full framework features
 * Version: 1.0.0
 * Text Domain: advanced-plugin
 */

if (!defined('ABSPATH')) exit;

define('AP_VERSION', '1.0.0');
define('AP_PATH', plugin_dir_path(__FILE__));
define('AP_URL', plugin_dir_url(__FILE__));

require_once AP_PATH . 'options-framework/options-loader.php';

class Advanced_Plugin {
    
    private static $instance = null;
    private $is_premium = false;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Check license
        $this->is_premium = $this->check_license();
        
        add_action('init', array($this, 'init_settings'));
        add_filter('bizzplugin_is_premium_advanced_plugin', array($this, 'get_premium_status'), 10, 2);
    }
    
    private function check_license() {
        $key = get_option('ap_license_key', '');
        $status = get_option('ap_license_status', '');
        return !empty($key) && $status === 'valid';
    }
    
    public function get_premium_status($is_premium, $panel_id) {
        return $this->is_premium;
    }
    
    public function init_settings() {
        $framework = bizzplugin_framework();
        
        // Create panel
        $panel = $framework->create_panel(array(
            'id'          => 'advanced_plugin',
            'title'       => __('Advanced Plugin', 'advanced-plugin'),
            'menu_title'  => __('Advanced', 'advanced-plugin'),
            'menu_slug'   => 'advanced-plugin',
            'capability'  => 'manage_options',
            'icon'        => 'dashicons-chart-area',
            'position'    => 81,
            'option_name' => 'ap_options',
        ));
        
        // Configure panel
        $panel
            ->set_logo(AP_URL . 'assets/logo.png')
            ->set_version(AP_VERSION)
            ->set_panel_title(__('Advanced Plugin Settings', 'advanced-plugin'))
            ->set_premium($this->is_premium)
            ->set_footer_text(__('© 2024 My Company', 'advanced-plugin'));
        
        // General Section
        $panel->add_section(array(
            'id'          => 'general',
            'title'       => __('General', 'advanced-plugin'),
            'description' => __('General configuration options.', 'advanced-plugin'),
            'icon'        => 'dashicons dashicons-admin-generic',
            'fields'      => array(
                array(
                    'id'          => 'site_name',
                    'type'        => 'text',
                    'title'       => __('Site Name', 'advanced-plugin'),
                    'description' => __('Your site name.', 'advanced-plugin'),
                    'default'     => get_bloginfo('name'),
                ),
                array(
                    'id'          => 'enable_plugin',
                    'type'        => 'switch',
                    'title'       => __('Enable Plugin', 'advanced-plugin'),
                    'description' => __('Turn the plugin on/off.', 'advanced-plugin'),
                    'default'     => '1',
                ),
            ),
        ));
        
        // Add subsection
        $panel->add_subsection('general', array(
            'id'          => 'advanced_options',
            'title'       => __('Advanced Options', 'advanced-plugin'),
            'description' => __('Advanced configuration.', 'advanced-plugin'),
            'fields'      => array(
                array(
                    'id'          => 'cache_enabled',
                    'type'        => 'checkbox',
                    'title'       => __('Enable Cache', 'advanced-plugin'),
                    'default'     => '1',
                    'label'       => __('Enable caching', 'advanced-plugin'),
                ),
                array(
                    'id'          => 'cache_time',
                    'type'        => 'number',
                    'title'       => __('Cache Duration', 'advanced-plugin'),
                    'description' => __('Time in seconds.', 'advanced-plugin'),
                    'default'     => 3600,
                    'min'         => 60,
                    'max'         => 86400,
                    'dependency'  => array(
                        'field' => 'cache_enabled',
                        'value' => '1',
                    ),
                ),
            ),
        ));
        
        // Appearance Section
        $panel->add_section(array(
            'id'          => 'appearance',
            'title'       => __('Appearance', 'advanced-plugin'),
            'description' => __('Visual customization.', 'advanced-plugin'),
            'icon'        => 'dashicons dashicons-admin-appearance',
            'fields'      => array(
                array(
                    'id'          => 'primary_color',
                    'type'        => 'color',
                    'title'       => __('Primary Color', 'advanced-plugin'),
                    'default'     => '#2271b1',
                ),
                array(
                    'id'          => 'layout',
                    'type'        => 'image_select',
                    'title'       => __('Layout', 'advanced-plugin'),
                    'default'     => 'sidebar-right',
                    'options'     => array(
                        'sidebar-left'  => AP_URL . 'options-framework/assets/images/sidebar-left.svg',
                        'no-sidebar'    => AP_URL . 'options-framework/assets/images/no-sidebar.svg',
                        'sidebar-right' => AP_URL . 'options-framework/assets/images/sidebar-right.svg',
                    ),
                ),
                array(
                    'id'          => 'font_size',
                    'type'        => 'slider',
                    'title'       => __('Font Size', 'advanced-plugin'),
                    'default'     => 16,
                    'min'         => 12,
                    'max'         => 24,
                    'step'        => 1,
                    'unit'        => 'px',
                ),
            ),
        ));
        
        // Premium Section
        $panel->add_section(array(
            'id'          => 'premium',
            'title'       => __('Premium Features', 'advanced-plugin'),
            'description' => __('Premium-only features.', 'advanced-plugin'),
            'icon'        => 'dashicons dashicons-star-filled',
            'fields'      => array(
                array(
                    'id'          => 'analytics',
                    'type'        => 'switch',
                    'title'       => __('Advanced Analytics', 'advanced-plugin'),
                    'default'     => '0',
                    'premium'     => true,
                ),
                array(
                    'id'          => 'custom_templates',
                    'type'        => 'switch',
                    'title'       => __('Custom Templates', 'advanced-plugin'),
                    'default'     => '0',
                    'premium'     => true,
                ),
            ),
        ));
        
        // Resources
        $panel
            ->add_resource(array(
                'icon'  => 'dashicons dashicons-book',
                'title' => __('Documentation', 'advanced-plugin'),
                'url'   => 'https://example.com/docs',
            ))
            ->add_resource(array(
                'icon'  => 'dashicons dashicons-sos',
                'title' => __('Support', 'advanced-plugin'),
                'url'   => 'https://example.com/support',
            ));
        
        // Recommended Plugins
        $panel->add_recommended_plugin(array(
            'slug'        => 'elementor',
            'name'        => 'Elementor',
            'description' => __('Page builder.', 'advanced-plugin'),
            'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
            'author'      => 'Elementor.com',
            'file'        => 'elementor/elementor.php',
            'url'         => 'https://wordpress.org/plugins/elementor/',
        ));
    }
}

Advanced_Plugin::get_instance();

// Helper function
function ap_get_option($key, $default = '') {
    $options = get_option('ap_options', array());
    return isset($options[$key]) ? $options[$key] : $default;
}
```

---

## Adding Fields from Addon Plugin

```php
<?php
/**
 * Plugin Name: Advanced Plugin Addon
 * Description: Addon for Advanced Plugin
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

// Wait for framework to load
add_action('bizzplugin_framework_loaded', function($framework) {
    // Get main plugin's panel
    $panel = $framework->get_panel('advanced_plugin');
    
    if (!$panel) {
        return; // Main plugin not active
    }
    
    // Add new section
    $panel->add_section(array(
        'id'          => 'addon_settings',
        'title'       => __('Addon Settings', 'ap-addon'),
        'description' => __('Settings from addon plugin.', 'ap-addon'),
        'icon'        => 'dashicons dashicons-admin-plugins',
        'fields'      => array(
            array(
                'id'          => 'addon_feature',
                'type'        => 'switch',
                'title'       => __('Enable Addon Feature', 'ap-addon'),
                'default'     => '1',
            ),
            array(
                'id'          => 'addon_option',
                'type'        => 'select',
                'title'       => __('Addon Option', 'ap-addon'),
                'default'     => 'option1',
                'options'     => array(
                    'option1' => __('Option One', 'ap-addon'),
                    'option2' => __('Option Two', 'ap-addon'),
                    'option3' => __('Option Three', 'ap-addon'),
                ),
            ),
        ),
    ));
    
    // Add field to existing section
    $panel->add_field('general', array(
        'id'          => 'addon_extra_field',
        'type'        => 'text',
        'title'       => __('Extra Field from Addon', 'ap-addon'),
        'description' => __('Added by addon plugin.', 'ap-addon'),
        'default'     => '',
    ));
});
```

---

## Custom Field Type

```php
<?php
// Register a custom "icon_picker" field type

add_action('bizzplugin_render_field_icon_picker', function($field, $value, $is_disabled) {
    $icons = isset($field['icons']) ? $field['icons'] : array(
        'dashicons-admin-site',
        'dashicons-admin-media',
        'dashicons-admin-links',
        'dashicons-admin-comments',
        'dashicons-admin-users',
        'dashicons-admin-tools',
        'dashicons-admin-settings',
    );
    
    $disabled = $is_disabled ? ' disabled="disabled"' : '';
    ?>
    <div class="icon-picker-wrap">
        <?php foreach ($icons as $icon) : ?>
            <label class="icon-picker-item <?php echo $value === $icon ? 'selected' : ''; ?>">
                <input 
                    type="radio" 
                    name="<?php echo esc_attr($field['id']); ?>"
                    value="<?php echo esc_attr($icon); ?>"
                    <?php checked($value, $icon); ?>
                    <?php echo $disabled; ?>
                />
                <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
            </label>
        <?php endforeach; ?>
    </div>
    <style>
        .icon-picker-wrap { display: flex; flex-wrap: wrap; gap: 10px; }
        .icon-picker-item { cursor: pointer; padding: 10px; border: 2px solid #ddd; border-radius: 4px; }
        .icon-picker-item:hover { border-color: #2271b1; }
        .icon-picker-item.selected { border-color: #2271b1; background: #f0f6fc; }
        .icon-picker-item input { display: none; }
        .icon-picker-item .dashicons { font-size: 24px; width: 24px; height: 24px; }
    </style>
    <?php
}, 10, 3);

// Usage in field definition:
array(
    'id'      => 'selected_icon',
    'type'    => 'icon_picker',
    'title'   => __('Select Icon', 'textdomain'),
    'default' => 'dashicons-admin-site',
    'icons'   => array(
        'dashicons-admin-home',
        'dashicons-admin-site',
        'dashicons-admin-media',
    ),
)
```

---

## Webhook Handler Example

```php
<?php
/**
 * Webhook receiver for BizzPlugin Options Framework
 * Place this file on an external server to receive webhooks
 */

// Configuration
$WEBHOOK_SECRET = 'your-webhook-secret-here';
$LOG_FILE = __DIR__ . '/webhook_log.txt';

// Get request data
$signature = $_SERVER['HTTP_X_BIZZPLUGIN_SIGNATURE'] ?? '';
$payload = file_get_contents('php://input');

// Verify signature
$expected_signature = hash_hmac('sha256', $payload, $WEBHOOK_SECRET);

if (!hash_equals($expected_signature, $signature)) {
    http_response_code(401);
    exit(json_encode(['error' => 'Invalid signature']));
}

// Parse payload
$data = json_decode($payload, true);

// Log the webhook
$log_entry = sprintf(
    "[%s] Event: %s | Option: %s | Changes: %s\n",
    date('Y-m-d H:i:s'),
    $data['event'] ?? 'unknown',
    $data['option_name'] ?? 'unknown',
    json_encode($data['changed_fields'] ?? [])
);
file_put_contents($LOG_FILE, $log_entry, FILE_APPEND);

// Handle specific events
switch ($data['event']) {
    case 'settings_saved':
        // Sync to external service
        sync_to_external_service($data['data']);
        break;
        
    case 'webhook_test':
        // Just log it
        break;
}

// Respond
http_response_code(200);
echo json_encode(['success' => true, 'received' => $data['event']]);

function sync_to_external_service($settings) {
    // Your sync logic here
}
```

---

## Submenu Panel Example

```php
<?php
// Create a panel as a submenu of an existing menu

add_action('init', function() {
    $framework = bizzplugin_framework();
    
    $framework->create_panel(array(
        'id'          => 'my_submenu_panel',
        'title'       => __('My Submenu Settings', 'textdomain'),
        'menu_title'  => __('Sub Settings', 'textdomain'),
        'menu_slug'   => 'my-submenu-settings',
        'parent_slug' => 'options-general.php', // Parent menu slug
        'capability'  => 'manage_options',
        'option_name' => 'my_submenu_options',
        'sections'    => array(/* sections */),
    ));
});
```

Common parent_slug values:
- `index.php` - Dashboard
- `edit.php` - Posts
- `upload.php` - Media
- `edit.php?post_type=page` - Pages
- `edit-comments.php` - Comments
- `themes.php` - Appearance
- `plugins.php` - Plugins
- `users.php` - Users
- `tools.php` - Tools
- `options-general.php` - Settings

---

## Next Steps

- [Field Types](field-types.md) - All field types reference
- [Chainable API](chainable-api.md) - API reference
- [Filters & Hooks](filters-hooks.md) - Extension points
