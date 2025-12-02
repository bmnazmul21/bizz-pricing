# Premium Features

This document explains how to implement premium/pro field locking and premium status management in the BizzPlugin Options Framework.

## Overview

The framework supports marking individual fields as "premium", which automatically:
- Displays a "Blocked" badge on the field
- Disables the input field
- Prevents saving changes to that field

This is useful for creating freemium plugins where some features are locked in the free version.

## Setting Premium Status

### At Panel Creation

```php
$framework->create_panel(array(
    'id'          => 'my_plugin',
    'title'       => __('My Plugin', 'textdomain'),
    'menu_title'  => __('My Plugin', 'textdomain'),
    'menu_slug'   => 'my-plugin',
    'capability'  => 'manage_options',
    'icon'        => 'dashicons-admin-settings',
    'position'    => 80,
    'option_name' => 'my_plugin_options',
    'is_premium'  => false,  // Set to true to unlock premium fields
    'sections'    => array(/* sections */),
));
```

### Using Chainable API

```php
$panel->set_premium(true);  // Unlock premium fields
$panel->set_premium(false); // Lock premium fields (default)
```

### Using Filters

```php
add_filter('bizzplugin_is_premium_my_plugin', function($is_premium, $panel_id) {
    // Check your license
    return my_plugin_license_is_valid();
}, 10, 2);
```

## Marking Fields as Premium

Add `'premium' => true` to any field definition:

```php
array(
    'id'          => 'advanced_feature',
    'type'        => 'switch',
    'title'       => __('Advanced Feature', 'textdomain'),
    'description' => __('This feature is available in the premium version.', 'textdomain'),
    'default'     => '0',
    'premium'     => true,  // This field is premium-only
)
```

## Visual Indicators

### Free Version (is_premium = false)

Premium fields display:
- "Blocked" badge next to the title
- Disabled/grayed out input
- Visual styling indicating locked state

### Premium Version (is_premium = true)

Premium fields display normally with full functionality.

## Badge Display

The panel header shows either:
- "Premium" badge (green) - when `is_premium` is true
- "Free" badge (blue) - when `is_premium` is false

## License Integration Example

### Basic License Check

```php
class My_Plugin {
    private $is_premium = false;
    
    public function __construct() {
        // Check license on init
        $this->is_premium = $this->check_license();
        
        add_action('init', array($this, 'init_settings'));
        add_filter('bizzplugin_is_premium_my_plugin', array($this, 'get_premium_status'), 10, 2);
    }
    
    private function check_license() {
        $license_key = get_option('my_plugin_license_key', '');
        $license_status = get_option('my_plugin_license_status', '');
        
        return !empty($license_key) && $license_status === 'valid';
    }
    
    public function get_premium_status($is_premium, $panel_id) {
        return $this->is_premium;
    }
    
    public function init_settings() {
        $framework = bizzplugin_framework();
        
        $framework->create_panel(array(
            'id'          => 'my_plugin',
            'title'       => __('My Plugin', 'textdomain'),
            'option_name' => 'my_plugin_options',
            'is_premium'  => $this->is_premium,
            // ... other options
        ));
    }
}
```

### License Activation Section

```php
// Add a license section to your settings
array(
    'id'          => 'license',
    'title'       => __('License', 'textdomain'),
    'description' => __('Enter your license key to unlock premium features.', 'textdomain'),
    'icon'        => 'dashicons dashicons-admin-network',
    'fields'      => array(
        array(
            'id'          => 'license_key',
            'type'        => 'text',
            'title'       => __('License Key', 'textdomain'),
            'description' => __('Enter your license key from your purchase receipt.', 'textdomain'),
            'default'     => '',
            'placeholder' => __('xxxx-xxxx-xxxx-xxxx', 'textdomain'),
        ),
        array(
            'id'      => 'license_status',
            'type'    => 'html',
            'title'   => __('License Status', 'textdomain'),
            'content' => $this->get_license_status_html(),
        ),
    ),
),
```

### Dynamic License Status Display

```php
private function get_license_status_html() {
    $status = get_option('my_plugin_license_status', '');
    
    if ($status === 'valid') {
        return '<div class="bizzplugin-notice bizzplugin-notice-success">
            <p><span class="dashicons dashicons-yes-alt"></span> ' . 
            esc_html__('License is active and valid.', 'textdomain') . '</p>
        </div>';
    } else {
        return '<div class="bizzplugin-notice bizzplugin-notice-warning">
            <p><span class="dashicons dashicons-warning"></span> ' . 
            esc_html__('No valid license found. Premium features are locked.', 'textdomain') . '</p>
            <p><a href="https://example.com/purchase" target="_blank" class="button button-primary">' . 
            esc_html__('Get Premium', 'textdomain') . '</a></p>
        </div>';
    }
}
```

## Premium Sections

You can create entire sections that are premium-only:

```php
array(
    'id'          => 'premium_features',
    'title'       => __('Premium Features', 'textdomain'),
    'description' => __('Exclusive features for premium users.', 'textdomain'),
    'icon'        => 'dashicons dashicons-star-filled',
    'fields'      => array(
        array(
            'id'      => 'feature_1',
            'type'    => 'switch',
            'title'   => __('Feature One', 'textdomain'),
            'default' => '0',
            'premium' => true,
        ),
        array(
            'id'      => 'feature_2',
            'type'    => 'switch',
            'title'   => __('Feature Two', 'textdomain'),
            'default' => '0',
            'premium' => true,
        ),
        array(
            'id'      => 'upgrade_notice',
            'type'    => 'html',
            'title'   => __('Upgrade', 'textdomain'),
            'content' => $this->is_premium ? '' : '<div class="bizzplugin-notice bizzplugin-notice-info">
                <p><strong>' . esc_html__('Unlock all features!', 'textdomain') . '</strong></p>
                <p>' . esc_html__('Upgrade to premium for access to all features.', 'textdomain') . '</p>
                <p><a href="#" class="button button-primary">' . esc_html__('Upgrade Now', 'textdomain') . '</a></p>
            </div>',
        ),
    ),
),
```

## Checking Premium Status in Code

### In Field Logic

```php
// When using field values
$options = get_option('my_plugin_options', array());
$framework = bizzplugin_framework();
$panel = $framework->get_panel('my_plugin');

if ($panel && $panel->is_premium()) {
    // Premium feature code
    $advanced_value = $options['advanced_feature'] ?? '0';
} else {
    // Free version fallback
    $advanced_value = '0'; // Force default
}
```

### In Templates

```php
<?php
$framework = bizzplugin_framework();
$panel = $framework->get_panel('my_plugin');
$is_premium = $panel ? $panel->is_premium() : false;

if ($is_premium) : ?>
    <!-- Premium features HTML -->
<?php else : ?>
    <!-- Free version HTML with upgrade prompt -->
<?php endif; ?>
```

## Best Practices

1. **Clear Communication**: Make it obvious which features are premium
2. **Graceful Degradation**: Free version should still be useful
3. **Upgrade Prompts**: Include clear upgrade paths in the UI
4. **Consistent Checking**: Use the filter for license checks
5. **Caching**: Cache license status to avoid repeated API calls
6. **Offline Support**: Handle cases where license server is unreachable

## CSS Customization

The framework applies these classes to premium fields:

```css
/* Locked premium field container */
.bizzplugin-field-premium-locked {
    opacity: 0.7;
    position: relative;
}

/* Premium badge */
.bizzplugin-premium-badge {
    background: #dc3232;
    color: #fff;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 3px;
    margin-left: 8px;
}
```

You can customize these styles:

```css
/* Custom premium field styling */
.bizzplugin-field-premium-locked {
    background: linear-gradient(135deg, #f9f9f9 0%, #e9e9e9 100%);
    border-left: 4px solid #dba617;
}

.bizzplugin-field-premium-locked::after {
    content: "🔒";
    position: absolute;
    right: 10px;
    top: 10px;
}
```

---

## Next Steps

- [Field Types](field-types.md) - All available field types
- [Filters & Hooks](filters-hooks.md) - Customization hooks
- [Examples](examples.md) - Complete code examples
