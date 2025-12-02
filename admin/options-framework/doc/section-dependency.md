# সেকশান ডিপেন্ডেন্সি (Section Dependency)

BizzPlugin Options Framework-এ সেকশান লেভেলে ডিপেন্ডেন্সি সাপোর্ট আছে। এর মানে হলো কোনো ফিল্ডের মান অনুযায়ী একটা পুরো সেকশান দেখানো বা লুকানো যায়।

## সেকশান ডিপেন্ডেন্সি কী?

যখন একটি সেকশানের দৃশ্যমানতা (visibility) অন্য কোনো ফিল্ডের মানের উপর নির্ভর করে, তখন সেটাকে সেকশান ডিপেন্ডেন্সি বলে।

**উদাহরণ**: "Advanced Settings" সেকশান শুধু তখনই দেখাবে যখন "Enable Advanced Mode" অপশন চালু (on) থাকবে।

## ব্যবহারের পদ্ধতি

### বেসিক সিনট্যাক্স

```php
$panel->add_section(array(
    'id'         => 'advanced_settings',
    'title'      => __('Advanced Settings', 'textdomain'),
    'icon'       => 'dashicons-admin-tools',
    'dependency' => array(
        'field' => 'enable_advanced',  // নির্ভরশীল ফিল্ডের ID
        'value' => '1',                // যে মান থাকলে দেখাবে
    ),
    'fields'     => array(
        // ...ফিল্ডসমূহ
    ),
));
```

### সম্পূর্ণ উদাহরণ

```php
add_action('init', function() {
    $framework = bizzplugin_framework();
    
    $panel = $framework->create_panel(array(
        'id'          => 'my_settings',
        'title'       => __('My Settings', 'textdomain'),
        'option_name' => 'my_options',
    ));
    
    // প্রথম সেকশান - কন্ট্রোল ফিল্ড এখানে
    $panel->add_section(array(
        'id'     => 'general',
        'title'  => __('General Settings', 'textdomain'),
        'icon'   => 'dashicons-admin-generic',
        'fields' => array(
            array(
                'id'      => 'enable_advanced',
                'type'    => 'switch',
                'title'   => __('Enable Advanced Mode', 'textdomain'),
                'default' => '0',
            ),
            array(
                'id'      => 'feature_type',
                'type'    => 'select',
                'title'   => __('Feature Type', 'textdomain'),
                'options' => array(
                    'basic'    => __('Basic', 'textdomain'),
                    'standard' => __('Standard', 'textdomain'),
                    'premium'  => __('Premium', 'textdomain'),
                ),
                'default' => 'basic',
            ),
        ),
    ));
    
    // দ্বিতীয় সেকশান - Switch (on/off) এর উপর নির্ভরশীল
    $panel->add_section(array(
        'id'         => 'advanced',
        'title'      => __('Advanced Settings', 'textdomain'),
        'icon'       => 'dashicons-admin-tools',
        'dependency' => array(
            'field' => 'enable_advanced',
            'value' => '1',  // শুধু যখন switch চালু থাকবে
        ),
        'fields'     => array(
            array(
                'id'      => 'cache_duration',
                'type'    => 'number',
                'title'   => __('Cache Duration', 'textdomain'),
                'default' => 3600,
            ),
            array(
                'id'      => 'debug_mode',
                'type'    => 'switch',
                'title'   => __('Debug Mode', 'textdomain'),
                'default' => '0',
            ),
        ),
    ));
    
    // তৃতীয় সেকশান - Select এর উপর নির্ভরশীল
    $panel->add_section(array(
        'id'         => 'premium_features',
        'title'      => __('Premium Features', 'textdomain'),
        'icon'       => 'dashicons-star-filled',
        'dependency' => array(
            'field' => 'feature_type',
            'value' => 'premium',  // শুধু যখন 'premium' সিলেক্ট করা থাকবে
        ),
        'fields'     => array(
            array(
                'id'      => 'premium_feature_1',
                'type'    => 'switch',
                'title'   => __('Premium Feature 1', 'textdomain'),
                'default' => '0',
            ),
        ),
    ));
});
```

## ডিপেন্ডেন্সি প্রপার্টি

| প্রপার্টি | টাইপ | আবশ্যক | বিবরণ |
|----------|------|--------|-------|
| `field` | string | হ্যাঁ | যে ফিল্ডের উপর নির্ভরশীল সেটার ID |
| `value` | string | হ্যাঁ | যে মান থাকলে সেকশান দেখাবে |

## সমর্থিত ফিল্ড টাইপ

নিম্নলিখিত ফিল্ড টাইপের উপর সেকশান ডিপেন্ডেন্সি কাজ করে:

| ফিল্ড টাইপ | value উদাহরণ |
|-----------|-------------|
| `switch` / `on_off` | `'1'` (চালু), `'0'` (বন্ধ) |
| `checkbox` | `'1'` (চেক করা), `'0'` (আনচেক) |
| `select` | অপশনের মান (যেমন: `'premium'`) |
| `radio` | রেডিও অপশনের মান |

## একাধিক মান সাপোর্ট (Comma-Separated)

একাধিক মানের যেকোনো একটি থাকলে সেকশান দেখানোর জন্য কমা দিয়ে মানগুলো লিখুন:

```php
$panel->add_section(array(
    'id'         => 'advanced_features',
    'title'      => __('Advanced Features', 'textdomain'),
    'dependency' => array(
        'field' => 'feature_type',
        'value' => 'standard,premium',  // 'standard' বা 'premium' যেকোনো একটি হলেই দেখাবে
    ),
    'fields'     => array(
        // ...
    ),
));
```

## কাজ করার পদ্ধতি

1. **প্রাথমিক অবস্থা**: পেজ লোড হওয়ার সময় ডিপেন্ডেন্সি চেক করে সেকশান দেখায় বা লুকায়
2. **রিয়েল-টাইম আপডেট**: ফিল্ডের মান পরিবর্তন হলে সাথে সাথে সেকশান দেখায় বা লুকায়
3. **অটো-নেভিগেশন**: যদি লুকানো সেকশানটি বর্তমানে অ্যাক্টিভ থাকে, স্বয়ংক্রিয়ভাবে প্রথম দৃশ্যমান সেকশানে যাবে

## ফিল্ড বনাম সেকশান ডিপেন্ডেন্সি

| বৈশিষ্ট্য | ফিল্ড ডিপেন্ডেন্সি | সেকশান ডিপেন্ডেন্সি |
|----------|-------------------|---------------------|
| কোথায় যোগ করবেন | ফিল্ড কনফিগারেশনে | সেকশান কনফিগারেশনে |
| কী লুকাবে | একটি ফিল্ড | পুরো সেকশান (নেভিগেশনসহ) |
| সিনট্যাক্স | `'dependency' => [...]` | `'dependency' => [...]` |

### ফিল্ড ডিপেন্ডেন্সি উদাহরণ

```php
array(
    'id'         => 'log_level',
    'type'       => 'select',
    'title'      => __('Log Level', 'textdomain'),
    'dependency' => array(
        'field' => 'debug_mode',
        'value' => '1',
    ),
    'options'    => array(
        'error' => 'Error Only',
        'all'   => 'All Logs',
    ),
)
```

## সীমাবদ্ধতা

1. ডিপেন্ডেন্ট ফিল্ড একই প্যানেলে থাকতে হবে
2. ডিপেন্ডেন্ট ফিল্ড সেকশান থেকে আগে রেন্ডার হতে হবে (সাধারণত প্রথম সেকশানে রাখুন)
3. নেস্টেড ডিপেন্ডেন্সি সাপোর্টেড নয় (A → B → C এর মতো)

## সেরা অনুশীলন (Best Practices)

1. **কন্ট্রোল ফিল্ড প্রথমে রাখুন**: যে ফিল্ডের উপর ডিপেন্ডেন্সি, সেটা প্রথম সেকশানে রাখুন
2. **ক্লিয়ার লেবেল দিন**: ইউজার বুঝতে পারে কোন অপশন চালু করলে কী দেখাবে
3. **ডিফল্ট মান সেট করুন**: ডিপেন্ডেন্ট সেকশানের ফিল্ডে ডিফল্ট মান দিন

## সম্পর্কিত ডকুমেন্টেশন

- [ফিল্ড ডিপেন্ডেন্সি](bd/field-types.md#ফিল্ড-ডিপেন্ডেন্সি)
- [সেকশান কনফিগারেশন](bd/sections-subsections.md)
- [চেইনেবল API](bd/chainable-api.md)
