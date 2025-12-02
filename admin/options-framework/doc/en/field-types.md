# Field Types

This document provides a complete reference of all available field types in the BizzPlugin Options Framework.

## Common Field Properties

All field types support these common properties:

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `id` | string | Yes | Unique identifier for the field |
| `type` | string | Yes | Field type (see types below) |
| `title` | string | Yes | Label displayed for the field |
| `description` | string | No | Help text shown below the field |
| `default` | mixed | No | Default value when no value is saved |
| `class` | string | No | Additional CSS classes |
| `premium` | bool | No | If true, field is locked in free version |
| `dependency` | array | No | Conditional visibility rules |

### Dependency Example

```php
'dependency' => array(
    'field' => 'parent_field_id',  // ID of the field to depend on
    'value' => '1',                 // Value that should trigger visibility
),
```

---

## Text Input Fields

### text

Simple text input field.

```php
array(
    'id'          => 'site_name',
    'type'        => 'text',
    'title'       => __('Site Name', 'textdomain'),
    'description' => __('Enter your site name.', 'textdomain'),
    'default'     => 'My Site',
    'placeholder' => __('Enter text...', 'textdomain'),
)
```

### email

Email input with validation.

```php
array(
    'id'          => 'admin_email',
    'type'        => 'email',
    'title'       => __('Admin Email', 'textdomain'),
    'description' => __('Email for notifications.', 'textdomain'),
    'default'     => get_option('admin_email'),
    'placeholder' => 'admin@example.com',
)
```

### url

URL input with validation.

```php
array(
    'id'          => 'website_url',
    'type'        => 'url',
    'title'       => __('Website URL', 'textdomain'),
    'description' => __('Your website address.', 'textdomain'),
    'default'     => home_url(),
    'placeholder' => 'https://example.com',
)
```

### password

Password input with masked characters.

```php
array(
    'id'          => 'api_key',
    'type'        => 'password',
    'title'       => __('API Key', 'textdomain'),
    'description' => __('Enter your API key.', 'textdomain'),
    'default'     => '',
    'placeholder' => __('Enter API key...', 'textdomain'),
)
```

---

## Number Fields

### number

Number input with optional min/max/step.

```php
array(
    'id'          => 'items_count',
    'type'        => 'number',
    'title'       => __('Items Per Page', 'textdomain'),
    'description' => __('Number of items to display.', 'textdomain'),
    'default'     => 10,
    'min'         => 1,
    'max'         => 100,
    'step'        => 1,
)
```

### slider / range

Visual slider for selecting a value within a range.

```php
array(
    'id'          => 'content_width',
    'type'        => 'slider',  // or 'range'
    'title'       => __('Content Width', 'textdomain'),
    'description' => __('Set content width in pixels.', 'textdomain'),
    'default'     => 1200,
    'min'         => 800,
    'max'         => 1600,
    'step'        => 10,
    'unit'        => 'px',
)
```

---

## Textarea

### textarea

Multi-line text input.

```php
array(
    'id'          => 'custom_css',
    'type'        => 'textarea',
    'title'       => __('Custom CSS', 'textdomain'),
    'description' => __('Add custom CSS code.', 'textdomain'),
    'default'     => '',
    'rows'        => 10,
    'placeholder' => '/* Your CSS here */',
)
```

---

## Selection Fields

### select

Dropdown select field.

```php
array(
    'id'          => 'layout_style',
    'type'        => 'select',
    'title'       => __('Layout Style', 'textdomain'),
    'description' => __('Choose layout style.', 'textdomain'),
    'default'     => 'full-width',
    'options'     => array(
        'full-width' => __('Full Width', 'textdomain'),
        'boxed'      => __('Boxed', 'textdomain'),
        'framed'     => __('Framed', 'textdomain'),
    ),
)
```

### multi_select

Multiple selection dropdown.

```php
array(
    'id'          => 'enabled_features',
    'type'        => 'multi_select',
    'title'       => __('Enabled Features', 'textdomain'),
    'description' => __('Select features to enable.', 'textdomain'),
    'default'     => array('feature_1', 'feature_2'),
    'options'     => array(
        'feature_1' => __('Feature One', 'textdomain'),
        'feature_2' => __('Feature Two', 'textdomain'),
        'feature_3' => __('Feature Three', 'textdomain'),
    ),
)
```

### radio

Radio button selection.

```php
array(
    'id'          => 'display_mode',
    'type'        => 'radio',
    'title'       => __('Display Mode', 'textdomain'),
    'description' => __('Select display mode.', 'textdomain'),
    'default'     => 'grid',
    'options'     => array(
        'grid' => __('Grid', 'textdomain'),
        'list' => __('List', 'textdomain'),
        'masonry' => __('Masonry', 'textdomain'),
    ),
)
```

### image_select

Visual image-based selection (like layout templates).

```php
array(
    'id'          => 'sidebar_layout',
    'type'        => 'image_select',
    'title'       => __('Sidebar Layout', 'textdomain'),
    'description' => __('Select sidebar position.', 'textdomain'),
    'default'     => 'sidebar-right',
    'options'     => array(
        'sidebar-left'  => MY_PLUGIN_URL . 'assets/images/sidebar-left.svg',
        'no-sidebar'    => MY_PLUGIN_URL . 'assets/images/no-sidebar.svg',
        'sidebar-right' => MY_PLUGIN_URL . 'assets/images/sidebar-right.svg',
    ),
)
```

### option_select

Button-style text option selection (similar to image_select but with text).

```php
array(
    'id'          => 'alignment',
    'type'        => 'option_select',
    'title'       => __('Alignment', 'textdomain'),
    'description' => __('Choose alignment.', 'textdomain'),
    'default'     => 'center',
    'options'     => array(
        'left'   => __('Left', 'textdomain'),
        'center' => __('Center', 'textdomain'),
        'right'  => __('Right', 'textdomain'),
    ),
)
```

### post_select

Select posts/pages/custom post types.

```php
// Single post selection
array(
    'id'          => 'featured_post',
    'type'        => 'post_select',
    'title'       => __('Featured Post', 'textdomain'),
    'description' => __('Select a post to feature.', 'textdomain'),
    'post_type'   => 'post',
    'default'     => '',
)

// Multiple page selection
array(
    'id'          => 'excluded_pages',
    'type'        => 'post_select',
    'title'       => __('Excluded Pages', 'textdomain'),
    'description' => __('Select pages to exclude.', 'textdomain'),
    'post_type'   => 'page',
    'multiple'    => true,
    'default'     => array(),
)
```

---

## Toggle/Checkbox Fields

### checkbox

Single checkbox with label.

```php
array(
    'id'          => 'enable_cache',
    'type'        => 'checkbox',
    'title'       => __('Enable Cache', 'textdomain'),
    'description' => __('Enable caching for better performance.', 'textdomain'),
    'default'     => '1',
    'label'       => __('Yes, enable caching', 'textdomain'),
)
```

### checkbox_group

Multiple checkboxes.

```php
array(
    'id'          => 'enabled_modules',
    'type'        => 'checkbox_group',
    'title'       => __('Enabled Modules', 'textdomain'),
    'description' => __('Select modules to enable.', 'textdomain'),
    'default'     => array('module_1', 'module_2'),
    'options'     => array(
        'module_1' => __('Module One', 'textdomain'),
        'module_2' => __('Module Two', 'textdomain'),
        'module_3' => __('Module Three', 'textdomain'),
        'module_4' => __('Module Four', 'textdomain'),
    ),
)
```

### switch / on_off

Toggle switch control.

```php
array(
    'id'          => 'maintenance_mode',
    'type'        => 'switch',  // or 'on_off'
    'title'       => __('Maintenance Mode', 'textdomain'),
    'description' => __('Enable maintenance mode.', 'textdomain'),
    'default'     => '0',
    'on_label'    => __('Enabled', 'textdomain'),
    'off_label'   => __('Disabled', 'textdomain'),
)
```

---

## Color & Date Fields

### color

Color picker field.

```php
array(
    'id'          => 'primary_color',
    'type'        => 'color',
    'title'       => __('Primary Color', 'textdomain'),
    'description' => __('Select primary color.', 'textdomain'),
    'default'     => '#2271b1',
)
```

### date

Date picker field.

```php
array(
    'id'          => 'start_date',
    'type'        => 'date',
    'title'       => __('Start Date', 'textdomain'),
    'description' => __('Select start date.', 'textdomain'),
    'default'     => '',
    'placeholder' => __('Select date...', 'textdomain'),
)
```

---

## Media Fields

### image

Image upload field using WordPress media library.

```php
array(
    'id'          => 'logo_image',
    'type'        => 'image',
    'title'       => __('Logo Image', 'textdomain'),
    'description' => __('Upload your logo.', 'textdomain'),
    'default'     => '',
)
```

**Note**: The saved value is the attachment ID, not the URL.

### file

File upload field.

```php
array(
    'id'          => 'download_file',
    'type'        => 'file',
    'title'       => __('Download File', 'textdomain'),
    'description' => __('Upload a file for download.', 'textdomain'),
    'default'     => '',
)
```

**Note**: The saved value is the attachment ID.

---

## Content/Display Fields

### html

Custom HTML content (non-saveable).

```php
array(
    'id'      => 'welcome_message',
    'type'    => 'html',
    'title'   => __('Welcome', 'textdomain'),
    'content' => '<div class="notice">
        <p><strong>Welcome!</strong></p>
        <p>Thank you for using our plugin.</p>
    </div>',
)
```

### info

Information display field (alias for simple notices).

```php
array(
    'id'          => 'info_message',
    'type'        => 'info',
    'title'       => __('Information', 'textdomain'),
    'description' => __('This is an informational message.', 'textdomain'),
)
```

### link

Link display field.

```php
array(
    'id'          => 'docs_link',
    'type'        => 'link',
    'title'       => __('Documentation', 'textdomain'),
    'description' => __('Visit our <a href="https://example.com/docs" target="_blank">documentation</a>.', 'textdomain'),
)
```

### plugins

Recommended plugins display with install/activate buttons.

```php
array(
    'id'      => 'recommended_plugins',
    'type'    => 'plugins',
    'title'   => __('Recommended Plugins', 'textdomain'),
    'plugins' => array(
        array(
            'slug'        => 'elementor',
            'name'        => 'Elementor',
            'description' => __('Page builder plugin.', 'textdomain'),
            'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
            'author'      => 'Elementor.com',
            'file'        => 'elementor/elementor.php',
            'url'         => 'https://wordpress.org/plugins/elementor/',
        ),
    ),
)
```

### callback

Custom callback for rendering.

```php
array(
    'id'              => 'custom_field',
    'type'            => 'callback',
    'title'           => __('Custom Field', 'textdomain'),
    'render_callback' => 'my_custom_render_function',
)

function my_custom_render_function($field, $value, $disabled) {
    echo '<div class="custom-field">';
    echo '<input type="text" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '">';
    echo '</div>';
}
```

---

## Field Dependency

Any field can be conditionally shown based on another field's value:

```php
array(
    'id'          => 'debug_mode',
    'type'        => 'switch',
    'title'       => __('Debug Mode', 'textdomain'),
    'default'     => '0',
),
array(
    'id'          => 'log_level',
    'type'        => 'select',
    'title'       => __('Log Level', 'textdomain'),
    'default'     => 'error',
    'options'     => array(
        'error'   => __('Errors Only', 'textdomain'),
        'warning' => __('Warnings', 'textdomain'),
        'info'    => __('All Info', 'textdomain'),
    ),
    'dependency'  => array(
        'field' => 'debug_mode',
        'value' => '1',
    ),
)
```

The `log_level` field will only appear when `debug_mode` is enabled.

---

## Premium/Locked Fields

Mark fields as premium to lock them in the free version:

```php
array(
    'id'          => 'advanced_feature',
    'type'        => 'switch',
    'title'       => __('Advanced Feature', 'textdomain'),
    'description' => __('Available in premium version.', 'textdomain'),
    'default'     => '0',
    'premium'     => true,  // This field will be locked
)
```

Set `is_premium` to `true` in your panel configuration to unlock premium fields.

---

## Next Steps

- [Sections & Subsections](sections-subsections.md) - Organize your fields
- [Filters & Hooks](filters-hooks.md) - Add custom field types
- [Examples](examples.md) - Complete code examples
