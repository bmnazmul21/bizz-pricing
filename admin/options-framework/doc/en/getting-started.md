# Getting Started

This guide will help you set up your first settings panel using the BizzPlugin Options Framework.

## Step 1: Include the Framework

First, include the framework in your plugin:

```php
// In your main plugin file
require_once plugin_dir_path(__FILE__) . 'options-framework/options-loader.php';
```

## Step 2: Create a Settings Panel

Create a panel during WordPress initialization:

```php
<?php
/**
 * Plugin Name: My Awesome Plugin
 * Description: Example plugin using BizzPlugin Options Framework
 * Version: 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MY_PLUGIN_VERSION', '1.0.0');
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the options framework
require_once MY_PLUGIN_PATH . 'options-framework/options-loader.php';

/**
 * Initialize settings
 */
add_action('init', function() {
    // Get framework instance
    $framework = bizzplugin_framework();
    
    // Create settings panel
    $panel = $framework->create_panel(array(
        'id'          => 'my_plugin_settings',        // Unique panel ID
        'title'       => __('My Plugin Settings', 'my-plugin'),
        'menu_title'  => __('My Plugin', 'my-plugin'),
        'menu_slug'   => 'my-plugin-settings',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-settings',  // WordPress dashicon
        'position'    => 80,                          // Menu position
        'option_name' => 'my_plugin_options',         // Database option name
        'is_premium'  => false,                       // Premium status
        'sections'    => array(
            array(
                'id'          => 'general',
                'title'       => __('General Settings', 'my-plugin'),
                'description' => __('Configure general settings.', 'my-plugin'),
                'icon'        => 'dashicons dashicons-admin-generic',
                'fields'      => array(
                    array(
                        'id'          => 'site_name',
                        'type'        => 'text',
                        'title'       => __('Site Name', 'my-plugin'),
                        'description' => __('Enter your site name.', 'my-plugin'),
                        'default'     => get_bloginfo('name'),
                        'placeholder' => __('Enter site name...', 'my-plugin'),
                    ),
                    array(
                        'id'          => 'enable_feature',
                        'type'        => 'switch',
                        'title'       => __('Enable Feature', 'my-plugin'),
                        'description' => __('Toggle the main feature.', 'my-plugin'),
                        'default'     => '1',
                    ),
                ),
            ),
        ),
    ));
});
```

## Step 3: Retrieve Saved Options

Use the WordPress `get_option()` function to retrieve your saved settings:

```php
// Get all options
$options = get_option('my_plugin_options', array());

// Get a specific option with default fallback
$site_name = isset($options['site_name']) ? $options['site_name'] : '';
$is_enabled = isset($options['enable_feature']) ? $options['enable_feature'] : '0';

// Helper function (optional)
function my_plugin_get_option($key, $default = '') {
    $options = get_option('my_plugin_options', array());
    return isset($options[$key]) ? $options[$key] : $default;
}

// Usage
$site_name = my_plugin_get_option('site_name', 'Default Site');
```

## Using the Chainable API (Recommended)

The framework supports a modern chainable API for more flexibility:

```php
add_action('init', function() {
    $framework = bizzplugin_framework();
    
    // Create panel with minimal config
    $panel = $framework->create_panel(array(
        'id'          => 'my_plugin_settings',
        'title'       => __('My Plugin Settings', 'my-plugin'),
        'menu_title'  => __('My Plugin', 'my-plugin'),
        'menu_slug'   => 'my-plugin-settings',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-settings',
        'position'    => 80,
        'option_name' => 'my_plugin_options',
    ));
    
    // Configure panel using chainable methods
    $panel
        ->set_logo(MY_PLUGIN_URL . 'assets/logo.png')
        ->set_version(MY_PLUGIN_VERSION)
        ->set_panel_title(__('My Plugin Settings', 'my-plugin'))
        ->set_premium(false)
        ->set_footer_text(__('Powered by My Company', 'my-plugin'));
    
    // Add section
    $panel->add_section(array(
        'id'          => 'general',
        'title'       => __('General Settings', 'my-plugin'),
        'description' => __('Configure general settings.', 'my-plugin'),
        'icon'        => 'dashicons dashicons-admin-generic',
        'fields'      => array(
            array(
                'id'          => 'site_name',
                'type'        => 'text',
                'title'       => __('Site Name', 'my-plugin'),
                'description' => __('Enter your site name.', 'my-plugin'),
                'default'     => get_bloginfo('name'),
            ),
        ),
    ));
    
    // Add more fields to existing section
    $panel->add_field('general', array(
        'id'          => 'site_color',
        'type'        => 'color',
        'title'       => __('Primary Color', 'my-plugin'),
        'description' => __('Select your primary color.', 'my-plugin'),
        'default'     => '#2271b1',
    ));
    
    // Add subsection
    $panel->add_subsection('general', array(
        'id'          => 'advanced',
        'title'       => __('Advanced Options', 'my-plugin'),
        'description' => __('Advanced configuration.', 'my-plugin'),
        'fields'      => array(
            array(
                'id'          => 'debug_mode',
                'type'        => 'checkbox',
                'title'       => __('Debug Mode', 'my-plugin'),
                'description' => __('Enable debug mode.', 'my-plugin'),
                'default'     => '0',
                'label'       => __('Enable debug', 'my-plugin'),
            ),
        ),
    ));
    
    // Add resources for sidebar
    $panel->add_resource(array(
        'icon'  => 'dashicons dashicons-book',
        'title' => __('Documentation', 'my-plugin'),
        'url'   => 'https://example.com/docs',
    ));
    
    // Add recommended plugins
    $panel->add_recommended_plugin(array(
        'slug'        => 'elementor',
        'name'        => 'Elementor',
        'description' => __('The best page builder.', 'my-plugin'),
        'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
        'author'      => 'Elementor.com',
        'file'        => 'elementor/elementor.php',
        'url'         => 'https://wordpress.org/plugins/elementor/',
    ));
});
```

## Complete Basic Example

Here's a complete minimal example:

```php
<?php
/**
 * Plugin Name: My Plugin
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once MY_PLUGIN_PATH . 'options-framework/options-loader.php';

add_action('init', function() {
    $framework = bizzplugin_framework();
    
    $framework->create_panel(array(
        'id'          => 'my_plugin',
        'title'       => 'My Plugin',
        'menu_title'  => 'My Plugin',
        'menu_slug'   => 'my-plugin',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-generic',
        'position'    => 80,
        'option_name' => 'my_plugin_options',
        'sections'    => array(
            array(
                'id'     => 'general',
                'title'  => 'General',
                'icon'   => 'dashicons dashicons-admin-generic',
                'fields' => array(
                    array(
                        'id'      => 'my_text',
                        'type'    => 'text',
                        'title'   => 'My Text Field',
                        'default' => 'Hello World',
                    ),
                ),
            ),
        ),
    ));
});

// Usage in your plugin
function my_plugin_get_text() {
    $options = get_option('my_plugin_options', array());
    return isset($options['my_text']) ? $options['my_text'] : 'Hello World';
}
```

## Next Steps

- [Field Types](field-types.md) - Learn about all available field types
- [Sections & Subsections](sections-subsections.md) - Organize your settings
- [Panel Configuration](panel-configuration.md) - Customize panel branding
- [Chainable API](chainable-api.md) - Master the chainable API
