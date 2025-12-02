# সকল ফিল্ড টাইপ - সম্পূর্ণ রেফারেন্স

BizzPlugin Options Framework এ ব্যবহৃত সকল ফিল্ড টাইপের পূর্ণাঙ্গ ডকুমেন্টেশন। প্রতিটি ফিল্ডের সম্পূর্ণ উদাহরণ এবং সকল প্যারামিটার সহ বিস্তারিত বর্ণনা।

## সূচিপত্র

1. [সাধারণ প্যারামিটার](#সাধারণ-প্যারামিটার)
2. [টেক্সট ফিল্ড (text)](#টেক্সট-ফিল্ড-text)
3. [টেক্সটএরিয়া (textarea)](#টেক্সটএরিয়া-textarea)
4. [নম্বর ফিল্ড (number)](#নম্বর-ফিল্ড-number)
5. [ইমেইল ফিল্ড (email)](#ইমেইল-ফিল্ড-email)
6. [URL ফিল্ড (url)](#url-ফিল্ড-url)
7. [পাসওয়ার্ড ফিল্ড (password)](#পাসওয়ার্ড-ফিল্ড-password)
8. [সিলেক্ট/ড্রপডাউন (select)](#সিলেক্টড্রপডাউন-select)
9. [মাল্টি সিলেক্ট (multi_select)](#মাল্টি-সিলেক্ট-multi_select)
10. [চেকবক্স (checkbox)](#চেকবক্স-checkbox)
11. [চেকবক্স গ্রুপ (checkbox_group)](#চেকবক্স-গ্রুপ-checkbox_group)
12. [রেডিও বাটন (radio)](#রেডিও-বাটন-radio)
13. [সুইচ/টগল (switch/on_off)](#সুইচটগল-switchon_off)
14. [কালার পিকার (color)](#কালার-পিকার-color)
15. [ডেট পিকার (date)](#ডেট-পিকার-date)
16. [স্লাইডার/রেঞ্জ (slider/range)](#স্লাইডাররেঞ্জ-sliderrange)
17. [ইমেজ আপলোড (image)](#ইমেজ-আপলোড-image)
18. [ফাইল আপলোড (file)](#ফাইল-আপলোড-file)
19. [ইমেজ সিলেক্ট (image_select)](#ইমেজ-সিলেক্ট-image_select)
20. [অপশন সিলেক্ট (option_select)](#অপশন-সিলেক্ট-option_select)
21. [পোস্ট সিলেক্ট (post_select)](#পোস্ট-সিলেক্ট-post_select)
22. [রিপিটার (repeater)](#রিপিটার-repeater)
23. [HTML ফিল্ড (html)](#html-ফিল্ড-html)
24. [ইনফো ফিল্ড (info)](#ইনফো-ফিল্ড-info)
25. [লিংক ফিল্ড (link)](#লিংক-ফিল্ড-link)
26. [হেডিং (heading)](#হেডিং-heading)
27. [ডিভাইডার (divider)](#ডিভাইডার-divider)
28. [নোটিশ (notice)](#নোটিশ-notice)
29. [প্লাগইন ফিল্ড (plugins)](#প্লাগইন-ফিল্ড-plugins)
30. [কলব্যাক ফিল্ড (callback)](#কলব্যাক-ফিল্ড-callback)

---

## সাধারণ প্যারামিটার

সব ফিল্ডে এই কমন প্যারামিটারগুলো ব্যবহার করা যায়:

```php
array(
    // আবশ্যক
    'id'                => 'field_id',        // ইউনিক আইডি
    'type'              => 'text',            // ফিল্ড টাইপ
    
    // ঐচ্ছিক
    'title'             => 'ফিল্ড টাইটেল',     // লেবেল
    'description'       => 'বর্ণনা',          // সাহায্যকারী টেক্সট
    'default'           => '',                // ডিফল্ট ভ্যালু
    'class'             => 'custom-class',    // কাস্টম CSS ক্লাস
    'premium'           => false,             // প্রিমিয়াম ফিল্ড কিনা
    'required'          => false,             // বাধ্যতামূলক কিনা
    
    // কলব্যাক
    'sanitize_callback' => 'my_sanitize_fn',  // কাস্টম স্যানিটাইজার
    'validate_callback' => 'my_validate_fn',  // কাস্টম ভ্যালিডেটর
    
    // শর্তসাপেক্ষ প্রদর্শন
    'dependency'        => array(
        'field' => 'other_field_id',
        'value' => 'expected_value',
    ),
)
```

---

## টেক্সট ফিল্ড (text)

সাধারণ একলাইন টেক্সট ইনপুট।

### সিনট্যাক্স

```php
array(
    'id'          => 'site_name',
    'type'        => 'text',
    'title'       => 'সাইটের নাম',
    'description' => 'আপনার সাইটের নাম লিখুন।',
    'default'     => 'আমার ওয়েবসাইট',
    'placeholder' => 'নাম লিখুন...',
    'minlength'   => 3,
    'maxlength'   => 50,
    'pattern'     => '/^[a-zA-Z0-9\s]+$/',
)
```

### প্যারামিটার

| প্যারামিটার | টাইপ | ডিফল্ট | বর্ণনা |
|-------------|------|--------|--------|
| `placeholder` | string | - | ইনপুটের প্লেসহোল্ডার |
| `minlength` | int | - | মিনিমাম ক্যারেক্টার |
| `maxlength` | int | - | ম্যাক্সিমাম ক্যারেক্টার |
| `pattern` | string | - | রেগুলার এক্সপ্রেশন প্যাটার্ন |

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('site_name');
// রিটার্ন: "আমার ওয়েবসাইট" (string)
```

---

## টেক্সটএরিয়া (textarea)

মাল্টি-লাইন টেক্সট ইনপুট।

### সিনট্যাক্স

```php
array(
    'id'          => 'site_description',
    'type'        => 'textarea',
    'title'       => 'সাইটের বর্ণনা',
    'description' => 'সংক্ষিপ্ত বর্ণনা লিখুন।',
    'default'     => '',
    'rows'        => 5,
    'placeholder' => 'বর্ণনা লিখুন...',
)
```

### প্যারামিটার

| প্যারামিটার | টাইপ | ডিফল্ট | বর্ণনা |
|-------------|------|--------|--------|
| `rows` | int | 5 | সারির সংখ্যা |
| `placeholder` | string | - | প্লেসহোল্ডার টেক্সট |

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('site_description');
// রিটার্ন: "বর্ণনা টেক্সট" (string)
```

---

## নম্বর ফিল্ড (number)

সংখ্যা ইনপুট।

### সিনট্যাক্স

```php
array(
    'id'          => 'posts_per_page',
    'type'        => 'number',
    'title'       => 'পোস্ট পার পেজ',
    'description' => 'প্রতি পেজে কতটি পোস্ট দেখাবে।',
    'default'     => 10,
    'min'         => 1,
    'max'         => 100,
    'step'        => 1,
)
```

### প্যারামিটার

| প্যারামিটার | টাইপ | ডিফল্ট | বর্ণনা |
|-------------|------|--------|--------|
| `min` | number | - | মিনিমাম ভ্যালু |
| `max` | number | - | ম্যাক্সিমাম ভ্যালু |
| `step` | number | 1 | স্টেপ ভ্যালু |
| `placeholder` | string | - | প্লেসহোল্ডার |

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('posts_per_page');
// রিটার্ন: 10 (integer)
```

---

## ইমেইল ফিল্ড (email)

ইমেইল অ্যাড্রেস ইনপুট (বিল্ট-ইন ভ্যালিডেশন সহ)।

### সিনট্যাক্স

```php
array(
    'id'          => 'contact_email',
    'type'        => 'email',
    'title'       => 'যোগাযোগের ইমেইল',
    'description' => 'সঠিক ইমেইল দিন।',
    'default'     => get_option('admin_email'),
    'placeholder' => 'email@example.com',
)
```

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('contact_email');
// রিটার্ন: "email@example.com" (string)
```

---

## URL ফিল্ড (url)

ওয়েব অ্যাড্রেস ইনপুট।

### সিনট্যাক্স

```php
array(
    'id'          => 'website_url',
    'type'        => 'url',
    'title'       => 'ওয়েবসাইট URL',
    'description' => 'সম্পূর্ণ URL দিন (https:// সহ)।',
    'default'     => home_url(),
    'placeholder' => 'https://example.com',
)
```

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('website_url');
// রিটার্ন: "https://example.com" (string)
```

---

## পাসওয়ার্ড ফিল্ড (password)

গোপন তথ্য ইনপুট (মাস্কড)।

### সিনট্যাক্স

```php
array(
    'id'          => 'api_secret',
    'type'        => 'password',
    'title'       => 'API সিক্রেট',
    'description' => 'আপনার API সিক্রেট কী।',
    'default'     => '',
    'placeholder' => 'সিক্রেট কী লিখুন...',
)
```

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('api_secret');
// রিটার্ন: "secret_key_value" (string)
```

---

## সিলেক্ট/ড্রপডাউন (select)

ড্রপডাউন মেনু থেকে একটি অপশন বাছাই।

### সিনট্যাক্স

```php
array(
    'id'          => 'layout_style',
    'type'        => 'select',
    'title'       => 'লেআউট স্টাইল',
    'description' => 'পছন্দের লেআউট বাছুন।',
    'default'     => 'full-width',
    'options'     => array(
        'full-width' => 'ফুল উইডথ',
        'boxed'      => 'বক্সড',
        'framed'     => 'ফ্রেমড',
    ),
)
```

### প্যারামিটার

| প্যারামিটার | টাইপ | ডিফল্ট | বর্ণনা |
|-------------|------|--------|--------|
| `options` | array | `array()` | key => label অপশন |

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('layout_style');
// রিটার্ন: "full-width" (string - অপশনের key)
```

---

## মাল্টি সিলেক্ট (multi_select)

একাধিক অপশন সিলেক্ট করা যায়।

### সিনট্যাক্স

```php
array(
    'id'          => 'featured_categories',
    'type'        => 'multi_select',
    'title'       => 'ফিচার্ড ক্যাটাগরি',
    'description' => 'একাধিক ক্যাটাগরি বাছুন।',
    'default'     => array('news', 'blog'),
    'options'     => array(
        'news'       => 'নিউজ',
        'blog'       => 'ব্লগ',
        'tutorials'  => 'টিউটোরিয়াল',
        'reviews'    => 'রিভিউ',
    ),
)
```

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('featured_categories');
// রিটার্ন: array('news', 'blog') (array of strings)
```

---

## চেকবক্স (checkbox)

একক চেকবক্স (হ্যাঁ/না)।

### সিনট্যাক্স

```php
array(
    'id'          => 'enable_comments',
    'type'        => 'checkbox',
    'title'       => 'কমেন্ট সক্রিয়',
    'description' => 'পোস্টে কমেন্ট অনুমোদন করুন।',
    'default'     => '1',
    'label'       => 'হ্যাঁ, কমেন্ট চালু করুন',
)
```

### প্যারামিটার

| প্যারামিটার | টাইপ | ডিফল্ট | বর্ণনা |
|-------------|------|--------|--------|
| `label` | string | - | চেকবক্সের পাশের লেবেল |

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('enable_comments');
// চেক করা: "1" (string)
// আনচেক: "0" (string)

// ব্যবহার
if ($value === '1') {
    // কমেন্ট চালু
}
```

---

## চেকবক্স গ্রুপ (checkbox_group)

একাধিক চেকবক্স একসাথে।

### সিনট্যাক্স

```php
array(
    'id'          => 'active_modules',
    'type'        => 'checkbox_group',
    'title'       => 'সক্রিয় মডিউল',
    'description' => 'কোন মডিউলগুলো চালু থাকবে।',
    'default'     => array('seo', 'cache'),
    'options'     => array(
        'seo'      => 'SEO মডিউল',
        'cache'    => 'ক্যাশ মডিউল',
        'security' => 'সিকিউরিটি মডিউল',
        'backup'   => 'ব্যাকআপ মডিউল',
    ),
)
```

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('active_modules');
// রিটার্ন: array('seo', 'cache') (array of strings)

// ব্যবহার
if (in_array('seo', $value)) {
    // SEO মডিউল সক্রিয়
}
```

---

## রেডিও বাটন (radio)

একটি অপশন সিলেক্ট (রেডিও বাটন স্টাইলে)।

### সিনট্যাক্স

```php
array(
    'id'          => 'theme_mode',
    'type'        => 'radio',
    'title'       => 'থিম মোড',
    'description' => 'থিমের মোড বাছুন।',
    'default'     => 'light',
    'options'     => array(
        'light' => 'লাইট মোড',
        'dark'  => 'ডার্ক মোড',
        'auto'  => 'অটো (সিস্টেম অনুযায়ী)',
    ),
)
```

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('theme_mode');
// রিটার্ন: "light" (string - অপশনের key)
```

---

## সুইচ/টগল (switch/on_off)

সুন্দর টগল সুইচ স্টাইল। `switch` বা `on_off` দুটোই ব্যবহার করা যায়।

### সিনট্যাক্স

```php
array(
    'id'          => 'maintenance_mode',
    'type'        => 'switch', // বা 'on_off'
    'title'       => 'মেইনটেনেন্স মোড',
    'description' => 'সাইট মেইনটেনেন্সে রাখুন।',
    'default'     => '0',
    'on_label'    => 'চালু',
    'off_label'   => 'বন্ধ',
)
```

### প্যারামিটার

| প্যারামিটার | টাইপ | ডিফল্ট | বর্ণনা |
|-------------|------|--------|--------|
| `on_label` | string | "On" | অন অবস্থার লেবেল |
| `off_label` | string | "Off" | অফ অবস্থার লেবেল |

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('maintenance_mode');
// চালু: "1" (string)
// বন্ধ: "0" (string)
```

---

## কালার পিকার (color)

রঙ বাছাই করার ফিল্ড।

### সিনট্যাক্স

```php
array(
    'id'          => 'primary_color',
    'type'        => 'color',
    'title'       => 'প্রাইমারি কালার',
    'description' => 'মূল থিম কালার বাছুন।',
    'default'     => '#2271b1',
)
```

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('primary_color');
// রিটার্ন: "#2271b1" (string - hex color)

// ব্যবহার
echo 'color: ' . esc_attr($value) . ';';
```

---

## ডেট পিকার (date)

তারিখ বাছাই করার ফিল্ড।

### সিনট্যাক্স

```php
array(
    'id'          => 'event_date',
    'type'        => 'date',
    'title'       => 'ইভেন্টের তারিখ',
    'description' => 'ইভেন্ট কবে হবে।',
    'default'     => '',
    'placeholder' => 'তারিখ বাছুন...',
)
```

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('event_date');
// রিটার্ন: "2024-01-15" (string - YYYY-MM-DD format)

// ব্যবহার
$timestamp = strtotime($value);
echo date_i18n('j F Y', $timestamp); // "১৫ জানুয়ারি ২০২৪"
```

---

## স্লাইডার/রেঞ্জ (slider/range)

স্লাইডার দিয়ে সংখ্যা বাছাই। `slider` বা `range` ব্যবহার করা যায়।

### সিনট্যাক্স

```php
array(
    'id'          => 'content_width',
    'type'        => 'slider', // বা 'range'
    'title'       => 'কন্টেন্ট উইডথ',
    'description' => 'কন্টেন্ট এলাকার প্রস্থ।',
    'default'     => 1200,
    'min'         => 800,
    'max'         => 1600,
    'step'        => 10,
    'unit'        => 'px',
)
```

### প্যারামিটার

| প্যারামিটার | টাইপ | ডিফল্ট | বর্ণনা |
|-------------|------|--------|--------|
| `min` | number | 0 | মিনিমাম ভ্যালু |
| `max` | number | 100 | ম্যাক্সিমাম ভ্যালু |
| `step` | number | 1 | স্টেপ ভ্যালু |
| `unit` | string | - | ইউনিট লেবেল (px, %, em) |

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('content_width');
// রিটার্ন: 1200 (integer)

// ব্যবহার
echo 'max-width: ' . intval($value) . 'px;';
```

---

## ইমেজ আপলোড (image)

মিডিয়া লাইব্রেরি থেকে ইমেজ সিলেক্ট।

### সিনট্যাক্স

```php
array(
    'id'          => 'site_logo',
    'type'        => 'image',
    'title'       => 'সাইট লোগো',
    'description' => 'আপনার সাইটের লোগো আপলোড করুন।',
    'default'     => '',
)
```

### সেভ হওয়া ভ্যালু

```php
$attachment_id = bizzplugin_get_option('site_logo');
// রিটার্ন: 123 (integer - Attachment ID)

// ইমেজ URL পেতে
$image_url = wp_get_attachment_image_url($attachment_id, 'full');

// <img> ট্যাগ পেতে
$image_tag = wp_get_attachment_image($attachment_id, 'full');
```

---

## ফাইল আপলোড (file)

যেকোনো ফাইল আপলোড।

### সিনট্যাক্স

```php
array(
    'id'          => 'download_file',
    'type'        => 'file',
    'title'       => 'ডাউনলোড ফাইল',
    'description' => 'PDF বা অন্য ফাইল আপলোড করুন।',
    'default'     => '',
)
```

### সেভ হওয়া ভ্যালু

```php
$attachment_id = bizzplugin_get_option('download_file');
// রিটার্ন: 456 (integer - Attachment ID)

// ফাইল URL পেতে
$file_url = wp_get_attachment_url($attachment_id);

// ফাইল পাথ পেতে
$file_path = get_attached_file($attachment_id);
```

---

## ইমেজ সিলেক্ট (image_select)

ইমেজ দিয়ে অপশন বাছাই (ভিজ্যুয়াল সিলেক্টর)।

### সিনট্যাক্স

```php
array(
    'id'          => 'sidebar_position',
    'type'        => 'image_select',
    'title'       => 'সাইডবার পজিশন',
    'description' => 'লেআউট বাছুন।',
    'default'     => 'right',
    'options'     => array(
        'left'  => plugin_dir_url(__FILE__) . 'images/sidebar-left.svg',
        'none'  => plugin_dir_url(__FILE__) . 'images/no-sidebar.svg',
        'right' => plugin_dir_url(__FILE__) . 'images/sidebar-right.svg',
    ),
)
```

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('sidebar_position');
// রিটার্ন: "right" (string - অপশনের key)
```

---

## অপশন সিলেক্ট (option_select)

টেক্সট দিয়ে অপশন বাছাই (বাটন স্টাইল)।

### সিনট্যাক্স

```php
array(
    'id'          => 'display_mode',
    'type'        => 'option_select',
    'title'       => 'ডিসপ্লে মোড',
    'description' => 'কন্টেন্ট কিভাবে দেখাবে।',
    'default'     => 'grid',
    'options'     => array(
        'grid'     => 'গ্রিড ভিউ',
        'list'     => 'লিস্ট ভিউ',
        'masonry'  => 'ম্যাসনরি',
    ),
)
```

### সেভ হওয়া ভ্যালু

```php
$value = bizzplugin_get_option('display_mode');
// রিটার্ন: "grid" (string - অপশনের key)
```

---

## পোস্ট সিলেক্ট (post_select)

পোস্ট/পেজ/কাস্টম পোস্ট টাইপ থেকে সিলেক্ট।

### সিঙ্গেল সিলেকশন

```php
array(
    'id'          => 'featured_post',
    'type'        => 'post_select',
    'title'       => 'ফিচার্ড পোস্ট',
    'description' => 'হোমপেজে দেখানোর জন্য পোস্ট বাছুন।',
    'post_type'   => 'post',
    'default'     => '',
)
```

### মাল্টিপল সিলেকশন

```php
array(
    'id'          => 'featured_pages',
    'type'        => 'post_select',
    'title'       => 'ফিচার্ড পেজ',
    'description' => 'একাধিক পেজ বাছুন।',
    'post_type'   => 'page',
    'multiple'    => true,
    'default'     => array(),
)
```

### প্যারামিটার

| প্যারামিটার | টাইপ | ডিফল্ট | বর্ণনা |
|-------------|------|--------|--------|
| `post_type` | string | "post" | পোস্ট টাইপ |
| `multiple` | bool | false | মাল্টিপল সিলেকশন |

### সেভ হওয়া ভ্যালু

```php
// সিঙ্গেল
$post_id = bizzplugin_get_option('featured_post');
// রিটার্ন: 42 (integer - Post ID)

// মাল্টিপল
$post_ids = bizzplugin_get_option('featured_pages');
// রিটার্ন: array(10, 20, 30) (array of integers)
```

---

## রিপিটার (repeater)

পুনরাবৃত্তিমূলক ডেটা গ্রুপ।

### সিনট্যাক্স

```php
array(
    'id'          => 'team_members',
    'type'        => 'repeater',
    'title'       => 'টিম মেম্বার',
    'description' => 'টিমের সদস্যদের তথ্য।',
    'button_text' => 'নতুন মেম্বার যোগ করুন',
    'max_items'   => 10,
    'min_items'   => 1,
    'sortable'    => true,
    'allow_add'   => true,
    'fields'      => array(
        array(
            'id'          => 'name',
            'type'        => 'text',
            'title'       => 'নাম',
            'placeholder' => 'পুরো নাম...',
        ),
        array(
            'id'          => 'email',
            'type'        => 'email',
            'title'       => 'ইমেইল',
        ),
        array(
            'id'          => 'position',
            'type'        => 'text',
            'title'       => 'পদবি',
        ),
        array(
            'id'          => 'photo',
            'type'        => 'image',
            'title'       => 'ছবি',
        ),
    ),
    'default'     => array(
        array(
            'name'     => 'জন ডো',
            'email'    => 'john@example.com',
            'position' => 'সিইও',
            'photo'    => '',
        ),
    ),
)
```

### প্যারামিটার

| প্যারামিটার | টাইপ | ডিফল্ট | বর্ণনা |
|-------------|------|--------|--------|
| `fields` | array | `array()` | সাব-ফিল্ডের তালিকা |
| `button_text` | string | "Add Item" | যোগ করার বাটনের টেক্সট |
| `max_items` | int | 0 | সর্বোচ্চ আইটেম (0 = সীমাহীন) |
| `min_items` | int | 0 | ন্যূনতম আইটেম |
| `sortable` | bool | true | ড্র্যাগ করে সাজানো যাবে |
| `allow_add` | bool | true | যোগ/বাদ দেওয়া যাবে |

### সেভ হওয়া ভ্যালু

```php
$members = bizzplugin_get_option('team_members');
// রিটার্ন: array(
//     array('name' => 'জন ডো', 'email' => '...', ...),
//     array('name' => 'জেন ডো', 'email' => '...', ...),
// )

// ব্যবহার
foreach ($members as $member) {
    echo $member['name'];
    echo $member['email'];
}
```

---

## HTML ফিল্ড (html)

কাস্টম HTML কন্টেন্ট (ভ্যালু সেভ হয় না)।

### সিনট্যাক্স

```php
array(
    'id'      => 'custom_notice',
    'type'    => 'html',
    'title'   => 'নোটিশ',
    'content' => '<div class="notice notice-info">
        <p><strong>গুরুত্বপূর্ণ:</strong> এখানে কাস্টম HTML দেখাতে পারেন।</p>
    </div>',
)
```

---

## ইনফো ফিল্ড (info)

তথ্যমূলক বার্তা দেখানোর জন্য।

### সিনট্যাক্স

```php
array(
    'id'          => 'welcome_info',
    'type'        => 'info',
    'title'       => 'স্বাগতম!',
    'description' => 'এই প্যানেল থেকে আপনি সাইটের সেটিংস পরিবর্তন করতে পারবেন।',
)
```

---

## লিংক ফিল্ড (link)

এক্সটার্নাল লিংক দেখানোর জন্য।

### সিনট্যাক্স

```php
array(
    'id'          => 'documentation',
    'type'        => 'link',
    'title'       => 'ডকুমেন্টেশন',
    'description' => 'বিস্তারিত জানতে <a href="https://example.com/docs" target="_blank">ডকুমেন্টেশন</a> দেখুন।',
)
```

---

## হেডিং (heading)

সেকশন হেডিং।

### সিনট্যাক্স

```php
array(
    'id'          => 'section_heading',
    'type'        => 'heading',
    'title'       => 'এডভান্সড সেটিংস',
    'description' => 'নিচের সেটিংসগুলো সাবধানে পরিবর্তন করুন।',
)
```

---

## ডিভাইডার (divider)

ফিল্ডগুলোর মধ্যে বিভাজক রেখা।

### সিনট্যাক্স

```php
array(
    'id'   => 'divider_1',
    'type' => 'divider',
)
```

---

## নোটিশ (notice)

সতর্কতা/তথ্য বার্তা।

### সিনট্যাক্স

```php
array(
    'id'      => 'warning_notice',
    'type'    => 'notice',
    'title'   => 'সতর্কতা',
    'content' => 'এই সেটিংস পরিবর্তন করলে সাইটে প্রভাব পড়বে।',
)
```

---

## প্লাগইন ফিল্ড (plugins)

প্রস্তাবিত প্লাগইন ইনস্টল/অ্যাক্টিভেট।

### সিনট্যাক্স

```php
array(
    'id'          => 'recommended_plugins',
    'type'        => 'plugins',
    'title'       => 'প্রস্তাবিত প্লাগইন',
    'description' => 'এই প্লাগইনগুলো ইনস্টল করলে ভালো হবে।',
    'plugins'     => array(
        array(
            'slug'        => 'woocommerce',
            'name'        => 'WooCommerce',
            'description' => 'ই-কমার্স সলিউশন।',
            'thumbnail'   => 'https://ps.w.org/woocommerce/assets/icon-256x256.png',
            'author'      => 'Automattic',
            'file'        => 'woocommerce/woocommerce.php',
            'url'         => 'https://wordpress.org/plugins/woocommerce/',
        ),
    ),
)
```

---

## কলব্যাক ফিল্ড (callback)

সম্পূর্ণ কাস্টম ফিল্ড রেন্ডার।

### সিনট্যাক্স

```php
array(
    'id'              => 'my_custom_field',
    'type'            => 'callback',
    'title'           => 'কাস্টম ফিল্ড',
    'description'     => 'এটি একটি কাস্টম ফিল্ড।',
    'render_callback' => function($field, $value, $is_disabled) {
        ?>
        <div class="my-custom-field">
            <input 
                type="text" 
                name="<?php echo esc_attr($field['id']); ?>" 
                value="<?php echo esc_attr($value); ?>"
                class="bizzplugin-input"
                <?php echo $is_disabled ? 'disabled' : ''; ?>
            />
        </div>
        <?php
    },
)
```

বিস্তারিত জানতে দেখুন: [কলব্যাক টাইপ](callback-type.md)

---

## শর্তসাপেক্ষ ফিল্ড (Dependency)

অন্য ফিল্ডের ভ্যালুর উপর ভিত্তি করে ফিল্ড দেখান/লুকান।

### সিনট্যাক্স

```php
// প্যারেন্ট ফিল্ড
array(
    'id'      => 'enable_feature',
    'type'    => 'switch',
    'title'   => 'ফিচার সক্রিয়',
    'default' => '0',
),

// ডিপেন্ডেন্ট ফিল্ড
array(
    'id'         => 'feature_settings',
    'type'       => 'select',
    'title'      => 'ফিচার সেটিংস',
    'options'    => array(...),
    'dependency' => array(
        'field' => 'enable_feature',
        'value' => '1',
    ),
)
```

### মাল্টিপল ভ্যালু

```php
'dependency' => array(
    'field' => 'layout_type',
    'value' => 'boxed,framed', // কমা দিয়ে আলাদা
)
```

---

## পরবর্তী পড়ুন

- [কলব্যাক টাইপ](callback-type.md)
- [কলব্যাক ফাংশন](callback-functions.md)
- [স্যানিটাইজ ও ভ্যালিডেশন](sanitize-validation-callbacks.md)
- [নতুন ফিল্ড টাইপ তৈরি](custom-field-type.md)
