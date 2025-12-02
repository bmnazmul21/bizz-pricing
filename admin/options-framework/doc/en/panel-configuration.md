# Panel Configuration

This document explains how to configure panel branding, resources, and recommended plugins.

## Panel Configuration Overview

Panel configuration controls the visual appearance and supplementary information displayed in your settings panel, including:

- Logo and branding
- Version display
- Premium/Free status badge
- Resource links in the sidebar
- Recommended plugins
- Footer text

## Configuration Methods

### Method 1: Using set_panel_config()

Set multiple configuration values at once:

```php
$panel->set_panel_config(array(
    'title'               => __('My Plugin Settings', 'textdomain'),
    'logo'                => MY_PLUGIN_URL . 'assets/logo.png',
    'version'             => MY_PLUGIN_VERSION,
    'is_premium'          => false,
    'footer_text'         => __('Powered by My Company', 'textdomain'),
    'recommended_plugins' => array(/* plugins array */),
    'resources'           => array(/* resources array */),
));
```

### Method 2: Using Individual Setters

Use chainable methods for individual settings:

```php
$panel
    ->set_logo(MY_PLUGIN_URL . 'assets/logo.png')
    ->set_version(MY_PLUGIN_VERSION)
    ->set_panel_title(__('My Plugin Settings', 'textdomain'))
    ->set_premium(false)
    ->set_footer_text(__('Powered by My Company', 'textdomain'));
```

## Configuration Options

### Logo

The logo appears at the top of the navigation sidebar.

```php
// Using chainable method
$panel->set_logo(MY_PLUGIN_URL . 'assets/logo.png');

// Using set_panel_config
$panel->set_panel_config(array(
    'logo' => MY_PLUGIN_URL . 'assets/logo.png',
));

// Using set_config
$panel->set_config('logo', MY_PLUGIN_URL . 'assets/logo.png');
```

**Recommended logo size**: 200x50 pixels (or similar aspect ratio)

### Panel Title

The title displayed below the logo in the navigation header.

```php
$panel->set_panel_title(__('My Plugin Settings', 'textdomain'));
```

### Version

The version number displayed next to the premium/free badge.

```php
$panel->set_version('1.0.0');

// Or use a constant
$panel->set_version(MY_PLUGIN_VERSION);
```

### Premium Status

Controls whether the "Premium" or "Free" badge is shown, and whether premium fields are locked.

```php
// Set as free
$panel->set_premium(false);

// Set as premium
$panel->set_premium(true);
```

### Footer Text

Text displayed at the bottom of the navigation sidebar.

```php
$panel->set_footer_text(__('Powered by My Company', 'textdomain'));
```

## Resource Links

Resource links appear in the right sidebar and provide quick access to documentation, support, etc.

### Set All Resources

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
    array(
        'icon'  => 'dashicons dashicons-cart',
        'title' => __('Upgrade to Pro', 'textdomain'),
        'url'   => 'https://example.com/pro',
    ),
));
```

### Add Individual Resources

```php
$panel->add_resource(array(
    'icon'  => 'dashicons dashicons-star-filled',
    'title' => __('Rate Plugin', 'textdomain'),
    'url'   => 'https://wordpress.org/plugins/my-plugin/reviews/',
));

$panel->add_resource(array(
    'icon'  => 'dashicons dashicons-facebook',
    'title' => __('Facebook Group', 'textdomain'),
    'url'   => 'https://facebook.com/groups/my-plugin',
));
```

### Resource Link Properties

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `icon` | string | Yes | WordPress dashicon class |
| `title` | string | Yes | Link text |
| `url` | string | Yes | Target URL |

### Common Dashicons for Resources

- `dashicons-book` - Documentation
- `dashicons-sos` - Support
- `dashicons-cart` - Purchase/Upgrade
- `dashicons-star-filled` - Reviews/Rating
- `dashicons-facebook` - Facebook
- `dashicons-twitter` - Twitter
- `dashicons-video-alt3` - Video/YouTube
- `dashicons-email` - Email/Contact
- `dashicons-groups` - Community

## Recommended Plugins

Display a list of recommended plugins with install/activate buttons in the sidebar.

### Set All Recommended Plugins

```php
$panel->set_recommended_plugins(array(
    array(
        'slug'        => 'elementor',
        'name'        => 'Elementor',
        'description' => __('The best page builder.', 'textdomain'),
        'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
        'author'      => 'Elementor.com',
        'file'        => 'elementor/elementor.php',
        'url'         => 'https://wordpress.org/plugins/elementor/',
    ),
    array(
        'slug'        => 'contact-form-7',
        'name'        => 'Contact Form 7',
        'description' => __('Simple contact form plugin.', 'textdomain'),
        'thumbnail'   => 'https://ps.w.org/contact-form-7/assets/icon-256x256.png',
        'author'      => 'Takayuki Miyoshi',
        'file'        => 'contact-form-7/wp-contact-form-7.php',
        'url'         => 'https://wordpress.org/plugins/contact-form-7/',
    ),
));
```

### Add Individual Recommended Plugins

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

### Plugin Properties

| Property | Type | Required | Description |
|----------|------|----------|-------------|
| `slug` | string | Yes | WordPress.org plugin slug |
| `name` | string | Yes | Plugin display name |
| `description` | string | No | Short description |
| `thumbnail` | string | No | Plugin icon URL (256x256) |
| `author` | string | No | Plugin author name |
| `file` | string | Yes | Plugin main file path (for activation check) |
| `url` | string | No | Link to plugin page |

### Finding Plugin Thumbnail URLs

Plugin icons can be found at:
```
https://ps.w.org/{plugin-slug}/assets/icon-256x256.png
```
or
```
https://ps.w.org/{plugin-slug}/assets/icon-256x256.gif
```

## Complete Configuration Example

```php
add_action('init', function() {
    $framework = bizzplugin_framework();
    
    $panel = $framework->create_panel(array(
        'id'          => 'my_plugin',
        'title'       => __('My Plugin', 'textdomain'),
        'menu_title'  => __('My Plugin', 'textdomain'),
        'menu_slug'   => 'my-plugin',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-settings',
        'position'    => 80,
        'option_name' => 'my_plugin_options',
    ));
    
    // Set panel configuration
    $panel->set_panel_config(array(
        'title'      => __('My Plugin Settings', 'textdomain'),
        'logo'       => MY_PLUGIN_URL . 'assets/logo.png',
        'version'    => MY_PLUGIN_VERSION,
        'is_premium' => false,
        'footer_text' => __('© 2024 My Company', 'textdomain'),
        'resources'  => array(
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
            array(
                'icon'  => 'dashicons dashicons-cart',
                'title' => __('Go Pro', 'textdomain'),
                'url'   => 'https://example.com/pro',
            ),
        ),
        'recommended_plugins' => array(
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
    ));
    
    // Add sections
    $panel->add_section(array(
        'id'     => 'general',
        'title'  => __('General', 'textdomain'),
        'icon'   => 'dashicons dashicons-admin-generic',
        'fields' => array(/* fields */),
    ));
});
```

## Using Filters for Configuration

You can also use WordPress filters to modify panel configuration:

```php
// Panel-specific filter (recommended)
add_filter('bizzplugin_panel_config_my_plugin', function($config, $panel_id) {
    $config['logo'] = MY_PLUGIN_URL . 'assets/custom-logo.png';
    $config['is_premium'] = license_is_valid();
    return $config;
}, 10, 2);
```

See [Filters & Hooks](filters-hooks.md) for more details.

---

## Next Steps

- [Chainable API](chainable-api.md) - Full chainable API reference
- [Filters & Hooks](filters-hooks.md) - Customize via filters
- [Examples](examples.md) - Complete code examples
