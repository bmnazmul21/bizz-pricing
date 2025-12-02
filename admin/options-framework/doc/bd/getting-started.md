# শুরু করার নির্দেশিকা

এই গাইড আপনাকে BizzPlugin Options Framework ব্যবহার করে আপনার প্রথম সেটিংস প্যানেল সেট আপ করতে সাহায্য করবে।

## ধাপ ১: ফ্রেমওয়ার্ক অন্তর্ভুক্ত করুন

প্রথমে, আপনার প্লাগইনে ফ্রেমওয়ার্ক অন্তর্ভুক্ত করুন:

```php
// আপনার মূল প্লাগইন ফাইলে
require_once plugin_dir_path(__FILE__) . 'options-framework/options-loader.php';
```

## ধাপ ২: সেটিংস প্যানেল তৈরি করুন

WordPress ইনিশিয়ালাইজেশনের সময় একটি প্যানেল তৈরি করুন:

```php
<?php
/**
 * Plugin Name: আমার অসাধারণ প্লাগইন
 * Description: BizzPlugin Options Framework ব্যবহার করে উদাহরণ প্লাগইন
 * Version: 1.0.0
 */

// সরাসরি অ্যাক্সেস প্রতিরোধ
if (!defined('ABSPATH')) {
    exit;
}

// প্লাগইন কনস্ট্যান্ট সংজ্ঞায়িত করুন
define('MY_PLUGIN_VERSION', '1.0.0');
define('MY_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));

// অপশন ফ্রেমওয়ার্ক অন্তর্ভুক্ত করুন
require_once MY_PLUGIN_PATH . 'options-framework/options-loader.php';

/**
 * সেটিংস ইনিশিয়ালাইজ করুন
 */
add_action('init', function() {
    // ফ্রেমওয়ার্ক ইনস্ট্যান্স পান
    $framework = bizzplugin_framework();
    
    // সেটিংস প্যানেল তৈরি করুন
    $panel = $framework->create_panel(array(
        'id'          => 'my_plugin_settings',        // অনন্য প্যানেল ID
        'title'       => __('আমার প্লাগইন সেটিংস', 'my-plugin'),
        'menu_title'  => __('আমার প্লাগইন', 'my-plugin'),
        'menu_slug'   => 'my-plugin-settings',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-settings',  // WordPress ড্যাশআইকন
        'position'    => 80,                          // মেনু পজিশন
        'option_name' => 'my_plugin_options',         // ডাটাবেস অপশন নাম
        'is_premium'  => false,                       // প্রিমিয়াম স্ট্যাটাস
        'sections'    => array(
            array(
                'id'          => 'general',
                'title'       => __('সাধারণ সেটিংস', 'my-plugin'),
                'description' => __('সাধারণ সেটিংস কনফিগার করুন।', 'my-plugin'),
                'icon'        => 'dashicons dashicons-admin-generic',
                'fields'      => array(
                    array(
                        'id'          => 'site_name',
                        'type'        => 'text',
                        'title'       => __('সাইট নাম', 'my-plugin'),
                        'description' => __('আপনার সাইটের নাম লিখুন।', 'my-plugin'),
                        'default'     => get_bloginfo('name'),
                        'placeholder' => __('সাইট নাম লিখুন...', 'my-plugin'),
                    ),
                    array(
                        'id'          => 'enable_feature',
                        'type'        => 'switch',
                        'title'       => __('ফিচার সক্রিয় করুন', 'my-plugin'),
                        'description' => __('প্রধান ফিচার টগল করুন।', 'my-plugin'),
                        'default'     => '1',
                    ),
                ),
            ),
        ),
    ));
});
```

## ধাপ ৩: সংরক্ষিত অপশন পুনরুদ্ধার করুন

আপনার সংরক্ষিত সেটিংস পুনরুদ্ধার করতে WordPress `get_option()` ফাংশন ব্যবহার করুন:

```php
// সব অপশন পান
$options = get_option('my_plugin_options', array());

// ডিফল্ট ফলব্যাক সহ একটি নির্দিষ্ট অপশন পান
$site_name = isset($options['site_name']) ? $options['site_name'] : '';
$is_enabled = isset($options['enable_feature']) ? $options['enable_feature'] : '0';

// হেল্পার ফাংশন (ঐচ্ছিক)
function my_plugin_get_option($key, $default = '') {
    $options = get_option('my_plugin_options', array());
    return isset($options[$key]) ? $options[$key] : $default;
}

// ব্যবহার
$site_name = my_plugin_get_option('site_name', 'ডিফল্ট সাইট');
```

## চেইনেবল API ব্যবহার করা (প্রস্তাবিত)

ফ্রেমওয়ার্ক আরও নমনীয়তার জন্য একটি আধুনিক চেইনেবল API সাপোর্ট করে:

```php
add_action('init', function() {
    $framework = bizzplugin_framework();
    
    // ন্যূনতম কনফিগ দিয়ে প্যানেল তৈরি করুন
    $panel = $framework->create_panel(array(
        'id'          => 'my_plugin_settings',
        'title'       => __('আমার প্লাগইন সেটিংস', 'my-plugin'),
        'menu_title'  => __('আমার প্লাগইন', 'my-plugin'),
        'menu_slug'   => 'my-plugin-settings',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-settings',
        'position'    => 80,
        'option_name' => 'my_plugin_options',
    ));
    
    // চেইনেবল মেথড ব্যবহার করে প্যানেল কনফিগার করুন
    $panel
        ->set_logo(MY_PLUGIN_URL . 'assets/logo.png')
        ->set_version(MY_PLUGIN_VERSION)
        ->set_panel_title(__('আমার প্লাগইন সেটিংস', 'my-plugin'))
        ->set_premium(false)
        ->set_footer_text(__('আমার কোম্পানি দ্বারা চালিত', 'my-plugin'));
    
    // সেকশন যোগ করুন
    $panel->add_section(array(
        'id'          => 'general',
        'title'       => __('সাধারণ সেটিংস', 'my-plugin'),
        'description' => __('সাধারণ সেটিংস কনফিগার করুন।', 'my-plugin'),
        'icon'        => 'dashicons dashicons-admin-generic',
        'fields'      => array(
            array(
                'id'          => 'site_name',
                'type'        => 'text',
                'title'       => __('সাইট নাম', 'my-plugin'),
                'description' => __('আপনার সাইটের নাম লিখুন।', 'my-plugin'),
                'default'     => get_bloginfo('name'),
            ),
        ),
    ));
    
    // বিদ্যমান সেকশনে আরও ফিল্ড যোগ করুন
    $panel->add_field('general', array(
        'id'          => 'site_color',
        'type'        => 'color',
        'title'       => __('প্রাথমিক রঙ', 'my-plugin'),
        'description' => __('আপনার প্রাথমিক রঙ নির্বাচন করুন।', 'my-plugin'),
        'default'     => '#2271b1',
    ));
    
    // সাবসেকশন যোগ করুন
    $panel->add_subsection('general', array(
        'id'          => 'advanced',
        'title'       => __('অ্যাডভান্সড অপশন', 'my-plugin'),
        'description' => __('অ্যাডভান্সড কনফিগারেশন।', 'my-plugin'),
        'fields'      => array(
            array(
                'id'          => 'debug_mode',
                'type'        => 'checkbox',
                'title'       => __('ডিবাগ মোড', 'my-plugin'),
                'description' => __('ডিবাগ মোড সক্রিয় করুন।', 'my-plugin'),
                'default'     => '0',
                'label'       => __('ডিবাগ সক্রিয় করুন', 'my-plugin'),
            ),
        ),
    ));
    
    // সাইডবারের জন্য রিসোর্স যোগ করুন
    $panel->add_resource(array(
        'icon'  => 'dashicons dashicons-book',
        'title' => __('ডকুমেন্টেশন', 'my-plugin'),
        'url'   => 'https://example.com/docs',
    ));
    
    // প্রস্তাবিত প্লাগইন যোগ করুন
    $panel->add_recommended_plugin(array(
        'slug'        => 'elementor',
        'name'        => 'Elementor',
        'description' => __('সেরা পেজ বিল্ডার।', 'my-plugin'),
        'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
        'author'      => 'Elementor.com',
        'file'        => 'elementor/elementor.php',
        'url'         => 'https://wordpress.org/plugins/elementor/',
    ));
});
```

## সম্পূর্ণ মৌলিক উদাহরণ

এখানে একটি সম্পূর্ণ ন্যূনতম উদাহরণ:

```php
<?php
/**
 * Plugin Name: আমার প্লাগইন
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
        'title'       => 'আমার প্লাগইন',
        'menu_title'  => 'আমার প্লাগইন',
        'menu_slug'   => 'my-plugin',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-generic',
        'position'    => 80,
        'option_name' => 'my_plugin_options',
        'sections'    => array(
            array(
                'id'     => 'general',
                'title'  => 'সাধারণ',
                'icon'   => 'dashicons dashicons-admin-generic',
                'fields' => array(
                    array(
                        'id'      => 'my_text',
                        'type'    => 'text',
                        'title'   => 'আমার টেক্সট ফিল্ড',
                        'default' => 'হ্যালো বিশ্ব',
                    ),
                ),
            ),
        ),
    ));
});

// আপনার প্লাগইনে ব্যবহার
function my_plugin_get_text() {
    $options = get_option('my_plugin_options', array());
    return isset($options['my_text']) ? $options['my_text'] : 'হ্যালো বিশ্ব';
}
```

## পরবর্তী পদক্ষেপ

- [ফিল্ড টাইপ](field-types.md) - সব উপলব্ধ ফিল্ড টাইপ সম্পর্কে জানুন
- [সেকশন এবং সাবসেকশন](sections-subsections.md) - আপনার সেটিংস সংগঠিত করুন
- [প্যানেল কনফিগারেশন](panel-configuration.md) - প্যানেল ব্র্যান্ডিং কাস্টমাইজ করুন
- [চেইনেবল API](chainable-api.md) - চেইনেবল API আয়ত্ত করুন
