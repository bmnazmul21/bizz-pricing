# চেইনেবল API

এই ডকুমেন্ট BizzPlugin Options Framework-এ চেইনেবল API-এর একটি সম্পূর্ণ রেফারেন্স প্রদান করে।

## ভূমিকা

চেইনেবল API সেটিংস প্যানেল তৈরির জন্য একটি ফ্লুয়েন্ট, আধুনিক ইন্টারফেস প্রদান করে। সব চেইনেবল মেথড `$this` রিটার্ন করে, যা আপনাকে একাধিক কল একসাথে চেইন করতে দেয়।

```php
$panel
    ->set_logo(MY_PLUGIN_URL . 'assets/logo.png')
    ->set_version('1.0.0')
    ->set_premium(false)
    ->add_section(array(/* সেকশন কনফিগ */))
    ->add_field('section_id', array(/* ফিল্ড কনফিগ */))
    ->add_resource(array(/* রিসোর্স কনফিগ */));
```

## প্যানেল কনফিগারেশন মেথড

### set_panel_config($config)

একসাথে একাধিক কনফিগারেশন মান সেট করুন।

```php
$panel->set_panel_config(array(
    'title'               => 'আমার প্লাগইন',
    'logo'                => 'https://example.com/logo.png',
    'version'             => '1.0.0',
    'is_premium'          => false,
    'footer_text'         => 'BizzPlugin দ্বারা চালিত',
    'resources'           => array(),
    'recommended_plugins' => array(),
));
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### set_config($key, $value)

একটি একক কনফিগারেশন মান সেট করুন।

```php
$panel->set_config('logo', 'https://example.com/logo.png');
$panel->set_config('version', '1.0.0');
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### set_logo($logo_url)

প্যানেল লোগো সেট করুন।

```php
$panel->set_logo(MY_PLUGIN_URL . 'assets/logo.png');
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### set_version($version)

ভার্সন নম্বর সেট করুন।

```php
$panel->set_version('1.0.0');
// অথবা একটি কনস্ট্যান্ট ব্যবহার করুন
$panel->set_version(MY_PLUGIN_VERSION);
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### set_panel_title($title)

প্যানেল শিরোনাম সেট করুন (হেডারে প্রদর্শিত)।

```php
$panel->set_panel_title(__('আমার প্লাগইন সেটিংস', 'textdomain'));
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### set_premium($is_premium)

প্রিমিয়াম স্ট্যাটাস সেট করুন। এটি প্রভাবিত করে:
- প্রিমিয়াম/ফ্রি ব্যাজ প্রদর্শন
- প্রিমিয়াম-চিহ্নিত ফিল্ড লক আছে কি না

```php
$panel->set_premium(true);  // প্রিমিয়াম
$panel->set_premium(false); // ফ্রি
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### set_footer_text($text)

ফুটার টেক্সট সেট করুন।

```php
$panel->set_footer_text(__('আমার কোম্পানি দ্বারা চালিত', 'textdomain'));
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### get_panel_config($key = null)

প্যানেল কনফিগারেশন পান।

```php
// সব কনফিগ পান
$config = $panel->get_panel_config();

// নির্দিষ্ট কী পান
$logo = $panel->get_panel_config('logo');
```

**রিটার্ন**: `array` (সব কনফিগ) অথবা `mixed` (নির্দিষ্ট কী মান)

## সেকশন মেথড

### add_section($args)

প্যানেলে একটি নতুন সেকশন যোগ করুন।

```php
$panel->add_section(array(
    'id'                => 'general',
    'title'             => __('সাধারণ সেটিংস', 'textdomain'),
    'description'       => __('সাধারণ অপশন কনফিগার করুন।', 'textdomain'),
    'icon'              => 'dashicons dashicons-admin-generic',
    'hide_reset_button' => false,
    'fields'            => array(
        array(
            'id'      => 'site_name',
            'type'    => 'text',
            'title'   => __('সাইট নাম', 'textdomain'),
            'default' => '',
        ),
    ),
    'subsections'       => array(
        array(
            'id'     => 'advanced',
            'title'  => __('অ্যাডভান্সড', 'textdomain'),
            'fields' => array(/* ফিল্ড */),
        ),
    ),
));
```

**প্যারামিটার**:
- `id` (string, আবশ্যক): অনন্য সেকশন ID
- `title` (string, আবশ্যক): সেকশন প্রদর্শন শিরোনাম
- `description` (string, ঐচ্ছিক): সেকশন বিবরণ
- `icon` (string, ঐচ্ছিক): WordPress ড্যাশআইকন ক্লাস
- `fields` (array, ঐচ্ছিক): ফিল্ড কনফিগারেশনের অ্যারে
- `subsections` (array, ঐচ্ছিক): সাবসেকশন কনফিগারেশনের অ্যারে
- `hide_reset_button` (bool, ঐচ্ছিক): এই সেকশনের জন্য রিসেট বাটন লুকান

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### get_sections()

সব সেকশন পান।

```php
$sections = $panel->get_sections();
```

**রিটার্ন**: সেকশনের `array`

### get_section($section_id)

ID দ্বারা একটি নির্দিষ্ট সেকশন পান।

```php
$section = $panel->get_section('general');
```

**রিটার্ন**: `array|null` সেকশন কনফিগারেশন অথবা খুঁজে না পেলে null

### remove_section($section_id)

ID দ্বারা একটি সেকশন রিমুভ করুন।

```php
$panel->remove_section('deprecated_section');
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

## ফিল্ড মেথড

### add_field($section_id, $field)

একটি বিদ্যমান সেকশনে ফিল্ড যোগ করুন।

```php
$panel->add_field('general', array(
    'id'          => 'email',
    'type'        => 'email',
    'title'       => __('ইমেইল ঠিকানা', 'textdomain'),
    'description' => __('ইমেইল ঠিকানা লিখুন।', 'textdomain'),
    'default'     => '',
    'placeholder' => 'admin@example.com',
));
```

**প্যারামিটার**:
- `$section_id` (string): যে সেকশনে ফিল্ড যোগ করতে হবে তার ID
- `$field` (array): ফিল্ড কনফিগারেশন অ্যারে

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### remove_field($section_id, $field_id)

একটি সেকশন থেকে ফিল্ড রিমুভ করুন।

```php
$panel->remove_field('general', 'old_field');
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### get_all_fields()

সব সেকশন থেকে সব ফিল্ড পান।

```php
$all_fields = $panel->get_all_fields();
```

**রিটার্ন**: ফিল্ড ID দ্বারা ইনডেক্স করা সব ফিল্ডের `array`

## সাবসেকশন মেথড

### add_subsection($section_id, $subsection)

একটি বিদ্যমান সেকশনে সাবসেকশন যোগ করুন।

```php
$panel->add_subsection('general', array(
    'id'          => 'advanced',
    'title'       => __('অ্যাডভান্সড অপশন', 'textdomain'),
    'description' => __('অ্যাডভান্সড কনফিগারেশন।', 'textdomain'),
    'fields'      => array(
        array(
            'id'      => 'debug_mode',
            'type'    => 'checkbox',
            'title'   => __('ডিবাগ মোড', 'textdomain'),
            'default' => '0',
            'label'   => __('ডিবাগ সক্রিয়', 'textdomain'),
        ),
    ),
));
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### add_subsection_field($section_id, $subsection_id, $field)

একটি সাবসেকশনে ফিল্ড যোগ করুন।

```php
$panel->add_subsection_field('general', 'advanced', array(
    'id'      => 'log_level',
    'type'    => 'select',
    'title'   => __('লগ লেভেল', 'textdomain'),
    'default' => 'error',
    'options' => array(
        'error'   => __('এরর', 'textdomain'),
        'warning' => __('সতর্কতা', 'textdomain'),
        'info'    => __('তথ্য', 'textdomain'),
    ),
));
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### remove_subsection($section_id, $subsection_id)

একটি সেকশন থেকে সাবসেকশন রিমুভ করুন।

```php
$panel->remove_subsection('general', 'old_subsection');
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

## রিসোর্স মেথড

### set_resources($resources)

সব রিসোর্স লিঙ্ক সেট করুন (বিদ্যমান প্রতিস্থাপন করে)।

```php
$panel->set_resources(array(
    array(
        'icon'  => 'dashicons dashicons-book',
        'title' => __('ডকুমেন্টেশন', 'textdomain'),
        'url'   => 'https://example.com/docs',
    ),
    array(
        'icon'  => 'dashicons dashicons-sos',
        'title' => __('সাপোর্ট', 'textdomain'),
        'url'   => 'https://example.com/support',
    ),
));
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### add_resource($resource)

একটি একক রিসোর্স লিঙ্ক যোগ করুন।

```php
$panel->add_resource(array(
    'icon'  => 'dashicons dashicons-star-filled',
    'title' => __('প্লাগইন রেট করুন', 'textdomain'),
    'url'   => 'https://wordpress.org/plugins/my-plugin/reviews/',
));
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### get_resources()

সব রিসোর্স লিঙ্ক পান।

```php
$resources = $panel->get_resources();
```

**রিটার্ন**: রিসোর্সের `array`

## প্রস্তাবিত প্লাগইন মেথড

### set_recommended_plugins($plugins)

সব প্রস্তাবিত প্লাগইন সেট করুন (বিদ্যমান প্রতিস্থাপন করে)।

```php
$panel->set_recommended_plugins(array(
    array(
        'slug'        => 'elementor',
        'name'        => 'Elementor',
        'description' => __('পেজ বিল্ডার।', 'textdomain'),
        'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
        'author'      => 'Elementor.com',
        'file'        => 'elementor/elementor.php',
        'url'         => 'https://wordpress.org/plugins/elementor/',
    ),
));
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### add_recommended_plugin($plugin)

একটি একক প্রস্তাবিত প্লাগইন যোগ করুন।

```php
$panel->add_recommended_plugin(array(
    'slug'        => 'woocommerce',
    'name'        => 'WooCommerce',
    'description' => __('ই-কমার্স প্ল্যাটফর্ম।', 'textdomain'),
    'thumbnail'   => 'https://ps.w.org/woocommerce/assets/icon-256x256.png',
    'author'      => 'Automattic',
    'file'        => 'woocommerce/woocommerce.php',
    'url'         => 'https://wordpress.org/plugins/woocommerce/',
));
```

**রিটার্ন**: `$this` (চেইনিংয়ের জন্য)

### get_recommended_plugins()

সব প্রস্তাবিত প্লাগইন পান।

```php
$plugins = $panel->get_recommended_plugins();
```

**রিটার্ন**: প্লাগইনের `array`

## ইউটিলিটি মেথড

### get_id()

প্যানেল ID পান।

```php
$panel_id = $panel->get_id();
```

**রিটার্ন**: `string`

### get_option_name()

অপশন নাম (ডাটাবেস কী) পান।

```php
$option_name = $panel->get_option_name();
```

**রিটার্ন**: `string`

### is_premium()

প্যানেল প্রিমিয়াম মোডে আছে কি না চেক করুন।

```php
if ($panel->is_premium()) {
    // প্রিমিয়াম মোড লজিক
}
```

**রিটার্ন**: `bool`

### get_args()

সব প্যানেল আর্গুমেন্ট পান।

```php
$args = $panel->get_args();
// রিটার্ন করে: id, title, menu_title, menu_slug, parent_slug, capability, icon, position, option_name, is_premium
```

**রিটার্ন**: `array`

### get_all_defaults()

সব ফিল্ডের ডিফল্ট মান পান।

```php
$defaults = $panel->get_all_defaults();
```

**রিটার্ন**: ফিল্ড IDs => ডিফল্ট মানের `array`

### get_section_defaults($section_id)

একটি নির্দিষ্ট সেকশনের ডিফল্ট মান পান।

```php
$section_defaults = $panel->get_section_defaults('general');
```

**রিটার্ন**: ফিল্ড IDs => ডিফল্ট মানের `array`

## সম্পূর্ণ চেইনেবল উদাহরণ

```php
add_action('init', function() {
    $framework = bizzplugin_framework();
    
    $framework->create_panel(array(
        'id'          => 'my_plugin',
        'title'       => __('আমার প্লাগইন', 'textdomain'),
        'menu_title'  => __('আমার প্লাগইন', 'textdomain'),
        'menu_slug'   => 'my-plugin',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-settings',
        'position'    => 80,
        'option_name' => 'my_plugin_options',
    ))
    // প্যানেল কনফিগারেশন
    ->set_logo(MY_PLUGIN_URL . 'assets/logo.png')
    ->set_version('1.0.0')
    ->set_panel_title(__('আমার প্লাগইন সেটিংস', 'textdomain'))
    ->set_premium(false)
    ->set_footer_text(__('BizzPlugin দ্বারা চালিত', 'textdomain'))
    
    // প্রধান সেকশন যোগ
    ->add_section(array(
        'id'     => 'general',
        'title'  => __('সাধারণ', 'textdomain'),
        'icon'   => 'dashicons dashicons-admin-generic',
        'fields' => array(
            array(
                'id'      => 'site_name',
                'type'    => 'text',
                'title'   => __('সাইট নাম', 'textdomain'),
                'default' => get_bloginfo('name'),
            ),
        ),
    ))
    
    // সেকশনে আরও ফিল্ড যোগ
    ->add_field('general', array(
        'id'      => 'enable_feature',
        'type'    => 'switch',
        'title'   => __('ফিচার সক্রিয়', 'textdomain'),
        'default' => '1',
    ))
    
    // সাবসেকশন যোগ
    ->add_subsection('general', array(
        'id'     => 'advanced',
        'title'  => __('অ্যাডভান্সড', 'textdomain'),
        'fields' => array(
            array(
                'id'      => 'debug_mode',
                'type'    => 'checkbox',
                'title'   => __('ডিবাগ মোড', 'textdomain'),
                'default' => '0',
            ),
        ),
    ))
    
    // সাবসেকশনে ফিল্ড যোগ
    ->add_subsection_field('general', 'advanced', array(
        'id'      => 'cache_time',
        'type'    => 'number',
        'title'   => __('ক্যাশ সময়', 'textdomain'),
        'default' => 3600,
        'min'     => 0,
        'max'     => 86400,
    ))
    
    // আরেকটি সেকশন যোগ
    ->add_section(array(
        'id'     => 'appearance',
        'title'  => __('চেহারা', 'textdomain'),
        'icon'   => 'dashicons dashicons-admin-appearance',
        'fields' => array(
            array(
                'id'      => 'primary_color',
                'type'    => 'color',
                'title'   => __('প্রাথমিক রঙ', 'textdomain'),
                'default' => '#2271b1',
            ),
        ),
    ))
    
    // রিসোর্স যোগ
    ->add_resource(array(
        'icon'  => 'dashicons dashicons-book',
        'title' => __('ডকুমেন্টেশন', 'textdomain'),
        'url'   => 'https://example.com/docs',
    ))
    ->add_resource(array(
        'icon'  => 'dashicons dashicons-sos',
        'title' => __('সাপোর্ট', 'textdomain'),
        'url'   => 'https://example.com/support',
    ))
    
    // প্রস্তাবিত প্লাগইন যোগ
    ->add_recommended_plugin(array(
        'slug'        => 'elementor',
        'name'        => 'Elementor',
        'description' => __('পেজ বিল্ডার।', 'textdomain'),
        'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
        'author'      => 'Elementor.com',
        'file'        => 'elementor/elementor.php',
        'url'         => 'https://wordpress.org/plugins/elementor/',
    ));
});
```

---

## পরবর্তী পদক্ষেপ

- [ফিল্টার এবং হুক](filters-hooks.md) - ফিল্টার দিয়ে এক্সটেন্ড করুন
- [প্যানেল কনফিগারেশন](panel-configuration.md) - কনফিগারেশন বিস্তারিত
- [উদাহরণ](examples.md) - আরও কোড উদাহরণ
