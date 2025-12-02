# Sections & Subsections

This document explains how to organize your settings using sections and subsections.

## Understanding the Structure

The BizzPlugin Options Framework uses a hierarchical structure:

```
Panel
├── Section 1
│   ├── Fields
│   └── Subsections
│       ├── Subsection 1.1
│       │   └── Fields
│       └── Subsection 1.2
│           └── Fields
├── Section 2
│   └── Fields
└── Section 3
    └── Subsections
        └── Subsection 3.1
            └── Fields
```

## Creating Sections

Sections are the top-level organization for your settings. Each section appears as a tab in the navigation sidebar.

### Section Properties

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `id` | string | Yes | Unique identifier for the section |
| `title` | string | Yes | Display title in navigation |
| `description` | string | No | Description shown at top of section content |
| `icon` | string | No | WordPress dashicon class |
| `fields` | array | No | Array of field configurations |
| `subsections` | array | No | Array of subsection configurations |
| `hide_reset_button` | bool | No | If true, hides the "Reset Section" button |

### Basic Section Example

```php
$sections = array(
    array(
        'id'          => 'general',
        'title'       => __('General Settings', 'textdomain'),
        'description' => __('Configure general plugin settings.', 'textdomain'),
        'icon'        => 'dashicons dashicons-admin-generic',
        'fields'      => array(
            array(
                'id'      => 'site_name',
                'type'    => 'text',
                'title'   => __('Site Name', 'textdomain'),
                'default' => '',
            ),
            array(
                'id'      => 'enable_feature',
                'type'    => 'switch',
                'title'   => __('Enable Feature', 'textdomain'),
                'default' => '1',
            ),
        ),
    ),
    array(
        'id'          => 'appearance',
        'title'       => __('Appearance', 'textdomain'),
        'description' => __('Customize visual settings.', 'textdomain'),
        'icon'        => 'dashicons dashicons-admin-appearance',
        'fields'      => array(
            array(
                'id'      => 'primary_color',
                'type'    => 'color',
                'title'   => __('Primary Color', 'textdomain'),
                'default' => '#2271b1',
            ),
        ),
    ),
);
```

## Creating Subsections

Subsections allow you to further organize fields within a section. When a section has subsections, they appear as nested items in the navigation.

### Subsection Properties

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `id` | string | Yes | Unique identifier for the subsection |
| `title` | string | Yes | Display title in navigation |
| `description` | string | No | Description shown at top of subsection content |
| `icon` | string | No | WordPress dashicon class |
| `fields` | array | Yes | Array of field configurations |
| `hide_reset_button` | bool | No | If true, hides the "Reset Section" button |

### Section with Subsections

```php
array(
    'id'          => 'basic',
    'title'       => __('Basic Settings', 'textdomain'),
    'description' => __('Basic configuration options.', 'textdomain'),
    'icon'        => 'dashicons dashicons-admin-settings',
    'fields'      => array(
        // Main section fields (shown when section is clicked)
        array(
            'id'      => 'main_field',
            'type'    => 'text',
            'title'   => __('Main Field', 'textdomain'),
            'default' => '',
        ),
    ),
    'subsections' => array(
        array(
            'id'          => 'text_settings',
            'title'       => __('Text Settings', 'textdomain'),
            'description' => __('Configure text options.', 'textdomain'),
            'fields'      => array(
                array(
                    'id'      => 'font_size',
                    'type'    => 'number',
                    'title'   => __('Font Size', 'textdomain'),
                    'default' => 16,
                    'min'     => 10,
                    'max'     => 30,
                ),
                array(
                    'id'      => 'font_family',
                    'type'    => 'select',
                    'title'   => __('Font Family', 'textdomain'),
                    'default' => 'system',
                    'options' => array(
                        'system'   => __('System Default', 'textdomain'),
                        'roboto'   => 'Roboto',
                        'open-sans' => 'Open Sans',
                    ),
                ),
            ),
        ),
        array(
            'id'          => 'color_settings',
            'title'       => __('Color Settings', 'textdomain'),
            'description' => __('Configure color options.', 'textdomain'),
            'fields'      => array(
                array(
                    'id'      => 'text_color',
                    'type'    => 'color',
                    'title'   => __('Text Color', 'textdomain'),
                    'default' => '#333333',
                ),
                array(
                    'id'      => 'bg_color',
                    'type'    => 'color',
                    'title'   => __('Background Color', 'textdomain'),
                    'default' => '#ffffff',
                ),
            ),
        ),
    ),
),
```

## Using the Chainable API

You can also use the chainable API to build sections and subsections dynamically:

### Add Section

```php
$panel->add_section(array(
    'id'          => 'general',
    'title'       => __('General', 'textdomain'),
    'description' => __('General settings.', 'textdomain'),
    'icon'        => 'dashicons dashicons-admin-generic',
    'fields'      => array(
        array(
            'id'      => 'site_name',
            'type'    => 'text',
            'title'   => __('Site Name', 'textdomain'),
            'default' => '',
        ),
    ),
));
```

### Add Field to Existing Section

```php
$panel->add_field('general', array(
    'id'      => 'site_email',
    'type'    => 'email',
    'title'   => __('Site Email', 'textdomain'),
    'default' => '',
));
```

### Add Subsection to Existing Section

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

### Add Field to Subsection

```php
$panel->add_subsection_field('general', 'advanced', array(
    'id'      => 'log_level',
    'type'    => 'select',
    'title'   => __('Log Level', 'textdomain'),
    'default' => 'error',
    'options' => array(
        'error'   => __('Errors Only', 'textdomain'),
        'warning' => __('Warnings', 'textdomain'),
        'info'    => __('All Info', 'textdomain'),
    ),
));
```

## Removing Sections

```php
// Remove entire section
$panel->remove_section('section_id');

// Remove field from section
$panel->remove_field('section_id', 'field_id');

// Remove subsection from section
$panel->remove_subsection('section_id', 'subsection_id');
```

## Section-Only vs Fields-Only

### Section with Only Fields (No Subsections)

When a section has only fields, clicking on it displays those fields directly.

```php
array(
    'id'     => 'simple_section',
    'title'  => __('Simple', 'textdomain'),
    'icon'   => 'dashicons dashicons-admin-generic',
    'fields' => array(
        // Fields here
    ),
    // No 'subsections' key
)
```

### Section with Only Subsections (No Main Fields)

When a section has only subsections, clicking on it expands to show subsections.

```php
array(
    'id'          => 'complex_section',
    'title'       => __('Complex', 'textdomain'),
    'icon'        => 'dashicons dashicons-admin-settings',
    // No 'fields' key or empty array
    'subsections' => array(
        array(
            'id'     => 'subsection_1',
            'title'  => __('Sub 1', 'textdomain'),
            'fields' => array(/* Fields */),
        ),
        array(
            'id'     => 'subsection_2',
            'title'  => __('Sub 2', 'textdomain'),
            'fields' => array(/* Fields */),
        ),
    ),
)
```

### Section with Both Fields and Subsections

When a section has both, clicking on the section shows main fields, and subsections are expandable.

```php
array(
    'id'          => 'mixed_section',
    'title'       => __('Mixed', 'textdomain'),
    'icon'        => 'dashicons dashicons-admin-generic',
    'fields'      => array(
        // Main section fields
    ),
    'subsections' => array(
        // Nested subsections
    ),
)
```

## Hide Reset Button

You can hide the "Reset Section" button for specific sections or subsections:

```php
array(
    'id'                => 'info_section',
    'title'             => __('Information', 'textdomain'),
    'icon'              => 'dashicons dashicons-info',
    'hide_reset_button' => true,  // Hide reset button
    'fields'            => array(
        array(
            'id'      => 'info_content',
            'type'    => 'html',
            'title'   => __('About', 'textdomain'),
            'content' => '<p>Information content here.</p>',
        ),
    ),
)
```

## Best Practices

1. **Logical Grouping**: Group related settings together
2. **Use Icons**: Add dashicons for visual navigation
3. **Descriptive Names**: Use clear section/subsection names
4. **Limit Depth**: Avoid deep nesting (max 2 levels: section → subsection)
5. **Balance Fields**: Don't overload sections with too many fields
6. **Use Subsections for Complex Settings**: Break down complex sections

## Built-in Sections

The framework automatically adds these sections to every panel:

- **Export/Import**: Export and import settings
- **API & Webhook**: Configure REST API and webhooks

These are handled internally and don't need to be defined.

---

## Next Steps

- [Panel Configuration](panel-configuration.md) - Configure panel branding
- [Chainable API](chainable-api.md) - Learn the chainable API
- [Field Types](field-types.md) - Available field types
