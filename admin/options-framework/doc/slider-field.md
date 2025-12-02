# স্লাইডার ফিল্ড (Slider/Range Field)

স্লাইডার ফিল্ড BizzPlugin Options Framework-এ একটি ভিজ্যুয়াল ইনপুট ফিল্ড যা দিয়ে ইউজার একটি নির্দিষ্ট রেঞ্জের মধ্যে সহজেই মান সিলেক্ট করতে পারে। এটি পিক্সেল, পার্সেন্টেজ, দিন, সময় ইত্যাদি নিউমেরিক ভ্যালুর জন্য আদর্শ।

## বৈশিষ্ট্যসমূহ

- ✅ ড্র্যাগ করে বা ক্লিক করে মান সিলেক্ট
- ✅ সর্বনিম্ন ও সর্বোচ্চ মান সেট করা যায়
- ✅ স্টেপ সাইজ কাস্টমাইজ করা যায়
- ✅ ইউনিট দেখানো যায় (px, %, s ইত্যাদি)
- ✅ রিয়েল-টাইম ভ্যালু ডিসপ্লে
- ✅ ভিজ্যুয়াল প্রোগ্রেস বার

## প্রপার্টি

| প্রপার্টি | টাইপ | আবশ্যক | বিবরণ |
|----------|------|--------|-------|
| `id` | string | হ্যাঁ | ফিল্ডের জন্য অনন্য আইডেন্টিফায়ার |
| `type` | string | হ্যাঁ | `slider` বা `range` হতে হবে |
| `title` | string | হ্যাঁ | ফিল্ডের টাইটেল |
| `description` | string | না | সাহায্যকারী বিবরণ |
| `default` | number | না | ডিফল্ট মান |
| `min` | number | না | সর্বনিম্ন মান (ডিফল্ট: 0) |
| `max` | number | না | সর্বোচ্চ মান (ডিফল্ট: 100) |
| `step` | number | না | স্টেপ সাইজ (ডিফল্ট: 1) |
| `unit` | string | না | ইউনিট (যেমন: px, %, s) |

## মৌলিক উদাহরণ

### সাধারণ স্লাইডার

```php
array(
    'id'          => 'opacity_level',
    'type'        => 'slider',
    'title'       => __('অপাসিটি লেভেল', 'textdomain'),
    'description' => __('কন্টেন্টের অপাসিটি সেট করুন।', 'textdomain'),
    'default'     => 100,
    'min'         => 0,
    'max'         => 100,
    'step'        => 1,
    'unit'        => '%',
)
```

### পিক্সেল ইউনিটসহ

```php
array(
    'id'          => 'content_width',
    'type'        => 'slider',
    'title'       => __('কন্টেন্ট প্রস্থ', 'textdomain'),
    'description' => __('মেইন কন্টেন্ট এরিয়ার প্রস্থ সেট করুন।', 'textdomain'),
    'default'     => 1200,
    'min'         => 800,
    'max'         => 1920,
    'step'        => 10,
    'unit'        => 'px',
)
```

### দশমিক স্টেপসহ

```php
array(
    'id'          => 'animation_speed',
    'type'        => 'slider',
    'title'       => __('অ্যানিমেশন স্পিড', 'textdomain'),
    'description' => __('অ্যানিমেশনের গতি সেকেন্ডে।', 'textdomain'),
    'default'     => 0.3,
    'min'         => 0.1,
    'max'         => 2.0,
    'step'        => 0.1,
    'unit'        => 's',
)
```

## বাস্তব ব্যবহারের উদাহরণ

### ১. ফন্ট সাইজ কন্ট্রোল

```php
array(
    'id'          => 'body_font_size',
    'type'        => 'slider',
    'title'       => __('বডি ফন্ট সাইজ', 'textdomain'),
    'description' => __('বেস ফন্ট সাইজ নির্বাচন করুন।', 'textdomain'),
    'default'     => 16,
    'min'         => 12,
    'max'         => 24,
    'step'        => 1,
    'unit'        => 'px',
)
```

### ২. লাইন হাইট কন্ট্রোল

```php
array(
    'id'          => 'line_height',
    'type'        => 'slider',
    'title'       => __('লাইন হাইট', 'textdomain'),
    'description' => __('টেক্সট লাইনের উচ্চতা।', 'textdomain'),
    'default'     => 1.6,
    'min'         => 1.0,
    'max'         => 3.0,
    'step'        => 0.1,
    'unit'        => '',
)
```

### ৩. বর্ডার রেডিয়াস

```php
array(
    'id'          => 'border_radius',
    'type'        => 'slider',
    'title'       => __('বর্ডার রেডিয়াস', 'textdomain'),
    'description' => __('উপাদানের কোণার গোলাকার পরিমাণ।', 'textdomain'),
    'default'     => 4,
    'min'         => 0,
    'max'         => 50,
    'step'        => 1,
    'unit'        => 'px',
)
```

### ৪. ক্যাশ সময়কাল

```php
array(
    'id'          => 'cache_duration',
    'type'        => 'slider',
    'title'       => __('ক্যাশ সময়কাল', 'textdomain'),
    'description' => __('ক্যাশ কতক্ষণ সংরক্ষিত থাকবে।', 'textdomain'),
    'default'     => 24,
    'min'         => 1,
    'max'         => 168,  // 7 দিন
    'step'        => 1,
    'unit'        => 'ঘন্টা',
)
```

### ৫. প্রতি পেজে আইটেম

```php
array(
    'id'          => 'items_per_page',
    'type'        => 'slider',
    'title'       => __('প্রতি পেজে আইটেম', 'textdomain'),
    'description' => __('একটি পেজে কতটি আইটেম দেখাবে।', 'textdomain'),
    'default'     => 10,
    'min'         => 5,
    'max'         => 50,
    'step'        => 5,
    'unit'        => '',
)
```

## ডেটা রিট্রিভ ও ব্যবহার

### বেসিক রিট্রিভ

```php
$options = get_option('my_options', array());
$content_width = isset($options['content_width']) ? intval($options['content_width']) : 1200;
```

### CSS হিসেবে ব্যবহার

```php
// অপশন পড়ুন
$options = get_option('my_theme_options', array());
$content_width = isset($options['content_width']) ? intval($options['content_width']) : 1200;
$font_size = isset($options['body_font_size']) ? intval($options['body_font_size']) : 16;
$border_radius = isset($options['border_radius']) ? intval($options['border_radius']) : 4;

// ইনলাইন CSS জেনারেট
add_action('wp_head', function() use ($content_width, $font_size, $border_radius) {
    echo '<style>
        .container {
            max-width: ' . esc_attr($content_width) . 'px;
        }
        body {
            font-size: ' . esc_attr($font_size) . 'px;
        }
        .button, .card {
            border-radius: ' . esc_attr($border_radius) . 'px;
        }
    </style>';
});
```

### PHP লজিকে ব্যবহার

```php
$options = get_option('my_plugin_options', array());

// ক্যাশ সময়কাল (ঘন্টা থেকে সেকেন্ডে)
$cache_hours = isset($options['cache_duration']) ? intval($options['cache_duration']) : 24;
$cache_seconds = $cache_hours * 3600;

// ক্যাশ সেট করা
set_transient('my_cached_data', $data, $cache_seconds);

// প্রতি পেজে আইটেম
$per_page = isset($options['items_per_page']) ? intval($options['items_per_page']) : 10;

$query = new WP_Query(array(
    'posts_per_page' => $per_page,
    // ...
));
```

## টাইপ অ্যালিয়াস

`slider` এবং `range` দুটি টাইপই একই কাজ করে। আপনার পছন্দমতো যেকোনোটি ব্যবহার করতে পারেন:

```php
// এই দুটি সমান
'type' => 'slider'
'type' => 'range'
```

## ডিপেন্ডেন্সিসহ স্লাইডার

```php
// সুইচ ফিল্ড
array(
    'id'      => 'enable_custom_width',
    'type'    => 'switch',
    'title'   => __('কাস্টম প্রস্থ সক্রিয়', 'textdomain'),
    'default' => '0',
),
// স্লাইডার (সুইচ চালু হলে দেখাবে)
array(
    'id'          => 'custom_width',
    'type'        => 'slider',
    'title'       => __('কাস্টম প্রস্থ', 'textdomain'),
    'default'     => 1200,
    'min'         => 600,
    'max'         => 1920,
    'step'        => 10,
    'unit'        => 'px',
    'dependency'  => array(
        'field' => 'enable_custom_width',
        'value' => '1',
    ),
)
```

## number ফিল্ড বনাম slider ফিল্ড

| বৈশিষ্ট্য | number | slider |
|----------|--------|--------|
| ইনপুট মোড | টাইপ করে | ড্র্যাগ/ক্লিক করে |
| সুনির্দিষ্ট মান | হ্যাঁ (যেকোনো) | হ্যাঁ (step অনুযায়ী) |
| ভিজ্যুয়াল ফিডব্যাক | না | হ্যাঁ |
| ব্যবহার | সুনির্দিষ্ট সংখ্যা | রেঞ্জের মধ্যে মান |

## গুরুত্বপূর্ণ টিপস

1. **সঠিক রেঞ্জ নির্বাচন**: min ও max এমনভাবে সেট করুন যেন বাস্তবসম্মত মান পাওয়া যায়
2. **উপযুক্ত step**: খুব বড় রেঞ্জে বড় step ব্যবহার করুন (যেমন: 0-1000 এ step 10)
3. **ইউনিট ব্যবহার**: ইউনিট থাকলে ইউজার বুঝতে পারে এটা কী ধরনের মান
4. **ডিফল্ট মান**: সবসময় একটি যুক্তিসঙ্গত ডিফল্ট মান দিন

## সম্পর্কিত ডকুমেন্টেশন

- [ফিল্ড টাইপ](bd/field-types.md)
- [ফিল্ড ডিপেন্ডেন্সি](bd/field-types.md#ফিল্ড-ডিপেন্ডেন্সি)
- [CSS জেনারেশন গাইড](bd/examples.md)
