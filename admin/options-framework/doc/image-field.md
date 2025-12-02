# ইমেজ ফিল্ড (Image Field)

ইমেজ ফিল্ড BizzPlugin Options Framework-এ WordPress মিডিয়া লাইব্রেরি ব্যবহার করে ইমেজ আপলোড ও সিলেক্ট করার জন্য একটি শক্তিশালী ফিল্ড টাইপ। এটি দিয়ে লোগো, ব্যানার, ব্যাকগ্রাউন্ড ইমেজ ইত্যাদি সেট করা যায়।

## বৈশিষ্ট্যসমূহ

- ✅ WordPress মিডিয়া লাইব্রেরি ইন্টিগ্রেশন
- ✅ নতুন ইমেজ আপলোড বা বিদ্যমান থেকে সিলেক্ট
- ✅ ইমেজ প্রিভিউ দেখায়
- ✅ সহজে ইমেজ পরিবর্তন ও মুছে ফেলা যায়
- ✅ অ্যাটাচমেন্ট ID স্টোর করে (URL নয়)
- ✅ সব ইমেজ সাইজ অ্যাক্সেস করা যায়

## প্রপার্টি

| প্রপার্টি | টাইপ | আবশ্যক | বিবরণ |
|----------|------|--------|-------|
| `id` | string | হ্যাঁ | ফিল্ডের জন্য অনন্য আইডেন্টিফায়ার |
| `type` | string | হ্যাঁ | `image` হতে হবে |
| `title` | string | হ্যাঁ | ফিল্ডের টাইটেল |
| `description` | string | না | সাহায্যকারী বিবরণ |
| `default` | string | না | ডিফল্ট অ্যাটাচমেন্ট ID (সাধারণত খালি) |

## মৌলিক উদাহরণ

### সাইট লোগো

```php
array(
    'id'          => 'site_logo',
    'type'        => 'image',
    'title'       => __('সাইট লোগো', 'textdomain'),
    'description' => __('আপনার সাইটের লোগো আপলোড করুন। PNG বা SVG ফরম্যাট সুপারিশ করা হয়।', 'textdomain'),
    'default'     => '',
)
```

### ফেভিকন

```php
array(
    'id'          => 'favicon',
    'type'        => 'image',
    'title'       => __('ফেভিকন', 'textdomain'),
    'description' => __('32x32 পিক্সেলের PNG বা ICO ফাইল আপলোড করুন।', 'textdomain'),
    'default'     => '',
)
```

### ব্যানার ইমেজ

```php
array(
    'id'          => 'hero_banner',
    'type'        => 'image',
    'title'       => __('হিরো ব্যানার', 'textdomain'),
    'description' => __('হোমপেজের হিরো সেকশানের ব্যাকগ্রাউন্ড ইমেজ। সুপারিশকৃত সাইজ: 1920x1080 পিক্সেল।', 'textdomain'),
    'default'     => '',
)
```

## স্টোরেজ ফরম্যাট

ইমেজ ফিল্ড **অ্যাটাচমেন্ট ID** স্টোর করে, ইমেজ URL নয়। এর সুবিধা:

1. **ফ্লেক্সিবিলিটি**: যেকোনো সাইজ পেতে পারবেন
2. **সিকিউরিটি**: URL পরিবর্তন হলেও কাজ করবে
3. **মেটাডেটা**: ইমেজের সব তথ্য পাওয়া যায়

## ডেটা রিট্রিভ ও ব্যবহার

### বেসিক রিট্রিভ

```php
$options = get_option('my_theme_options', array());
$logo_id = isset($options['site_logo']) ? intval($options['site_logo']) : 0;
```

### ইমেজ URL পাওয়া

```php
$options = get_option('my_theme_options', array());
$logo_id = isset($options['site_logo']) ? intval($options['site_logo']) : 0;

// বিভিন্ন সাইজের URL
if ($logo_id) {
    // থাম্বনেইল (150x150)
    $thumbnail_url = wp_get_attachment_image_url($logo_id, 'thumbnail');
    
    // মিডিয়াম (300x300)
    $medium_url = wp_get_attachment_image_url($logo_id, 'medium');
    
    // লার্জ (1024x1024)
    $large_url = wp_get_attachment_image_url($logo_id, 'large');
    
    // ফুল সাইজ (অরিজিনাল)
    $full_url = wp_get_attachment_image_url($logo_id, 'full');
    
    // কাস্টম সাইজ (প্রস্থ, উচ্চতা)
    $custom_url = wp_get_attachment_image_url($logo_id, array(200, 100));
}
```

### ইমেজ ট্যাগ আউটপুট

```php
$options = get_option('my_theme_options', array());
$logo_id = isset($options['site_logo']) ? intval($options['site_logo']) : 0;

if ($logo_id) {
    // সিম্পল ইমেজ ট্যাগ
    echo wp_get_attachment_image($logo_id, 'medium');
    
    // কাস্টম ক্লাস ও অ্যাট্রিবিউটসহ
    echo wp_get_attachment_image($logo_id, 'full', false, array(
        'class' => 'site-logo custom-class',
        'alt'   => get_bloginfo('name') . ' Logo',
    ));
}
```

### রেসপনসিভ ইমেজ (srcset)

```php
$options = get_option('my_theme_options', array());
$banner_id = isset($options['hero_banner']) ? intval($options['hero_banner']) : 0;

if ($banner_id) {
    // WordPress স্বয়ংক্রিয়ভাবে srcset যোগ করে
    echo wp_get_attachment_image($banner_id, 'large', false, array(
        'class' => 'hero-banner',
        'sizes' => '(max-width: 768px) 100vw, 1200px',
    ));
}
```

### ব্যাকগ্রাউন্ড ইমেজ

```php
$options = get_option('my_theme_options', array());
$bg_id = isset($options['hero_banner']) ? intval($options['hero_banner']) : 0;
$bg_url = $bg_id ? wp_get_attachment_image_url($bg_id, 'full') : '';
?>

<div class="hero-section" <?php if ($bg_url) : ?>style="background-image: url('<?php echo esc_url($bg_url); ?>');"<?php endif; ?>>
    <!-- কন্টেন্ট -->
</div>
```

### ইনলাইন CSS

```php
$options = get_option('my_theme_options', array());
$bg_id = isset($options['hero_banner']) ? intval($options['hero_banner']) : 0;

add_action('wp_head', function() use ($bg_id) {
    if ($bg_id) {
        $bg_url = wp_get_attachment_image_url($bg_id, 'full');
        if ($bg_url) {
            echo '<style>
                .hero-section {
                    background-image: url(' . esc_url($bg_url) . ');
                    background-size: cover;
                    background-position: center;
                }
            </style>';
        }
    }
});
```

## ইমেজ মেটাডেটা

```php
$options = get_option('my_theme_options', array());
$image_id = isset($options['featured_image']) ? intval($options['featured_image']) : 0;

if ($image_id) {
    // ইমেজ মেটাডেটা পান
    $image_meta = wp_get_attachment_metadata($image_id);
    
    $width = isset($image_meta['width']) ? $image_meta['width'] : 0;
    $height = isset($image_meta['height']) ? $image_meta['height'] : 0;
    $file = isset($image_meta['file']) ? $image_meta['file'] : '';
    
    // Alt টেক্সট
    $alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
    
    // ক্যাপশন
    $post = get_post($image_id);
    $caption = $post ? $post->post_excerpt : '';
    
    // টাইটেল
    $title = $post ? $post->post_title : '';
}
```

## বাস্তব ব্যবহারের উদাহরণ

### ১. হেডার লোগো থিম

```php
// functions.php বা প্লাগইনে
$options = get_option('my_theme_options', array());
$logo_id = isset($options['site_logo']) ? intval($options['site_logo']) : 0;

function my_custom_logo() {
    global $logo_id;
    
    if ($logo_id) {
        $logo_url = wp_get_attachment_image_url($logo_id, 'medium');
        return '<a href="' . home_url() . '" class="custom-logo-link">
            <img src="' . esc_url($logo_url) . '" alt="' . esc_attr(get_bloginfo('name')) . '" class="custom-logo">
        </a>';
    }
    
    return '<a href="' . home_url() . '" class="site-title">' . get_bloginfo('name') . '</a>';
}
```

### ২. ডিফল্ট ইমেজ ব্যবহার

```php
$options = get_option('my_plugin_options', array());
$placeholder_id = isset($options['default_image']) ? intval($options['default_image']) : 0;

function get_post_thumbnail_or_default($post_id) {
    global $placeholder_id;
    
    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail($post_id, 'medium');
    } elseif ($placeholder_id) {
        return wp_get_attachment_image($placeholder_id, 'medium', false, array(
            'class' => 'placeholder-image',
        ));
    }
    
    return '';
}
```

### ৩. OG ইমেজ (সোশ্যাল শেয়ারিং)

```php
$options = get_option('my_seo_options', array());
$og_image_id = isset($options['og_default_image']) ? intval($options['og_default_image']) : 0;

add_action('wp_head', function() use ($og_image_id) {
    // সিঙ্গেল পোস্টে ফিচার্ড ইমেজ ব্যবহার
    if (is_singular() && has_post_thumbnail()) {
        $og_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
    } elseif ($og_image_id) {
        $og_url = wp_get_attachment_image_url($og_image_id, 'large');
    } else {
        return;
    }
    
    if ($og_url) {
        echo '<meta property="og:image" content="' . esc_url($og_url) . '">';
    }
});
```

## ডিপেন্ডেন্সিসহ ইমেজ ফিল্ড

```php
// সুইচ
array(
    'id'      => 'use_custom_logo',
    'type'    => 'switch',
    'title'   => __('কাস্টম লোগো ব্যবহার করুন', 'textdomain'),
    'default' => '0',
),
// ইমেজ (সুইচ চালু হলে দেখাবে)
array(
    'id'          => 'custom_logo',
    'type'        => 'image',
    'title'       => __('কাস্টম লোগো', 'textdomain'),
    'description' => __('আপনার কাস্টম লোগো আপলোড করুন।', 'textdomain'),
    'dependency'  => array(
        'field' => 'use_custom_logo',
        'value' => '1',
    ),
)
```

## image বনাম file ফিল্ড

| বৈশিষ্ট্য | image | file |
|----------|-------|------|
| ফাইল টাইপ | শুধু ইমেজ | সব ফাইল |
| প্রিভিউ | ইমেজ প্রিভিউ দেখায় | ফাইল নাম দেখায় |
| ব্যবহার | লোগো, ব্যানার, ফটো | PDF, ZIP, ডক |

## গুরুত্বপূর্ণ টিপস

1. **সাইজ গাইডলাইন দিন**: description-এ সুপারিশকৃত ইমেজ সাইজ উল্লেখ করুন
2. **ফরম্যাট উল্লেখ**: কোন ফরম্যাট ভালো হবে (PNG, JPG, SVG) তা জানান
3. **Alt টেক্সট**: ইমেজের জন্য উপযুক্ত alt টেক্সট সেট করুন (SEO এর জন্য)
4. **ফলব্যাক রাখুন**: ইমেজ না থাকলে কী দেখাবে তা ঠিক করুন
5. **সঠিক সাইজ ব্যবহার**: পারফরম্যান্সের জন্য সঠিক ইমেজ সাইজ নির্বাচন করুন

## হেল্পার ফাংশন

```php
/**
 * অপশন থেকে ইমেজ URL পান (ফলব্যাকসহ)
 */
function get_option_image_url($option_name, $field_id, $size = 'full', $fallback = '') {
    $options = get_option($option_name, array());
    $image_id = isset($options[$field_id]) ? intval($options[$field_id]) : 0;
    
    if ($image_id) {
        $url = wp_get_attachment_image_url($image_id, $size);
        if ($url) {
            return $url;
        }
    }
    
    return $fallback;
}

// ব্যবহার
$logo_url = get_option_image_url('my_options', 'site_logo', 'medium', '/path/to/default-logo.png');
```

## সম্পর্কিত ডকুমেন্টেশন

- [ফিল্ড টাইপ](bd/field-types.md)
- [ফাইল ফিল্ড](bd/field-types.md#file)
- [রিপিটার ফিল্ডে ইমেজ](repeater-field.md)
