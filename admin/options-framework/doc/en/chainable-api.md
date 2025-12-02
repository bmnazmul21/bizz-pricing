# Chainable API

This document provides a complete reference for the chainable API in the BizzPlugin Options Framework.

## Introduction

The chainable API provides a fluent, modern interface for building settings panels. All chainable methods return `$this`, allowing you to chain multiple calls together.

```php
$panel
    ->set_logo(MY_PLUGIN_URL . 'assets/logo.png')
    ->set_version('1.0.0')
    ->set_premium(false)
    ->add_section(array(/* section config */))
    ->add_field('section_id', array(/* field config */))
    ->add_resource(array(/* resource config */));
```

## Panel Configuration Methods

### set_panel_config($config)

Set multiple configuration values at once.

```php
$panel->set_panel_config(array(
    'title'               => 'My Plugin',
    'logo'                => 'https://example.com/logo.png',
    'version'             => '1.0.0',
    'is_premium'          => false,
    'footer_text'         => 'Powered by BizzPlugin',
    'resources'           => array(),
    'recommended_plugins' => array(),
));
```

**Returns**: `$this` (for chaining)

### set_config($key, $value)

Set a single configuration value.

```php
$panel->set_config('logo', 'https://example.com/logo.png');
$panel->set_config('version', '1.0.0');
```

**Returns**: `$this` (for chaining)

### set_logo($logo_url)

Set the panel logo.

```php
$panel->set_logo(MY_PLUGIN_URL . 'assets/logo.png');
```

**Returns**: `$this` (for chaining)

### set_version($version)

Set the version number.

```php
$panel->set_version('1.0.0');
// Or use a constant
$panel->set_version(MY_PLUGIN_VERSION);
```

**Returns**: `$this` (for chaining)

### set_panel_title($title)

Set the panel title (displayed in header).

```php
$panel->set_panel_title(__('My Plugin Settings', 'textdomain'));
```

**Returns**: `$this` (for chaining)

### set_premium($is_premium)

Set premium status. This affects:
- Premium/Free badge display
- Whether premium-marked fields are locked

```php
$panel->set_premium(true);  // Premium
$panel->set_premium(false); // Free
```

**Returns**: `$this` (for chaining)

### set_footer_text($text)

Set the footer text.

```php
$panel->set_footer_text(__('Powered by My Company', 'textdomain'));
```

**Returns**: `$this` (for chaining)

### get_panel_config($key = null)

Get panel configuration.

```php
// Get all config
$config = $panel->get_panel_config();

// Get specific key
$logo = $panel->get_panel_config('logo');
```

**Returns**: `array` (all config) or `mixed` (specific key value)

## Section Methods

### add_section($args)

Add a new section to the panel.

```php
$panel->add_section(array(
    'id'                => 'general',
    'title'             => __('General Settings', 'textdomain'),
    'description'       => __('Configure general options.', 'textdomain'),
    'icon'              => 'dashicons dashicons-admin-generic',
    'hide_reset_button' => false,
    'fields'            => array(
        array(
            'id'      => 'site_name',
            'type'    => 'text',
            'title'   => __('Site Name', 'textdomain'),
            'default' => '',
        ),
    ),
    'subsections'       => array(
        array(
            'id'     => 'advanced',
            'title'  => __('Advanced', 'textdomain'),
            'fields' => array(/* fields */),
        ),
    ),
));
```

**Parameters**:
- `id` (string, required): Unique section ID
- `title` (string, required): Section display title
- `description` (string, optional): Section description
- `icon` (string, optional): WordPress dashicon class
- `fields` (array, optional): Array of field configurations
- `subsections` (array, optional): Array of subsection configurations
- `hide_reset_button` (bool, optional): Hide reset button for this section

**Returns**: `$this` (for chaining)

### get_sections()

Get all sections.

```php
$sections = $panel->get_sections();
```

**Returns**: `array` of sections

### get_section($section_id)

Get a specific section by ID.

```php
$section = $panel->get_section('general');
```

**Returns**: `array|null` section configuration or null if not found

### remove_section($section_id)

Remove a section by ID.

```php
$panel->remove_section('deprecated_section');
```

**Returns**: `$this` (for chaining)

## Field Methods

### add_field($section_id, $field)

Add a field to an existing section.

```php
$panel->add_field('general', array(
    'id'          => 'email',
    'type'        => 'email',
    'title'       => __('Email Address', 'textdomain'),
    'description' => __('Enter email address.', 'textdomain'),
    'default'     => '',
    'placeholder' => 'admin@example.com',
));
```

**Parameters**:
- `$section_id` (string): ID of the section to add field to
- `$field` (array): Field configuration array

**Returns**: `$this` (for chaining)

### remove_field($section_id, $field_id)

Remove a field from a section.

```php
$panel->remove_field('general', 'old_field');
```

**Returns**: `$this` (for chaining)

### get_all_fields()

Get all fields from all sections.

```php
$all_fields = $panel->get_all_fields();
```

**Returns**: `array` of all fields indexed by field ID

## Subsection Methods

### add_subsection($section_id, $subsection)

Add a subsection to an existing section.

```php
$panel->add_subsection('general', array(
    'id'          => 'advanced',
    'title'       => __('Advanced Options', 'textdomain'),
    'description' => __('Advanced configuration.', 'textdomain'),
    'fields'      => array(
        array(
            'id'      => 'debug_mode',
            'type'    => 'checkbox',
            'title'   => __('Debug Mode', 'textdomain'),
            'default' => '0',
            'label'   => __('Enable debug', 'textdomain'),
        ),
    ),
));
```

**Returns**: `$this` (for chaining)

### add_subsection_field($section_id, $subsection_id, $field)

Add a field to a subsection.

```php
$panel->add_subsection_field('general', 'advanced', array(
    'id'      => 'log_level',
    'type'    => 'select',
    'title'   => __('Log Level', 'textdomain'),
    'default' => 'error',
    'options' => array(
        'error'   => __('Errors', 'textdomain'),
        'warning' => __('Warnings', 'textdomain'),
        'info'    => __('Info', 'textdomain'),
    ),
));
```

**Returns**: `$this` (for chaining)

### remove_subsection($section_id, $subsection_id)

Remove a subsection from a section.

```php
$panel->remove_subsection('general', 'old_subsection');
```

**Returns**: `$this` (for chaining)

## Resource Methods

### set_resources($resources)

Set all resource links (replaces existing).

```php
$panel->set_resources(array(
    array(
        'icon'  => 'dashicons dashicons-book',
        'title' => __('Documentation', 'textdomain'),
        'url'   => 'https://example.com/docs',
    ),
    array(
        'icon'  => 'dashicons dashicons-sos',
        'title' => __('Support', 'textdomain'),
        'url'   => 'https://example.com/support',
    ),
));
```

**Returns**: `$this` (for chaining)

### add_resource($resource)

Add a single resource link.

```php
$panel->add_resource(array(
    'icon'  => 'dashicons dashicons-star-filled',
    'title' => __('Rate Plugin', 'textdomain'),
    'url'   => 'https://wordpress.org/plugins/my-plugin/reviews/',
));
```

**Returns**: `$this` (for chaining)

### get_resources()

Get all resource links.

```php
$resources = $panel->get_resources();
```

**Returns**: `array` of resources

## Recommended Plugin Methods

### set_recommended_plugins($plugins)

Set all recommended plugins (replaces existing).

```php
$panel->set_recommended_plugins(array(
    array(
        'slug'        => 'elementor',
        'name'        => 'Elementor',
        'description' => __('Page builder.', 'textdomain'),
        'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
        'author'      => 'Elementor.com',
        'file'        => 'elementor/elementor.php',
        'url'         => 'https://wordpress.org/plugins/elementor/',
    ),
));
```

**Returns**: `$this` (for chaining)

### add_recommended_plugin($plugin)

Add a single recommended plugin.

```php
$panel->add_recommended_plugin(array(
    'slug'        => 'woocommerce',
    'name'        => 'WooCommerce',
    'description' => __('eCommerce platform.', 'textdomain'),
    'thumbnail'   => 'https://ps.w.org/woocommerce/assets/icon-256x256.png',
    'author'      => 'Automattic',
    'file'        => 'woocommerce/woocommerce.php',
    'url'         => 'https://wordpress.org/plugins/woocommerce/',
));
```

**Returns**: `$this` (for chaining)

### get_recommended_plugins()

Get all recommended plugins.

```php
$plugins = $panel->get_recommended_plugins();
```

**Returns**: `array` of plugins

## Utility Methods

### get_id()

Get the panel ID.

```php
$panel_id = $panel->get_id();
```

**Returns**: `string`

### get_option_name()

Get the option name (database key).

```php
$option_name = $panel->get_option_name();
```

**Returns**: `string`

### is_premium()

Check if panel is in premium mode.

```php
if ($panel->is_premium()) {
    // Premium mode logic
}
```

**Returns**: `bool`

### get_args()

Get all panel arguments.

```php
$args = $panel->get_args();
// Returns: id, title, menu_title, menu_slug, parent_slug, capability, icon, position, option_name, is_premium
```

**Returns**: `array`

### get_all_defaults()

Get default values for all fields.

```php
$defaults = $panel->get_all_defaults();
```

**Returns**: `array` of field IDs => default values

### get_section_defaults($section_id)

Get default values for a specific section.

```php
$section_defaults = $panel->get_section_defaults('general');
```

**Returns**: `array` of field IDs => default values

## Complete Chainable Example

```php
add_action('init', function() {
    $framework = bizzplugin_framework();
    
    $framework->create_panel(array(
        'id'          => 'my_plugin',
        'title'       => __('My Plugin', 'textdomain'),
        'menu_title'  => __('My Plugin', 'textdomain'),
        'menu_slug'   => 'my-plugin',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-settings',
        'position'    => 80,
        'option_name' => 'my_plugin_options',
    ))
    // Panel configuration
    ->set_logo(MY_PLUGIN_URL . 'assets/logo.png')
    ->set_version('1.0.0')
    ->set_panel_title(__('My Plugin Settings', 'textdomain'))
    ->set_premium(false)
    ->set_footer_text(__('Powered by BizzPlugin', 'textdomain'))
    
    // Add main section
    ->add_section(array(
        'id'     => 'general',
        'title'  => __('General', 'textdomain'),
        'icon'   => 'dashicons dashicons-admin-generic',
        'fields' => array(
            array(
                'id'      => 'site_name',
                'type'    => 'text',
                'title'   => __('Site Name', 'textdomain'),
                'default' => get_bloginfo('name'),
            ),
        ),
    ))
    
    // Add more fields to section
    ->add_field('general', array(
        'id'      => 'enable_feature',
        'type'    => 'switch',
        'title'   => __('Enable Feature', 'textdomain'),
        'default' => '1',
    ))
    
    // Add subsection
    ->add_subsection('general', array(
        'id'     => 'advanced',
        'title'  => __('Advanced', 'textdomain'),
        'fields' => array(
            array(
                'id'      => 'debug_mode',
                'type'    => 'checkbox',
                'title'   => __('Debug Mode', 'textdomain'),
                'default' => '0',
            ),
        ),
    ))
    
    // Add field to subsection
    ->add_subsection_field('general', 'advanced', array(
        'id'      => 'cache_time',
        'type'    => 'number',
        'title'   => __('Cache Time', 'textdomain'),
        'default' => 3600,
        'min'     => 0,
        'max'     => 86400,
    ))
    
    // Add another section
    ->add_section(array(
        'id'     => 'appearance',
        'title'  => __('Appearance', 'textdomain'),
        'icon'   => 'dashicons dashicons-admin-appearance',
        'fields' => array(
            array(
                'id'      => 'primary_color',
                'type'    => 'color',
                'title'   => __('Primary Color', 'textdomain'),
                'default' => '#2271b1',
            ),
        ),
    ))
    
    // Add resources
    ->add_resource(array(
        'icon'  => 'dashicons dashicons-book',
        'title' => __('Documentation', 'textdomain'),
        'url'   => 'https://example.com/docs',
    ))
    ->add_resource(array(
        'icon'  => 'dashicons dashicons-sos',
        'title' => __('Support', 'textdomain'),
        'url'   => 'https://example.com/support',
    ))
    
    // Add recommended plugin
    ->add_recommended_plugin(array(
        'slug'        => 'elementor',
        'name'        => 'Elementor',
        'description' => __('Page builder.', 'textdomain'),
        'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
        'author'      => 'Elementor.com',
        'file'        => 'elementor/elementor.php',
        'url'         => 'https://wordpress.org/plugins/elementor/',
    ));
});
```

---

## Next Steps

- [Filters & Hooks](filters-hooks.md) - Extend via filters
- [Panel Configuration](panel-configuration.md) - Configuration details
- [Examples](examples.md) - More code examples
