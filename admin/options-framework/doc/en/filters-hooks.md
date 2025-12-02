# Filters & Hooks

This document provides a complete reference of all available filters and action hooks in the BizzPlugin Options Framework.

## Action Hooks

### Framework Initialization

#### bizzplugin_framework_loaded

Fired when the framework is fully loaded and ready.

```php
add_action('bizzplugin_framework_loaded', function($framework) {
    // Framework is ready to use
    // $framework is the BizzPlugin_Framework instance
});
```

#### bizzplugin_panel_created

Fired when a panel is created.

```php
add_action('bizzplugin_panel_created', function($panel, $panel_id) {
    // Panel has been created
    // Use this to add fields/sections from other plugins
    if ($panel_id === 'target_panel') {
        $panel->add_field('general', array(
            'id'      => 'extra_field',
            'type'    => 'text',
            'title'   => __('Extra Field', 'textdomain'),
            'default' => '',
        ));
    }
}, 10, 2);
```

### Options Save

#### bizzplugin_options_saved

Fired after options are saved via AJAX.

```php
add_action('bizzplugin_options_saved', function($option_name, $new_options, $old_options, $panel_id) {
    // Options have been saved
    // Compare old and new values, trigger side effects, etc.
    
    if (isset($new_options['cache_enabled']) && $new_options['cache_enabled'] !== $old_options['cache_enabled']) {
        // Cache setting changed - clear cache
        my_plugin_clear_cache();
    }
}, 10, 4);
```

### Navigation Hooks

#### bizzplugin_nav_before_menu

Add content before the navigation menu.

```php
add_action('bizzplugin_nav_before_menu', function($panel_id) {
    if ($panel_id === 'my_plugin') {
        echo '<div class="custom-nav-header">Custom Content</div>';
    }
});
```

#### bizzplugin_nav_after_menu

Add content after the navigation menu.

```php
add_action('bizzplugin_nav_after_menu', function($panel_id) {
    if ($panel_id === 'my_plugin') {
        echo '<div class="custom-nav-footer">Additional Links</div>';
    }
});
```

### Sidebar Hooks

#### bizzplugin_sidebar_top

Add content at the top of the sidebar.

```php
add_action('bizzplugin_sidebar_top', function($panel_id) {
    echo '<div class="custom-sidebar-widget">Top Widget</div>';
});
```

#### bizzplugin_sidebar_middle

Add content in the middle of the sidebar.

```php
add_action('bizzplugin_sidebar_middle', function($panel_id) {
    echo '<div class="custom-sidebar-widget">Middle Widget</div>';
});
```

#### bizzplugin_sidebar_bottom

Add content at the bottom of the sidebar.

```php
add_action('bizzplugin_sidebar_bottom', function($panel_id) {
    echo '<div class="custom-sidebar-widget">Bottom Widget</div>';
});
```

### Custom Field Rendering

#### bizzplugin_render_field_{type}

Render a custom field type.

```php
// Register a custom field type
add_action('bizzplugin_render_field_my_custom', function($field, $value, $is_disabled) {
    $disabled = $is_disabled ? ' disabled="disabled"' : '';
    ?>
    <div class="my-custom-field-wrapper">
        <input 
            type="text" 
            id="<?php echo esc_attr($field['id']); ?>"
            name="<?php echo esc_attr($field['id']); ?>"
            value="<?php echo esc_attr($value); ?>"
            class="my-custom-input"
            <?php echo $disabled; ?>
        />
        <span class="my-custom-suffix"><?php echo esc_html($field['suffix'] ?? ''); ?></span>
    </div>
    <?php
}, 10, 3);

// Usage
array(
    'id'      => 'my_field',
    'type'    => 'my_custom',  // Matches action name
    'title'   => 'My Field',
    'default' => '',
    'suffix'  => 'units',
)
```

#### bizzplugin_render_custom_field

Fallback for custom field types not handled elsewhere.

```php
add_action('bizzplugin_render_custom_field', function($field, $value, $is_disabled) {
    $field_type = $field['type'];
    
    if ($field_type === 'special_field') {
        // Render special field
    }
}, 10, 3);
```

### Custom Panel Rendering

#### bizzplugin_render_panel_{panel_id}

Completely replace the panel rendering for a specific panel.

```php
add_action('bizzplugin_render_panel_my_plugin', function($panel, $options, $sections, $current_section, $current_subsection) {
    // Completely custom panel rendering
    ?>
    <div class="my-custom-panel">
        <h1>Custom Panel</h1>
        <!-- Your custom HTML -->
    </div>
    <?php
}, 10, 5);
```

---

## Filter Hooks

### Panel Configuration Filters

#### bizzplugin_panel_config_{panel_id} (Recommended)

Modify panel configuration for a specific panel. This is the recommended approach for panel-specific modifications.

```php
add_filter('bizzplugin_panel_config_my_plugin', function($config, $panel_id) {
    // Modify panel config
    $config['logo'] = MY_PLUGIN_URL . 'assets/logo.png';
    $config['version'] = '2.0.0';
    $config['is_premium'] = license_is_valid();
    
    // Add resources
    $config['resources'][] = array(
        'icon'  => 'dashicons dashicons-video-alt3',
        'title' => __('Video Tutorials', 'textdomain'),
        'url'   => 'https://example.com/videos',
    );
    
    return $config;
}, 10, 2);
```

### Section Filters

#### bizzplugin_panel_sections_{panel_id}

Modify sections for a specific panel.

```php
add_filter('bizzplugin_panel_sections_my_plugin', function($sections, $panel_id) {
    // Add a new section
    $sections['my_addon_section'] = array(
        'id'     => 'my_addon_section',
        'title'  => __('Addon Settings', 'textdomain'),
        'icon'   => 'dashicons dashicons-admin-plugins',
        'fields' => array(
            array(
                'id'      => 'addon_enabled',
                'type'    => 'switch',
                'title'   => __('Enable Addon', 'textdomain'),
                'default' => '0',
            ),
        ),
    );
    
    return $sections;
}, 10, 2);
```

### Field Filters

#### bizzplugin_section_fields_{panel_id}

Modify fields for a specific section within a specific panel.

```php
add_filter('bizzplugin_section_fields_my_plugin', function($fields, $section_id, $panel_id) {
    if ($section_id === 'general') {
        // Add field to general section
        $fields[] = array(
            'id'      => 'injected_field',
            'type'    => 'text',
            'title'   => __('Injected Field', 'textdomain'),
            'default' => '',
        );
    }
    
    return $fields;
}, 10, 3);
```

#### bizzplugin_section_fields (Generic)

Generic filter for all panels (not recommended - use panel-specific filters).

```php
add_filter('bizzplugin_section_fields', function($fields, $section_id, $panel_id) {
    // Only modify if it's our panel
    if ($panel_id !== 'my_plugin') {
        return $fields;
    }
    
    // Modify fields
    return $fields;
}, 10, 3);
```

### Premium Status Filters

#### bizzplugin_is_premium_{panel_id}

Control premium status for a specific panel.

```php
add_filter('bizzplugin_is_premium_my_plugin', function($is_premium, $panel_id) {
    // Check license
    return my_plugin_license_is_valid();
}, 10, 2);
```

---

## Filter Naming Conventions

The framework uses panel-specific filters with the pattern:

```
{filter_name}_{panel_id}
```

This allows multiple plugins using the framework to coexist without conflicts.

| Generic Filter | Panel-Specific Filter |
|---------------|----------------------|
| `bizzplugin_panel_config` | `bizzplugin_panel_config_{panel_id}` |
| `bizzplugin_panel_sections` | `bizzplugin_panel_sections_{panel_id}` |
| `bizzplugin_section_fields` | `bizzplugin_section_fields_{panel_id}` |
| `bizzplugin_is_premium` | `bizzplugin_is_premium_{panel_id}` |

**Always prefer panel-specific filters** to avoid affecting other plugins.

---

## Extending with Addon Plugins

### Example: Adding Fields from an Addon

```php
<?php
/**
 * Plugin Name: My Plugin Addon
 */

// Wait for framework to be ready
add_action('bizzplugin_framework_loaded', function($framework) {
    // Get the main plugin's panel
    $panel = $framework->get_panel('my_main_plugin');
    
    if ($panel) {
        // Add addon section using chainable API
        $panel->add_section(array(
            'id'     => 'addon_settings',
            'title'  => __('Addon Settings', 'my-addon'),
            'icon'   => 'dashicons dashicons-admin-plugins',
            'fields' => array(
                array(
                    'id'      => 'addon_feature',
                    'type'    => 'switch',
                    'title'   => __('Addon Feature', 'my-addon'),
                    'default' => '0',
                ),
            ),
        ));
    }
});
```

### Example: Modifying Existing Fields

```php
// Use filter to modify fields
add_filter('bizzplugin_section_fields_my_plugin', function($fields, $section_id, $panel_id) {
    if ($section_id === 'general') {
        // Modify existing field
        foreach ($fields as &$field) {
            if ($field['id'] === 'existing_field') {
                $field['description'] .= ' ' . __('(Modified by addon)', 'my-addon');
            }
        }
        
        // Add new field after existing one
        $new_fields = array();
        foreach ($fields as $field) {
            $new_fields[] = $field;
            if ($field['id'] === 'existing_field') {
                $new_fields[] = array(
                    'id'      => 'addon_related_field',
                    'type'    => 'text',
                    'title'   => __('Related Field', 'my-addon'),
                    'default' => '',
                );
            }
        }
        $fields = $new_fields;
    }
    
    return $fields;
}, 10, 3);
```

### Example: Custom Field Type

```php
// Register custom field type
add_action('bizzplugin_render_field_icon_picker', function($field, $value, $is_disabled) {
    $icons = array(
        'dashicons-admin-site',
        'dashicons-admin-media',
        'dashicons-admin-links',
        'dashicons-admin-comments',
    );
    ?>
    <div class="icon-picker-field">
        <?php foreach ($icons as $icon) : ?>
            <label class="icon-option <?php echo $value === $icon ? 'selected' : ''; ?>">
                <input 
                    type="radio" 
                    name="<?php echo esc_attr($field['id']); ?>"
                    value="<?php echo esc_attr($icon); ?>"
                    <?php checked($value, $icon); ?>
                    <?php disabled($is_disabled, true); ?>
                />
                <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
            </label>
        <?php endforeach; ?>
    </div>
    <?php
}, 10, 3);
```

---

## Best Practices

1. **Always use panel-specific filters** to avoid conflicts with other plugins
2. **Check panel_id** when using generic filters
3. **Use action hooks** for side effects (logging, cache clearing, etc.)
4. **Use filters** for modifying data (configurations, fields, etc.)
5. **Document your hooks** when creating plugins that extend others
6. **Test thoroughly** when modifying existing fields
7. **Maintain compatibility** with both free and premium versions

---

## Next Steps

- [REST API](api.md) - API integration
- [Webhooks](webhooks.md) - Webhook configuration
- [Examples](examples.md) - More code examples
