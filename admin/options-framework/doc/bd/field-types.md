# ফিল্ড টাইপ

এই ডকুমেন্ট BizzPlugin Options Framework-এ সব উপলব্ধ ফিল্ড টাইপের একটি সম্পূর্ণ রেফারেন্স প্রদান করে।

## সাধারণ ফিল্ড প্রপার্টি

সব ফিল্ড টাইপ এই সাধারণ প্রপার্টিগুলো সাপোর্ট করে:

| প্রপার্টি | টাইপ | আবশ্যক | বিবরণ |
|----------|------|--------|-------|
| `id` | string | হ্যাঁ | ফিল্ডের জন্য অনন্য আইডেন্টিফায়ার |
| `type` | string | হ্যাঁ | ফিল্ড টাইপ (নিচে টাইপ দেখুন) |
| `title` | string | হ্যাঁ | ফিল্ডের জন্য প্রদর্শিত লেবেল |
| `description` | string | না | ফিল্ডের নিচে দেখানো সাহায্য টেক্সট |
| `default` | mixed | না | কোন মান সংরক্ষিত না থাকলে ডিফল্ট মান |
| `class` | string | না | অতিরিক্ত CSS ক্লাস |
| `premium` | bool | না | সত্য হলে, ফ্রি ভার্সনে ফিল্ড লক থাকে |
| `dependency` | array | না | শর্তসাপেক্ষ দৃশ্যমানতার নিয়ম |

### ডিপেন্ডেন্সি উদাহরণ

```php
'dependency' => array(
    'field' => 'parent_field_id',  // যে ফিল্ডের উপর নির্ভরশীল তার ID
    'value' => '1',                 // যে মান দৃশ্যমানতা ট্রিগার করবে
),
```

---

## টেক্সট ইনপুট ফিল্ড

### text

সাধারণ টেক্সট ইনপুট ফিল্ড।

```php
array(
    'id'          => 'site_name',
    'type'        => 'text',
    'title'       => __('সাইট নাম', 'textdomain'),
    'description' => __('আপনার সাইটের নাম লিখুন।', 'textdomain'),
    'default'     => 'আমার সাইট',
    'placeholder' => __('টেক্সট লিখুন...', 'textdomain'),
)
```

### email

ভ্যালিডেশন সহ ইমেইল ইনপুট।

```php
array(
    'id'          => 'admin_email',
    'type'        => 'email',
    'title'       => __('অ্যাডমিন ইমেইল', 'textdomain'),
    'description' => __('নোটিফিকেশনের জন্য ইমেইল।', 'textdomain'),
    'default'     => get_option('admin_email'),
    'placeholder' => 'admin@example.com',
)
```

### url

ভ্যালিডেশন সহ URL ইনপুট।

```php
array(
    'id'          => 'website_url',
    'type'        => 'url',
    'title'       => __('ওয়েবসাইট URL', 'textdomain'),
    'description' => __('আপনার ওয়েবসাইট ঠিকানা।', 'textdomain'),
    'default'     => home_url(),
    'placeholder' => 'https://example.com',
)
```

### password

মাস্ক করা অক্ষর সহ পাসওয়ার্ড ইনপুট।

```php
array(
    'id'          => 'api_key',
    'type'        => 'password',
    'title'       => __('API কী', 'textdomain'),
    'description' => __('আপনার API কী লিখুন।', 'textdomain'),
    'default'     => '',
    'placeholder' => __('API কী লিখুন...', 'textdomain'),
)
```

---

## নম্বর ফিল্ড

### number

ঐচ্ছিক min/max/step সহ নম্বর ইনপুট।

```php
array(
    'id'          => 'items_count',
    'type'        => 'number',
    'title'       => __('প্রতি পেজে আইটেম', 'textdomain'),
    'description' => __('প্রদর্শনের জন্য আইটেমের সংখ্যা।', 'textdomain'),
    'default'     => 10,
    'min'         => 1,
    'max'         => 100,
    'step'        => 1,
)
```

### slider / range

একটি রেঞ্জের মধ্যে মান নির্বাচন করার জন্য ভিজ্যুয়াল স্লাইডার।

```php
array(
    'id'          => 'content_width',
    'type'        => 'slider',  // অথবা 'range'
    'title'       => __('কন্টেন্ট প্রস্থ', 'textdomain'),
    'description' => __('পিক্সেলে কন্টেন্ট প্রস্থ সেট করুন।', 'textdomain'),
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

মাল্টি-লাইন টেক্সট ইনপুট।

```php
array(
    'id'          => 'custom_css',
    'type'        => 'textarea',
    'title'       => __('কাস্টম CSS', 'textdomain'),
    'description' => __('কাস্টম CSS কোড যোগ করুন।', 'textdomain'),
    'default'     => '',
    'rows'        => 10,
    'placeholder' => '/* আপনার CSS এখানে */',
)
```

---

## সিলেকশন ফিল্ড

### select

ড্রপডাউন সিলেক্ট ফিল্ড।

```php
array(
    'id'          => 'layout_style',
    'type'        => 'select',
    'title'       => __('লেআউট স্টাইল', 'textdomain'),
    'description' => __('লেআউট স্টাইল নির্বাচন করুন।', 'textdomain'),
    'default'     => 'full-width',
    'options'     => array(
        'full-width' => __('পূর্ণ প্রস্থ', 'textdomain'),
        'boxed'      => __('বক্সড', 'textdomain'),
        'framed'     => __('ফ্রেমড', 'textdomain'),
    ),
)
```

### multi_select

একাধিক সিলেকশন ড্রপডাউন।

```php
array(
    'id'          => 'enabled_features',
    'type'        => 'multi_select',
    'title'       => __('সক্রিয় ফিচার', 'textdomain'),
    'description' => __('সক্রিয় করতে ফিচার নির্বাচন করুন।', 'textdomain'),
    'default'     => array('feature_1', 'feature_2'),
    'options'     => array(
        'feature_1' => __('ফিচার এক', 'textdomain'),
        'feature_2' => __('ফিচার দুই', 'textdomain'),
        'feature_3' => __('ফিচার তিন', 'textdomain'),
    ),
)
```

### radio

রেডিও বাটন সিলেকশন।

```php
array(
    'id'          => 'display_mode',
    'type'        => 'radio',
    'title'       => __('ডিসপ্লে মোড', 'textdomain'),
    'description' => __('ডিসপ্লে মোড নির্বাচন করুন।', 'textdomain'),
    'default'     => 'grid',
    'options'     => array(
        'grid' => __('গ্রিড', 'textdomain'),
        'list' => __('তালিকা', 'textdomain'),
        'masonry' => __('ম্যাসনরি', 'textdomain'),
    ),
)
```

### image_select

ভিজ্যুয়াল ইমেজ-ভিত্তিক সিলেকশন (যেমন লেআউট টেমপ্লেট)।

```php
array(
    'id'          => 'sidebar_layout',
    'type'        => 'image_select',
    'title'       => __('সাইডবার লেআউট', 'textdomain'),
    'description' => __('সাইডবার পজিশন নির্বাচন করুন।', 'textdomain'),
    'default'     => 'sidebar-right',
    'options'     => array(
        'sidebar-left'  => MY_PLUGIN_URL . 'assets/images/sidebar-left.svg',
        'no-sidebar'    => MY_PLUGIN_URL . 'assets/images/no-sidebar.svg',
        'sidebar-right' => MY_PLUGIN_URL . 'assets/images/sidebar-right.svg',
    ),
)
```

### option_select

বাটন-স্টাইল টেক্সট অপশন সিলেকশন (image_select এর মতো কিন্তু টেক্সট সহ)।

```php
array(
    'id'          => 'alignment',
    'type'        => 'option_select',
    'title'       => __('অ্যালাইনমেন্ট', 'textdomain'),
    'description' => __('অ্যালাইনমেন্ট নির্বাচন করুন।', 'textdomain'),
    'default'     => 'center',
    'options'     => array(
        'left'   => __('বাম', 'textdomain'),
        'center' => __('মাঝে', 'textdomain'),
        'right'  => __('ডান', 'textdomain'),
    ),
)
```

### post_select

পোস্ট/পেজ/কাস্টম পোস্ট টাইপ নির্বাচন করুন।

```php
// একক পোস্ট সিলেকশন
array(
    'id'          => 'featured_post',
    'type'        => 'post_select',
    'title'       => __('ফিচার্ড পোস্ট', 'textdomain'),
    'description' => __('ফিচার করার জন্য একটি পোস্ট নির্বাচন করুন।', 'textdomain'),
    'post_type'   => 'post',
    'default'     => '',
)

// একাধিক পেজ সিলেকশন
array(
    'id'          => 'excluded_pages',
    'type'        => 'post_select',
    'title'       => __('বাদ দেওয়া পেজ', 'textdomain'),
    'description' => __('বাদ দিতে পেজ নির্বাচন করুন।', 'textdomain'),
    'post_type'   => 'page',
    'multiple'    => true,
    'default'     => array(),
)
```

---

## টগল/চেকবক্স ফিল্ড

### checkbox

লেবেল সহ একক চেকবক্স।

```php
array(
    'id'          => 'enable_cache',
    'type'        => 'checkbox',
    'title'       => __('ক্যাশ সক্রিয়', 'textdomain'),
    'description' => __('ভালো পারফরম্যান্সের জন্য ক্যাশিং সক্রিয় করুন।', 'textdomain'),
    'default'     => '1',
    'label'       => __('হ্যাঁ, ক্যাশিং সক্রিয় করুন', 'textdomain'),
)
```

### checkbox_group

একাধিক চেকবক্স।

```php
array(
    'id'          => 'enabled_modules',
    'type'        => 'checkbox_group',
    'title'       => __('সক্রিয় মডিউল', 'textdomain'),
    'description' => __('সক্রিয় করতে মডিউল নির্বাচন করুন।', 'textdomain'),
    'default'     => array('module_1', 'module_2'),
    'options'     => array(
        'module_1' => __('মডিউল এক', 'textdomain'),
        'module_2' => __('মডিউল দুই', 'textdomain'),
        'module_3' => __('মডিউল তিন', 'textdomain'),
        'module_4' => __('মডিউল চার', 'textdomain'),
    ),
)
```

### switch / on_off

টগল সুইচ কন্ট্রোল।

```php
array(
    'id'          => 'maintenance_mode',
    'type'        => 'switch',  // অথবা 'on_off'
    'title'       => __('মেইনটেনেন্স মোড', 'textdomain'),
    'description' => __('মেইনটেনেন্স মোড সক্রিয় করুন।', 'textdomain'),
    'default'     => '0',
    'on_label'    => __('সক্রিয়', 'textdomain'),
    'off_label'   => __('নিষ্ক্রিয়', 'textdomain'),
)
```

---

## কালার এবং ডেট ফিল্ড

### color

কালার পিকার ফিল্ড।

```php
array(
    'id'          => 'primary_color',
    'type'        => 'color',
    'title'       => __('প্রাথমিক রঙ', 'textdomain'),
    'description' => __('প্রাথমিক রঙ নির্বাচন করুন।', 'textdomain'),
    'default'     => '#2271b1',
)
```

### date

ডেট পিকার ফিল্ড।

```php
array(
    'id'          => 'start_date',
    'type'        => 'date',
    'title'       => __('শুরুর তারিখ', 'textdomain'),
    'description' => __('শুরুর তারিখ নির্বাচন করুন।', 'textdomain'),
    'default'     => '',
    'placeholder' => __('তারিখ নির্বাচন করুন...', 'textdomain'),
)
```

---

## মিডিয়া ফিল্ড

### image

WordPress মিডিয়া লাইব্রেরি ব্যবহার করে ইমেজ আপলোড ফিল্ড।

```php
array(
    'id'          => 'logo_image',
    'type'        => 'image',
    'title'       => __('লোগো ইমেজ', 'textdomain'),
    'description' => __('আপনার লোগো আপলোড করুন।', 'textdomain'),
    'default'     => '',
)
```

**দ্রষ্টব্য**: সংরক্ষিত মান হল অ্যাটাচমেন্ট ID, URL নয়।

### file

ফাইল আপলোড ফিল্ড।

```php
array(
    'id'          => 'download_file',
    'type'        => 'file',
    'title'       => __('ডাউনলোড ফাইল', 'textdomain'),
    'description' => __('ডাউনলোডের জন্য একটি ফাইল আপলোড করুন।', 'textdomain'),
    'default'     => '',
)
```

**দ্রষ্টব্য**: সংরক্ষিত মান হল অ্যাটাচমেন্ট ID।

---

## কন্টেন্ট/ডিসপ্লে ফিল্ড

### html

কাস্টম HTML কন্টেন্ট (সংরক্ষণযোগ্য নয়)।

```php
array(
    'id'      => 'welcome_message',
    'type'    => 'html',
    'title'   => __('স্বাগতম', 'textdomain'),
    'content' => '<div class="notice">
        <p><strong>স্বাগতম!</strong></p>
        <p>আমাদের প্লাগইন ব্যবহার করার জন্য ধন্যবাদ।</p>
    </div>',
)
```

### info

তথ্য প্রদর্শন ফিল্ড (সাধারণ নোটিশের জন্য উপনাম)।

```php
array(
    'id'          => 'info_message',
    'type'        => 'info',
    'title'       => __('তথ্য', 'textdomain'),
    'description' => __('এটি একটি তথ্যমূলক বার্তা।', 'textdomain'),
)
```

### link

লিঙ্ক প্রদর্শন ফিল্ড।

```php
array(
    'id'          => 'docs_link',
    'type'        => 'link',
    'title'       => __('ডকুমেন্টেশন', 'textdomain'),
    'description' => __('আমাদের <a href="https://example.com/docs" target="_blank">ডকুমেন্টেশন</a> দেখুন।', 'textdomain'),
)
```

### plugins

ইনস্টল/অ্যাক্টিভেট বাটন সহ প্রস্তাবিত প্লাগইন প্রদর্শন।

```php
array(
    'id'      => 'recommended_plugins',
    'type'    => 'plugins',
    'title'   => __('প্রস্তাবিত প্লাগইন', 'textdomain'),
    'plugins' => array(
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
)
```

### callback

রেন্ডারিংয়ের জন্য কাস্টম কলব্যাক।

```php
array(
    'id'              => 'custom_field',
    'type'            => 'callback',
    'title'           => __('কাস্টম ফিল্ড', 'textdomain'),
    'render_callback' => 'my_custom_render_function',
)

function my_custom_render_function($field, $value, $disabled) {
    echo '<div class="custom-field">';
    echo '<input type="text" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '">';
    echo '</div>';
}
```

---

## ফিল্ড ডিপেন্ডেন্সি

যেকোনো ফিল্ড অন্য ফিল্ডের মানের উপর ভিত্তি করে শর্তসাপেক্ষে দেখানো যায়:

```php
array(
    'id'          => 'debug_mode',
    'type'        => 'switch',
    'title'       => __('ডিবাগ মোড', 'textdomain'),
    'default'     => '0',
),
array(
    'id'          => 'log_level',
    'type'        => 'select',
    'title'       => __('লগ লেভেল', 'textdomain'),
    'default'     => 'error',
    'options'     => array(
        'error'   => __('শুধু এরর', 'textdomain'),
        'warning' => __('সতর্কতা', 'textdomain'),
        'info'    => __('সব তথ্য', 'textdomain'),
    ),
    'dependency'  => array(
        'field' => 'debug_mode',
        'value' => '1',
    ),
)
```

`log_level` ফিল্ড শুধুমাত্র `debug_mode` সক্রিয় থাকলে দেখাবে।

---

## প্রিমিয়াম/লক করা ফিল্ড

ফ্রি ভার্সনে লক করতে ফিল্ডগুলো প্রিমিয়াম হিসেবে চিহ্নিত করুন:

```php
array(
    'id'          => 'advanced_feature',
    'type'        => 'switch',
    'title'       => __('অ্যাডভান্সড ফিচার', 'textdomain'),
    'description' => __('প্রিমিয়াম ভার্সনে উপলব্ধ।', 'textdomain'),
    'default'     => '0',
    'premium'     => true,  // এই ফিল্ড লক থাকবে
)
```

প্রিমিয়াম ফিল্ড আনলক করতে আপনার প্যানেল কনফিগারেশনে `is_premium` কে `true` সেট করুন।

---

## পরবর্তী পদক্ষেপ

- [সেকশন এবং সাবসেকশন](sections-subsections.md) - আপনার ফিল্ড সংগঠিত করুন
- [ফিল্টার এবং হুক](filters-hooks.md) - কাস্টম ফিল্ড টাইপ যোগ করুন
- [উদাহরণ](examples.md) - সম্পূর্ণ কোড উদাহরণ
