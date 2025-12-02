# প্যানেল কনফিগারেশন

এই ডকুমেন্ট প্যানেল ব্র্যান্ডিং, রিসোর্স এবং প্রস্তাবিত প্লাগইন কীভাবে কনফিগার করবেন তা ব্যাখ্যা করে।

## প্যানেল কনফিগারেশন ওভারভিউ

প্যানেল কনফিগারেশন আপনার সেটিংস প্যানেলে প্রদর্শিত ভিজ্যুয়াল চেহারা এবং সম্পূরক তথ্য নিয়ন্ত্রণ করে, যার মধ্যে রয়েছে:

- লোগো এবং ব্র্যান্ডিং
- ভার্সন প্রদর্শন
- প্রিমিয়াম/ফ্রি স্ট্যাটাস ব্যাজ
- সাইডবারে রিসোর্স লিঙ্ক
- প্রস্তাবিত প্লাগইন
- ফুটার টেক্সট

## কনফিগারেশন পদ্ধতি

### পদ্ধতি ১: set_panel_config() ব্যবহার করা

একসাথে একাধিক কনফিগারেশন মান সেট করুন:

```php
$panel->set_panel_config(array(
    'title'               => __('আমার প্লাগইন সেটিংস', 'textdomain'),
    'logo'                => MY_PLUGIN_URL . 'assets/logo.png',
    'version'             => MY_PLUGIN_VERSION,
    'is_premium'          => false,
    'footer_text'         => __('আমার কোম্পানি দ্বারা চালিত', 'textdomain'),
    'recommended_plugins' => array(/* প্লাগইন অ্যারে */),
    'resources'           => array(/* রিসোর্স অ্যারে */),
));
```

### পদ্ধতি ২: পৃথক সেটার ব্যবহার করা

পৃথক সেটিংসের জন্য চেইনেবল মেথড ব্যবহার করুন:

```php
$panel
    ->set_logo(MY_PLUGIN_URL . 'assets/logo.png')
    ->set_version(MY_PLUGIN_VERSION)
    ->set_panel_title(__('আমার প্লাগইন সেটিংস', 'textdomain'))
    ->set_premium(false)
    ->set_footer_text(__('আমার কোম্পানি দ্বারা চালিত', 'textdomain'));
```

## কনফিগারেশন অপশন

### লোগো

লোগো নেভিগেশন সাইডবারের উপরে দেখায়।

```php
// চেইনেবল মেথড ব্যবহার করে
$panel->set_logo(MY_PLUGIN_URL . 'assets/logo.png');

// set_panel_config ব্যবহার করে
$panel->set_panel_config(array(
    'logo' => MY_PLUGIN_URL . 'assets/logo.png',
));

// set_config ব্যবহার করে
$panel->set_config('logo', MY_PLUGIN_URL . 'assets/logo.png');
```

**প্রস্তাবিত লোগো সাইজ**: 200x50 পিক্সেল (বা অনুরূপ অনুপাত)

### প্যানেল শিরোনাম

নেভিগেশন হেডারে লোগোর নিচে প্রদর্শিত শিরোনাম।

```php
$panel->set_panel_title(__('আমার প্লাগইন সেটিংস', 'textdomain'));
```

### ভার্সন

প্রিমিয়াম/ফ্রি ব্যাজের পাশে প্রদর্শিত ভার্সন নম্বর।

```php
$panel->set_version('1.0.0');

// অথবা একটি কনস্ট্যান্ট ব্যবহার করুন
$panel->set_version(MY_PLUGIN_VERSION);
```

### প্রিমিয়াম স্ট্যাটাস

"প্রিমিয়াম" বা "ফ্রি" ব্যাজ দেখানো হবে কি না এবং প্রিমিয়াম ফিল্ড লক আছে কি না নিয়ন্ত্রণ করে।

```php
// ফ্রি হিসেবে সেট করুন
$panel->set_premium(false);

// প্রিমিয়াম হিসেবে সেট করুন
$panel->set_premium(true);
```

### ফুটার টেক্সট

নেভিগেশন সাইডবারের নিচে প্রদর্শিত টেক্সট।

```php
$panel->set_footer_text(__('আমার কোম্পানি দ্বারা চালিত', 'textdomain'));
```

## রিসোর্স লিঙ্ক

রিসোর্স লিঙ্ক ডান সাইডবারে দেখায় এবং ডকুমেন্টেশন, সাপোর্ট ইত্যাদিতে দ্রুত অ্যাক্সেস প্রদান করে।

### সব রিসোর্স সেট করুন

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
    array(
        'icon'  => 'dashicons dashicons-cart',
        'title' => __('প্রো তে আপগ্রেড করুন', 'textdomain'),
        'url'   => 'https://example.com/pro',
    ),
));
```

### পৃথক রিসোর্স যোগ করুন

```php
$panel->add_resource(array(
    'icon'  => 'dashicons dashicons-star-filled',
    'title' => __('প্লাগইন রেট করুন', 'textdomain'),
    'url'   => 'https://wordpress.org/plugins/my-plugin/reviews/',
));

$panel->add_resource(array(
    'icon'  => 'dashicons dashicons-facebook',
    'title' => __('ফেসবুক গ্রুপ', 'textdomain'),
    'url'   => 'https://facebook.com/groups/my-plugin',
));
```

### রিসোর্স লিঙ্ক প্রপার্টি

| প্রপার্টি | টাইপ | আবশ্যক | বিবরণ |
|----------|------|--------|-------|
| `icon` | string | হ্যাঁ | WordPress ড্যাশআইকন ক্লাস |
| `title` | string | হ্যাঁ | লিঙ্ক টেক্সট |
| `url` | string | হ্যাঁ | টার্গেট URL |

### রিসোর্সের জন্য সাধারণ ড্যাশআইকন

- `dashicons-book` - ডকুমেন্টেশন
- `dashicons-sos` - সাপোর্ট
- `dashicons-cart` - ক্রয়/আপগ্রেড
- `dashicons-star-filled` - রিভিউ/রেটিং
- `dashicons-facebook` - ফেসবুক
- `dashicons-twitter` - টুইটার
- `dashicons-video-alt3` - ভিডিও/ইউটিউব
- `dashicons-email` - ইমেইল/যোগাযোগ
- `dashicons-groups` - কমিউনিটি

## প্রস্তাবিত প্লাগইন

সাইডবারে ইনস্টল/অ্যাক্টিভেট বাটন সহ প্রস্তাবিত প্লাগইনের তালিকা প্রদর্শন করুন।

### সব প্রস্তাবিত প্লাগইন সেট করুন

```php
$panel->set_recommended_plugins(array(
    array(
        'slug'        => 'elementor',
        'name'        => 'Elementor',
        'description' => __('সেরা পেজ বিল্ডার।', 'textdomain'),
        'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
        'author'      => 'Elementor.com',
        'file'        => 'elementor/elementor.php',
        'url'         => 'https://wordpress.org/plugins/elementor/',
    ),
    array(
        'slug'        => 'contact-form-7',
        'name'        => 'Contact Form 7',
        'description' => __('সাধারণ কন্টাক্ট ফর্ম প্লাগইন।', 'textdomain'),
        'thumbnail'   => 'https://ps.w.org/contact-form-7/assets/icon-256x256.png',
        'author'      => 'Takayuki Miyoshi',
        'file'        => 'contact-form-7/wp-contact-form-7.php',
        'url'         => 'https://wordpress.org/plugins/contact-form-7/',
    ),
));
```

### পৃথক প্রস্তাবিত প্লাগইন যোগ করুন

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

### প্লাগইন প্রপার্টি

| প্রপার্টি | টাইপ | আবশ্যক | বিবরণ |
|----------|------|--------|-------|
| `slug` | string | হ্যাঁ | WordPress.org প্লাগইন স্লাগ |
| `name` | string | হ্যাঁ | প্লাগইন প্রদর্শন নাম |
| `description` | string | না | সংক্ষিপ্ত বিবরণ |
| `thumbnail` | string | না | প্লাগইন আইকন URL (256x256) |
| `author` | string | না | প্লাগইন লেখকের নাম |
| `file` | string | হ্যাঁ | প্লাগইন মূল ফাইল পাথ (অ্যাক্টিভেশন চেকের জন্য) |
| `url` | string | না | প্লাগইন পেজের লিঙ্ক |

### প্লাগইন থাম্বনেইল URL খোঁজা

প্লাগইন আইকন এখানে পাওয়া যায়:
```
https://ps.w.org/{plugin-slug}/assets/icon-256x256.png
```
অথবা
```
https://ps.w.org/{plugin-slug}/assets/icon-256x256.gif
```

## সম্পূর্ণ কনফিগারেশন উদাহরণ

```php
add_action('init', function() {
    $framework = bizzplugin_framework();
    
    $panel = $framework->create_panel(array(
        'id'          => 'my_plugin',
        'title'       => __('আমার প্লাগইন', 'textdomain'),
        'menu_title'  => __('আমার প্লাগইন', 'textdomain'),
        'menu_slug'   => 'my-plugin',
        'capability'  => 'manage_options',
        'icon'        => 'dashicons-admin-settings',
        'position'    => 80,
        'option_name' => 'my_plugin_options',
    ));
    
    // প্যানেল কনফিগারেশন সেট করুন
    $panel->set_panel_config(array(
        'title'      => __('আমার প্লাগইন সেটিংস', 'textdomain'),
        'logo'       => MY_PLUGIN_URL . 'assets/logo.png',
        'version'    => MY_PLUGIN_VERSION,
        'is_premium' => false,
        'footer_text' => __('© ২০২৪ আমার কোম্পানি', 'textdomain'),
        'resources'  => array(
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
            array(
                'icon'  => 'dashicons dashicons-cart',
                'title' => __('প্রো তে যান', 'textdomain'),
                'url'   => 'https://example.com/pro',
            ),
        ),
        'recommended_plugins' => array(
            array(
                'slug'        => 'elementor',
                'name'        => 'Elementor',
                'description' => __('পেজ বিল্ডার প্লাগইন।', 'textdomain'),
                'thumbnail'   => 'https://ps.w.org/elementor/assets/icon-256x256.gif',
                'author'      => 'Elementor.com',
                'file'        => 'elementor/elementor.php',
                'url'         => 'https://wordpress.org/plugins/elementor/',
            ),
        ),
    ));
    
    // সেকশন যোগ করুন
    $panel->add_section(array(
        'id'     => 'general',
        'title'  => __('সাধারণ', 'textdomain'),
        'icon'   => 'dashicons dashicons-admin-generic',
        'fields' => array(/* ফিল্ড */),
    ));
});
```

## কনফিগারেশনের জন্য ফিল্টার ব্যবহার করা

আপনি প্যানেল কনফিগারেশন মডিফাই করতে WordPress ফিল্টারও ব্যবহার করতে পারেন:

```php
// প্যানেল-নির্দিষ্ট ফিল্টার (প্রস্তাবিত)
add_filter('bizzplugin_panel_config_my_plugin', function($config, $panel_id) {
    $config['logo'] = MY_PLUGIN_URL . 'assets/custom-logo.png';
    $config['is_premium'] = license_is_valid();
    return $config;
}, 10, 2);
```

আরও বিস্তারিত জানতে [ফিল্টার এবং হুক](filters-hooks.md) দেখুন।

---

## পরবর্তী পদক্ষেপ

- [চেইনেবল API](chainable-api.md) - সম্পূর্ণ চেইনেবল API রেফারেন্স
- [ফিল্টার এবং হুক](filters-hooks.md) - ফিল্টার দিয়ে কাস্টমাইজ করুন
- [উদাহরণ](examples.md) - সম্পূর্ণ কোড উদাহরণ
