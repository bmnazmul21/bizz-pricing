# স্যানিটাইজ এবং ভ্যালিডেশন কলব্যাক - সম্পূর্ণ গাইড

BizzPlugin Options Framework এ ডেটা সুরক্ষা এবং সঠিকতা নিশ্চিত করতে sanitize এবং validation callback ব্যবহার করা হয়। এই ডকুমেন্টে এ বিষয়ে বিস্তারিত আলোচনা করা হলো।

## সূচিপত্র

1. [স্যানিটাইজেশন কী?](#স্যানিটাইজেশন-কী)
2. [ভ্যালিডেশন কী?](#ভ্যালিডেশন-কী)
3. [স্যানিটাইজ vs ভ্যালিডেট - পার্থক্য](#স্যানিটাইজ-vs-ভ্যালিডেট---পার্থক্য)
4. [বিল্ট-ইন স্যানিটাইজেশন](#বিল্ট-ইন-স্যানিটাইজেশন)
5. [কাস্টম sanitize_callback](#কাস্টম-sanitize_callback)
6. [কাস্টম validate_callback](#কাস্টম-validate_callback)
7. [ফিল্টার হুক দিয়ে স্যানিটাইজেশন](#ফিল্টার-হুক-দিয়ে-স্যানিটাইজেশন)
8. [বাস্তব উদাহরণ](#বাস্তব-উদাহরণ)
9. [সাধারণ ভুল এবং সমাধান](#সাধারণ-ভুল-এবং-সমাধান)

---

## স্যানিটাইজেশন কী?

স্যানিটাইজেশন হলো ইউজারের দেওয়া ডেটা পরিষ্কার করার প্রক্রিয়া। এটি:

- ক্ষতিকর কোড (XSS, SQL Injection) সরিয়ে দেয়
- অপ্রয়োজনীয় স্পেস, ট্যাগ সরিয়ে দেয়
- ডেটাকে প্রত্যাশিত ফরম্যাটে রূপান্তর করে

### উদাহরণ

```php
// ইউজার ইনপুট: "<script>alert('xss')</script>Hello World"
// স্যানিটাইজ করার পর: "Hello World"
```

---

## ভ্যালিডেশন কী?

ভ্যালিডেশন হলো ডেটা সঠিক কিনা যাচাই করার প্রক্রিয়া। এটি:

- ডেটা প্রত্যাশিত প্যাটার্নে আছে কিনা চেক করে
- রিকোয়ার্ড ফিল্ড খালি নেই তা নিশ্চিত করে
- রেঞ্জ, লেংথ, ফরম্যাট চেক করে
- ব্যর্থ হলে এরর মেসেজ দেখায়

### উদাহরণ

```php
// ইমেইল ভ্যালিডেশন
// ইনপুট: "not-an-email"
// রেজাল্ট: WP_Error("সঠিক ইমেইল দিন")
```

---

## স্যানিটাইজ vs ভ্যালিডেট - পার্থক্য

| বিষয় | Sanitize | Validate |
|-------|----------|----------|
| উদ্দেশ্য | ডেটা পরিষ্কার করা | ডেটা যাচাই করা |
| রিটার্ন | পরিষ্কার ডেটা | true/false/WP_Error |
| চলার সময় | সেভ করার আগে | স্যানিটাইজের পরে |
| ব্যর্থ হলে | ডিফল্ট ভ্যালু সেভ হয় | এরর মেসেজ দেখায় |

### ক্রমানুসারে চলে

```
ইউজার ইনপুট → Sanitize → Validate → সেভ
```

---

## বিল্ট-ইন স্যানিটাইজেশন

Framework স্বয়ংক্রিয়ভাবে ফিল্ড টাইপ অনুযায়ী স্যানিটাইজ করে:

| ফিল্ড টাইপ | স্যানিটাইজার |
|------------|--------------|
| `text` | `sanitize_text_field()` |
| `textarea` | `sanitize_textarea_field()` |
| `email` | `sanitize_email()` |
| `url` | `esc_url_raw()` |
| `number` | `floatval()` + রেঞ্জ চেক |
| `color` | `sanitize_hex_color()` |
| `checkbox`, `switch` | `'1'` অথবা `'0'` |
| `select`, `radio` | অপশন ভ্যালিডেশন |
| `date` | `YYYY-MM-DD` ফরম্যাট |
| `image`, `file` | `absint()` |

### উদাহরণ: বিল্ট-ইন স্যানিটাইজেশন

```php
// Framework এর BizzPlugin_Field_Sanitizer ক্লাস থেকে
switch ($type) {
    case 'text':
        return sanitize_text_field($value);
        
    case 'email':
        return sanitize_email($value);
        
    case 'number':
        $value = floatval($value);
        if (isset($field['min']) && $value < $field['min']) {
            $value = $field['min'];
        }
        if (isset($field['max']) && $value > $field['max']) {
            $value = $field['max'];
        }
        return $value;
        
    case 'checkbox':
    case 'switch':
        return !empty($value) ? '1' : '0';
}
```

---

## কাস্টম sanitize_callback

যখন বিল্ট-ইন স্যানিটাইজেশন যথেষ্ট নয়, তখন কাস্টম কলব্যাক ব্যবহার করুন।

### সিনট্যাক্স

```php
array(
    'id'                => 'my_field',
    'type'              => 'text',
    'title'             => 'আমার ফিল্ড',
    'sanitize_callback' => 'my_sanitize_function', // বা callable
)
```

### উদাহরণ ১: বাংলা ফোন নম্বর স্যানিটাইজ

```php
array(
    'id'                => 'bd_phone',
    'type'              => 'text',
    'title'             => 'বাংলাদেশি ফোন নম্বর',
    'description'       => '০১ দিয়ে শুরু হওয়া ১১ ডিজিট নম্বর।',
    'placeholder'       => '০১XXXXXXXXX',
    'sanitize_callback' => function($value, $field) {
        // সব স্পেস, ড্যাশ সরান
        $clean = preg_replace('/[\s\-\(\)]/', '', $value);
        
        // শুধু নম্বর রাখুন
        $clean = preg_replace('/[^0-9]/', '', $clean);
        
        // +880 থাকলে সরান এবং 0 যোগ করুন
        if (strpos($value, '+880') === 0) {
            $clean = '0' . substr($clean, 3);
        }
        
        // ১১ ডিজিটে সীমিত
        return substr($clean, 0, 11);
    },
)
```

### উদাহরণ ২: HTML ট্যাগ সীমিত রাখা

```php
array(
    'id'                => 'custom_html',
    'type'              => 'textarea',
    'title'             => 'কাস্টম HTML',
    'description'       => 'শুধু <b>, <i>, <a> ট্যাগ গ্রহণযোগ্য।',
    'sanitize_callback' => function($value, $field) {
        // শুধু নির্দিষ্ট ট্যাগ অনুমোদন
        $allowed_tags = array(
            'b'      => array(),
            'i'      => array(),
            'strong' => array(),
            'em'     => array(),
            'a'      => array(
                'href'   => array(),
                'target' => array(),
                'rel'    => array(),
            ),
        );
        
        return wp_kses($value, $allowed_tags);
    },
)
```

### উদাহরণ ৩: JSON স্যানিটাইজ

```php
array(
    'id'                => 'json_settings',
    'type'              => 'textarea',
    'title'             => 'JSON সেটিংস',
    'sanitize_callback' => function($value, $field) {
        // JSON পার্স করার চেষ্টা
        $decoded = json_decode($value, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // ইনভ্যালিড JSON, খালি অবজেক্ট ফেরত দিন
            return '{}';
        }
        
        // রিফরম্যাট এবং ফেরত দিন
        return wp_json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    },
)
```

### উদাহরণ ৪: ফাইল এক্সটেনশন চেক

```php
array(
    'id'                => 'allowed_file',
    'type'              => 'file',
    'title'             => 'ডকুমেন্ট আপলোড',
    'description'       => 'শুধু PDF এবং DOC ফাইল।',
    'sanitize_callback' => function($value, $field) {
        if (empty($value)) {
            return '';
        }
        
        $attachment_id = absint($value);
        $file_path = get_attached_file($attachment_id);
        
        if (!$file_path) {
            return '';
        }
        
        $allowed_extensions = array('pdf', 'doc', 'docx');
        $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowed_extensions)) {
            // অনুমোদিত নয়, খালি ফেরত দিন
            return '';
        }
        
        return $attachment_id;
    },
)
```

---

## কাস্টম validate_callback

ডেটা সঠিক কিনা যাচাই করতে validate_callback ব্যবহার করুন।

### সিনট্যাক্স

```php
array(
    'id'                => 'my_field',
    'type'              => 'text',
    'title'             => 'আমার ফিল্ড',
    'validate_callback' => 'my_validate_function',
)
```

### রিটার্ন ভ্যালু

- `true` - ভ্যালিড
- `false` - ইনভ্যালিড (জেনেরিক এরর দেখাবে)
- `WP_Error` - কাস্টম এরর মেসেজ সহ

### উদাহরণ ১: বাংলাদেশি NID ভ্যালিডেশন

```php
array(
    'id'                => 'nid_number',
    'type'              => 'text',
    'title'             => 'জাতীয় পরিচয়পত্র নম্বর',
    'validate_callback' => function($value, $field) {
        if (empty($value)) {
            return true; // ঐচ্ছিক ফিল্ড
        }
        
        // শুধু সংখ্যা কিনা
        if (!ctype_digit($value)) {
            return new WP_Error(
                'invalid_nid',
                'জাতীয় পরিচয়পত্র নম্বরে শুধু সংখ্যা থাকতে পারে।'
            );
        }
        
        // লেংথ চেক (১০, ১৩, অথবা ১৭ ডিজিট)
        $length = strlen($value);
        if (!in_array($length, array(10, 13, 17))) {
            return new WP_Error(
                'invalid_nid_length',
                'জাতীয় পরিচয়পত্র নম্বর ১০, ১৩, অথবা ১৭ ডিজিট হতে হবে।'
            );
        }
        
        return true;
    },
)
```

### উদাহরণ ২: ইউনিক স্লাগ চেক

```php
array(
    'id'                => 'custom_slug',
    'type'              => 'text',
    'title'             => 'কাস্টম স্লাগ',
    'validate_callback' => function($value, $field) {
        if (empty($value)) {
            return true;
        }
        
        // স্লাগ ফরম্যাট চেক
        if (!preg_match('/^[a-z0-9\-]+$/', $value)) {
            return new WP_Error(
                'invalid_slug_format',
                'স্লাগে শুধু ছোট হাতের অক্ষর, সংখ্যা এবং হাইফেন (-) ব্যবহার করুন।'
            );
        }
        
        // ইতিমধ্যে ব্যবহৃত কিনা চেক
        global $wpdb;
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s LIMIT 1",
            $value
        ));
        
        if ($existing) {
            return new WP_Error(
                'slug_exists',
                'এই স্লাগ ইতিমধ্যে ব্যবহৃত হয়েছে। অন্য স্লাগ দিন।'
            );
        }
        
        return true;
    },
)
```

### উদাহরণ ৩: ইমেজ ডাইমেনশন চেক

```php
array(
    'id'                => 'banner_image',
    'type'              => 'image',
    'title'             => 'ব্যানার ইমেজ',
    'description'       => 'মিনিমাম ১২০০x৩০০ পিক্সেল।',
    'validate_callback' => function($value, $field) {
        if (empty($value)) {
            return true;
        }
        
        $attachment_id = absint($value);
        $image_data = wp_get_attachment_image_src($attachment_id, 'full');
        
        if (!$image_data) {
            return new WP_Error('invalid_image', 'সঠিক ইমেজ সিলেক্ট করুন।');
        }
        
        $width = $image_data[1];
        $height = $image_data[2];
        
        if ($width < 1200) {
            return new WP_Error(
                'image_too_small',
                sprintf('ইমেজের প্রস্থ কমপক্ষে ১২০০ পিক্সেল হতে হবে। বর্তমান: %d পিক্সেল।', $width)
            );
        }
        
        if ($height < 300) {
            return new WP_Error(
                'image_too_short',
                sprintf('ইমেজের উচ্চতা কমপক্ষে ৩০০ পিক্সেল হতে হবে। বর্তমান: %d পিক্সেল।', $height)
            );
        }
        
        return true;
    },
)
```

### উদাহরণ ৪: তারিখ রেঞ্জ চেক

```php
array(
    'id'                => 'event_date',
    'type'              => 'date',
    'title'             => 'ইভেন্টের তারিখ',
    'description'       => 'আজ থেকে এক বছরের মধ্যে হতে হবে।',
    'validate_callback' => function($value, $field) {
        if (empty($value)) {
            return true;
        }
        
        $timestamp = strtotime($value);
        $today = strtotime('today');
        $one_year_later = strtotime('+1 year');
        
        if ($timestamp < $today) {
            return new WP_Error(
                'past_date',
                'অতীতের তারিখ দেওয়া যাবে না। আজ বা ভবিষ্যতের তারিখ দিন।'
            );
        }
        
        if ($timestamp > $one_year_later) {
            return new WP_Error(
                'too_far',
                'তারিখ আজ থেকে এক বছরের মধ্যে হতে হবে।'
            );
        }
        
        return true;
    },
)
```

---

## ফিল্টার হুক দিয়ে স্যানিটাইজেশন

গ্লোবালি সব ফিল্ডে স্যানিটাইজেশন প্রয়োগ করতে ফিল্টার হুক ব্যবহার করুন।

### নির্দিষ্ট ফিল্ড টাইপের জন্য

```php
// সব টেক্সট ফিল্ডে trim() প্রয়োগ
add_filter('bizzplugin_sanitize_field_text', function($value, $field) {
    return trim(sanitize_text_field($value));
}, 10, 2);

// সব URL ফিল্ডে trailing slash সরান
add_filter('bizzplugin_sanitize_field_url', function($value, $field) {
    return rtrim(esc_url_raw($value), '/');
}, 10, 2);
```

### সব ফিল্ডের জন্য

```php
add_filter('bizzplugin_sanitize_field', function($value, $field, $type) {
    // XSS প্রটেকশন (স্ট্রিং ভ্যালুর জন্য)
    if (is_string($value)) {
        $value = wp_strip_all_tags($value);
    }
    
    return $value;
}, 10, 3);
```

---

## বাস্তব উদাহরণ

### সম্পূর্ণ প্রোডাক্ট ফর্ম

```php
$panel->add_section(array(
    'id'     => 'product_settings',
    'title'  => 'প্রোডাক্ট সেটিংস',
    'fields' => array(
        // প্রোডাক্ট নাম
        array(
            'id'                => 'product_name',
            'type'              => 'text',
            'title'             => 'প্রোডাক্ট নাম',
            'required'          => true,
            'sanitize_callback' => function($value, $field) {
                return sanitize_text_field(trim($value));
            },
            'validate_callback' => function($value, $field) {
                if (strlen($value) < 3) {
                    return new WP_Error('name_too_short', 'প্রোডাক্ট নাম কমপক্ষে ৩ অক্ষর হতে হবে।');
                }
                if (strlen($value) > 100) {
                    return new WP_Error('name_too_long', 'প্রোডাক্ট নাম ১০০ অক্ষরের বেশি হতে পারবে না।');
                }
                return true;
            },
        ),
        
        // দাম
        array(
            'id'                => 'product_price',
            'type'              => 'number',
            'title'             => 'দাম (টাকা)',
            'min'               => 0,
            'step'              => 0.01,
            'sanitize_callback' => function($value, $field) {
                $price = floatval($value);
                return round($price, 2);
            },
            'validate_callback' => function($value, $field) {
                if ($value < 0) {
                    return new WP_Error('negative_price', 'দাম নেগেটিভ হতে পারবে না।');
                }
                if ($value > 1000000) {
                    return new WP_Error('price_too_high', 'দাম ১০ লাখের বেশি হতে পারবে না।');
                }
                return true;
            },
        ),
        
        // SKU
        array(
            'id'                => 'product_sku',
            'type'              => 'text',
            'title'             => 'SKU',
            'placeholder'       => 'PROD-001',
            'sanitize_callback' => function($value, $field) {
                return strtoupper(sanitize_title($value));
            },
            'validate_callback' => function($value, $field) {
                if (!empty($value) && !preg_match('/^[A-Z0-9\-]+$/', $value)) {
                    return new WP_Error('invalid_sku', 'SKU তে শুধু বড় হাতের অক্ষর, সংখ্যা এবং হাইফেন ব্যবহার করুন।');
                }
                return true;
            },
        ),
        
        // স্টক
        array(
            'id'                => 'product_stock',
            'type'              => 'number',
            'title'             => 'স্টক পরিমাণ',
            'min'               => 0,
            'sanitize_callback' => function($value, $field) {
                return absint($value);
            },
        ),
        
        // বিবরণ
        array(
            'id'                => 'product_description',
            'type'              => 'textarea',
            'title'             => 'বিবরণ',
            'rows'              => 5,
            'sanitize_callback' => function($value, $field) {
                // শুধু বেসিক ফরম্যাটিং অনুমোদন
                $allowed = array(
                    'b'  => array(),
                    'i'  => array(),
                    'br' => array(),
                    'p'  => array(),
                );
                return wp_kses($value, $allowed);
            },
            'validate_callback' => function($value, $field) {
                if (strlen(strip_tags($value)) > 1000) {
                    return new WP_Error('desc_too_long', 'বিবরণ ১০০০ অক্ষরের বেশি হতে পারবে না।');
                }
                return true;
            },
        ),
    ),
));
```

---

## সাধারণ ভুল এবং সমাধান

### ভুল ১: রিটার্ন না করা

```php
// ভুল
'sanitize_callback' => function($value, $field) {
    sanitize_text_field($value); // রিটার্ন নেই!
}

// সঠিক
'sanitize_callback' => function($value, $field) {
    return sanitize_text_field($value);
}
```

### ভুল ২: Empty চেক না করা

```php
// ভুল
'validate_callback' => function($value, $field) {
    // খালি ভ্যালুতেও চলবে এবং ভুল এরর দেবে
    if (strlen($value) < 3) {
        return new WP_Error('too_short', 'খুব ছোট');
    }
    return true;
}

// সঠিক
'validate_callback' => function($value, $field) {
    if (empty($value)) {
        return true; // ঐচ্ছিক ফিল্ড হলে
    }
    if (strlen($value) < 3) {
        return new WP_Error('too_short', 'কমপক্ষে ৩ অক্ষর দিন');
    }
    return true;
}
```

### ভুল ৩: WP_Error সঠিকভাবে না ব্যবহার করা

```php
// ভুল
'validate_callback' => function($value, $field) {
    if (invalid) {
        return "এরর মেসেজ"; // স্ট্রিং রিটার্ন truthy হিসেবে ধরা হবে
    }
}

// সঠিক
'validate_callback' => function($value, $field) {
    if (invalid) {
        return new WP_Error('error_code', 'এরর মেসেজ');
    }
    return true;
}
```

---

## পরবর্তী পড়ুন

- [নতুন ফিল্ড টাইপ তৈরি](custom-field-type.md)
- [সকল ফিল্ড টাইপ](all-field-types.md)
- [কলব্যাক টাইপ](callback-type.md)
