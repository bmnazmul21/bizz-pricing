# কলব্যাক ফাংশন (Callback Functions) - বিস্তারিত গাইড

BizzPlugin Options Framework এ বিভিন্ন ধরনের কলব্যাক ফাংশন ব্যবহার করা যায়। এই ডকুমেন্টে সকল কলব্যাক ফাংশনের বিস্তারিত আলোচনা করা হলো।

## সূচিপত্র

1. [কলব্যাক ফাংশনের প্রকারভেদ](#কলব্যাক-ফাংশনের-প্রকারভেদ)
2. [render_callback](#render_callback)
3. [sanitize_callback](#sanitize_callback)
4. [validate_callback](#validate_callback)
5. [ফিল্টার হুক কলব্যাক](#ফিল্টার-হুক-কলব্যাক)
6. [অ্যাকশন হুক কলব্যাক](#অ্যাকশন-হুক-কলব্যাক)
7. [সেরা অভ্যাস](#সেরা-অভ্যাস)

---

## কলব্যাক ফাংশনের প্রকারভেদ

Framework এ তিন ধরনের প্রধান কলব্যাক আছে:

| কলব্যাক | কাজ | কখন ব্যবহার হয় |
|---------|-----|-----------------|
| `render_callback` | কাস্টম ফিল্ড রেন্ডার করে | ফিল্ড দেখানোর সময় |
| `sanitize_callback` | ভ্যালু পরিষ্কার করে | সেভ করার আগে |
| `validate_callback` | ভ্যালু যাচাই করে | সেভ করার আগে |

---

## render_callback

`render_callback` ব্যবহার করে আপনি কাস্টম HTML দিয়ে ফিল্ড রেন্ডার করতে পারেন।

### সিনট্যাক্স

```php
array(
    'id'              => 'my_field',
    'type'            => 'callback',
    'title'           => 'কাস্টম ফিল্ড',
    'render_callback' => 'my_render_function',
)
```

### ফাংশন সিগনেচার

```php
function my_render_function($field, $value, $is_disabled) {
    // $field - ফিল্ড কনফিগারেশন অ্যারে
    // $value - বর্তমান সেভ করা ভ্যালু
    // $is_disabled - প্রিমিয়াম লক থাকলে true
}
```

### উদাহরণ: Google Map Picker

```php
// ফিল্ড ডেফিনিশন
array(
    'id'              => 'store_location',
    'type'            => 'callback',
    'title'           => 'স্টোরের লোকেশন',
    'description'     => 'ম্যাপে ক্লিক করে লোকেশন সিলেক্ট করুন।',
    'default'         => array('lat' => 23.8103, 'lng' => 90.4125),
    'render_callback' => 'render_map_picker',
)

// রেন্ডার ফাংশন
function render_map_picker($field, $value, $is_disabled) {
    $lat = isset($value['lat']) ? $value['lat'] : 23.8103;
    $lng = isset($value['lng']) ? $value['lng'] : 90.4125;
    $disabled = $is_disabled ? ' disabled' : '';
    ?>
    <div class="map-picker-field">
        <input 
            type="hidden" 
            name="<?php echo esc_attr($field['id']); ?>[lat]" 
            value="<?php echo esc_attr($lat); ?>"
            class="lat-input"
            <?php echo $disabled; ?>
        />
        <input 
            type="hidden" 
            name="<?php echo esc_attr($field['id']); ?>[lng]" 
            value="<?php echo esc_attr($lng); ?>"
            class="lng-input"
            <?php echo $disabled; ?>
        />
        <div id="map-<?php echo esc_attr($field['id']); ?>" class="map-container" style="height: 300px; border: 1px solid #ddd;"></div>
        <p class="coordinates">
            অক্ষাংশ: <span class="lat-display"><?php echo esc_html($lat); ?></span>, 
            দ্রাঘিমাংশ: <span class="lng-display"><?php echo esc_html($lng); ?></span>
        </p>
    </div>
    <?php
}
```

### উদাহরণ: Color Gradient Picker

```php
// ফিল্ড ডেফিনিশন
array(
    'id'              => 'gradient_colors',
    'type'            => 'callback',
    'title'           => 'গ্রেডিয়েন্ট কালার',
    'description'     => 'দুটি কালার সিলেক্ট করুন।',
    'render_callback' => 'render_gradient_picker',
)

// রেন্ডার ফাংশন
function render_gradient_picker($field, $value, $is_disabled) {
    $color1 = isset($value['color1']) ? $value['color1'] : '#ff0000';
    $color2 = isset($value['color2']) ? $value['color2'] : '#0000ff';
    $disabled = $is_disabled ? ' disabled="disabled"' : '';
    ?>
    <div class="gradient-picker">
        <div class="color-inputs">
            <div class="color-input-group">
                <label>প্রথম কালার:</label>
                <input 
                    type="text" 
                    name="<?php echo esc_attr($field['id']); ?>[color1]" 
                    value="<?php echo esc_attr($color1); ?>"
                    class="bizzplugin-color-picker"
                    <?php echo $disabled; ?>
                />
            </div>
            <div class="color-input-group">
                <label>দ্বিতীয় কালার:</label>
                <input 
                    type="text" 
                    name="<?php echo esc_attr($field['id']); ?>[color2]" 
                    value="<?php echo esc_attr($color2); ?>"
                    class="bizzplugin-color-picker"
                    <?php echo $disabled; ?>
                />
            </div>
        </div>
        <div class="gradient-preview" style="background: linear-gradient(to right, <?php echo esc_attr($color1); ?>, <?php echo esc_attr($color2); ?>); height: 50px; border-radius: 4px; margin-top: 10px;"></div>
    </div>
    <?php
}
```

---

## sanitize_callback

`sanitize_callback` ব্যবহার করে ভ্যালু সেভ করার আগে পরিষ্কার করা হয়।

### সিনট্যাক্স

```php
array(
    'id'                => 'my_field',
    'type'              => 'text',
    'title'             => 'আমার ফিল্ড',
    'sanitize_callback' => 'my_sanitize_function',
)
```

### ফাংশন সিগনেচার

```php
function my_sanitize_function($value, $field) {
    // $value - ইউজারের দেওয়া ভ্যালু
    // $field - ফিল্ড কনফিগারেশন অ্যারে
    // return - পরিষ্কার করা ভ্যালু
}
```

### উদাহরণসমূহ

#### উদাহরণ ১: ফোন নম্বর স্যানিটাইজ

```php
array(
    'id'                => 'phone_number',
    'type'              => 'text',
    'title'             => 'ফোন নম্বর',
    'sanitize_callback' => function($value, $field) {
        // শুধু নম্বর রাখুন
        $clean = preg_replace('/[^0-9+]/', '', $value);
        return sanitize_text_field($clean);
    },
)
```

#### উদাহরণ ২: স্লাগ স্যানিটাইজ

```php
array(
    'id'                => 'custom_slug',
    'type'              => 'text',
    'title'             => 'কাস্টম স্লাগ',
    'sanitize_callback' => function($value, $field) {
        // স্লাগ ফরম্যাটে রূপান্তর
        return sanitize_title($value);
    },
)
```

#### উদাহরণ ৩: Uppercase টেক্সট

```php
array(
    'id'                => 'country_code',
    'type'              => 'text',
    'title'             => 'কান্ট্রি কোড',
    'sanitize_callback' => function($value, $field) {
        // বড় হাতের অক্ষরে এবং ২ ক্যারেক্টারে সীমিত
        $clean = sanitize_text_field($value);
        return strtoupper(substr($clean, 0, 2));
    },
)
```

#### উদাহরণ ৪: JSON ভ্যালু স্যানিটাইজ

```php
array(
    'id'                => 'json_config',
    'type'              => 'textarea',
    'title'             => 'JSON কনফিগ',
    'sanitize_callback' => function($value, $field) {
        // JSON ভ্যালিড কিনা চেক করুন
        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            // ভ্যালিড JSON, ফরম্যাট করে ফেরত দিন
            return wp_json_encode($decoded, JSON_PRETTY_PRINT);
        }
        // ইনভ্যালিড হলে খালি স্ট্রিং
        return '';
    },
)
```

#### উদাহরণ ৫: Array ভ্যালু স্যানিটাইজ

```php
array(
    'id'                => 'custom_array_field',
    'type'              => 'callback',
    'title'             => 'কাস্টম অ্যারে',
    'sanitize_callback' => function($value, $field) {
        if (!is_array($value)) {
            return array();
        }
        
        $sanitized = array();
        foreach ($value as $key => $val) {
            $sanitized[sanitize_key($key)] = sanitize_text_field($val);
        }
        return $sanitized;
    },
)
```

---

## validate_callback

`validate_callback` ব্যবহার করে ভ্যালু সঠিক কিনা যাচাই করা হয়।

### সিনট্যাক্স

```php
array(
    'id'                => 'my_field',
    'type'              => 'text',
    'title'             => 'আমার ফিল্ড',
    'validate_callback' => 'my_validate_function',
)
```

### ফাংশন সিগনেচার

```php
function my_validate_function($value, $field) {
    // $value - যাচাই করার ভ্যালু
    // $field - ফিল্ড কনফিগারেশন
    
    // সফল হলে true রিটার্ন করুন
    // ব্যর্থ হলে false অথবা WP_Error রিটার্ন করুন
}
```

### উদাহরণসমূহ

#### উদাহরণ ১: ইমেইল ডোমেইন চেক

```php
array(
    'id'                => 'company_email',
    'type'              => 'email',
    'title'             => 'কোম্পানি ইমেইল',
    'validate_callback' => function($value, $field) {
        if (empty($value)) {
            return true; // ঐচ্ছিক ফিল্ড
        }
        
        // শুধু নির্দিষ্ট ডোমেইন গ্রহণ করুন
        $allowed_domains = array('company.com', 'company.bd');
        $domain = substr(strrchr($value, "@"), 1);
        
        if (!in_array($domain, $allowed_domains)) {
            return new WP_Error(
                'invalid_domain',
                'শুধুমাত্র company.com অথবা company.bd ডোমেইনের ইমেইল গ্রহণযোগ্য।'
            );
        }
        
        return true;
    },
)
```

#### উদাহরণ ২: পাসওয়ার্ড স্ট্রেংথ চেক

```php
array(
    'id'                => 'api_password',
    'type'              => 'password',
    'title'             => 'API পাসওয়ার্ড',
    'validate_callback' => function($value, $field) {
        if (empty($value)) {
            return true;
        }
        
        $errors = array();
        
        // মিনিমাম ৮ ক্যারেক্টার
        if (strlen($value) < 8) {
            $errors[] = 'পাসওয়ার্ড কমপক্ষে ৮ ক্যারেক্টার হতে হবে।';
        }
        
        // বড় হাতের অক্ষর থাকতে হবে
        if (!preg_match('/[A-Z]/', $value)) {
            $errors[] = 'পাসওয়ার্ডে কমপক্ষে একটি বড় হাতের অক্ষর থাকতে হবে।';
        }
        
        // সংখ্যা থাকতে হবে
        if (!preg_match('/[0-9]/', $value)) {
            $errors[] = 'পাসওয়ার্ডে কমপক্ষে একটি সংখ্যা থাকতে হবে।';
        }
        
        if (!empty($errors)) {
            return new WP_Error('weak_password', implode(' ', $errors));
        }
        
        return true;
    },
)
```

#### উদাহরণ ৩: URL এক্সেস চেক

```php
array(
    'id'                => 'webhook_url',
    'type'              => 'url',
    'title'             => 'Webhook URL',
    'validate_callback' => function($value, $field) {
        if (empty($value)) {
            return true;
        }
        
        // URL ফরম্যাট চেক
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return new WP_Error('invalid_url', 'সঠিক URL দিন।');
        }
        
        // HTTPS প্রয়োজন
        if (strpos($value, 'https://') !== 0) {
            return new WP_Error('https_required', 'শুধুমাত্র HTTPS URL গ্রহণযোগ্য।');
        }
        
        return true;
    },
)
```

#### উদাহরণ ৪: রেঞ্জ ভ্যালিডেশন

```php
array(
    'id'                => 'discount_percentage',
    'type'              => 'number',
    'title'             => 'ডিসকাউন্ট শতাংশ',
    'validate_callback' => function($value, $field) {
        $value = floatval($value);
        
        if ($value < 0 || $value > 100) {
            return new WP_Error(
                'out_of_range',
                'ডিসকাউন্ট ০ থেকে ১০০ এর মধ্যে হতে হবে।'
            );
        }
        
        return true;
    },
)
```

---

## ফিল্টার হুক কলব্যাক

Framework এর বিল্ট-ইন ফিল্টার হুকগুলোতে কলব্যাক সংযুক্ত করা যায়।

### ফিল্ড স্যানিটাইজেশন ফিল্টার

```php
// সব টেক্সট ফিল্ডের জন্য
add_filter('bizzplugin_sanitize_field_text', function($value, $field) {
    // আপনার স্যানিটাইজেশন লজিক
    return sanitize_text_field($value);
}, 10, 2);

// নির্দিষ্ট ফিল্ড টাইপের জন্য
add_filter('bizzplugin_sanitize_field_my_custom_type', function($value, $field) {
    // কাস্টম টাইপের জন্য স্যানিটাইজেশন
    return $value;
}, 10, 2);

// সব ফিল্ডের জন্য জেনেরিক
add_filter('bizzplugin_sanitize_field', function($value, $field, $type) {
    // সব ফিল্ডের জন্য অতিরিক্ত স্যানিটাইজেশন
    return $value;
}, 10, 3);
```

### সেকশন ফিল্ড ফিল্টার

```php
// নির্দিষ্ট প্যানেলের সেকশনে ফিল্ড যোগ
add_filter('bizzplugin_section_fields_my_panel', function($fields, $section_id, $panel_id) {
    if ($section_id === 'general') {
        $fields[] = array(
            'id'      => 'extra_field',
            'type'    => 'text',
            'title'   => 'অতিরিক্ত ফিল্ড',
            'default' => '',
        );
    }
    return $fields;
}, 10, 3);
```

---

## অ্যাকশন হুক কলব্যাক

### ফিল্ড রেন্ডার অ্যাকশন

```php
// কাস্টম ফিল্ড টাইপ রেন্ডার করতে
add_action('bizzplugin_render_field_my_custom_type', function($field, $value, $is_disabled) {
    ?>
    <div class="my-custom-field">
        <input 
            type="text" 
            name="<?php echo esc_attr($field['id']); ?>" 
            value="<?php echo esc_attr($value); ?>"
        />
    </div>
    <?php
}, 10, 3);
```

### প্যানেল রেন্ডার অ্যাকশন

```php
// কাস্টম প্যানেল রেন্ডার
add_action('bizzplugin_render_panel_my_panel', function($panel, $options, $sections, $current_section, $current_subsection) {
    // সম্পূর্ণ কাস্টম প্যানেল UI
}, 10, 5);
```

---

## সেরা অভ্যাস

### ১. সবসময় Escaping ব্যবহার করুন

```php
// ভুল
echo $value;

// সঠিক
echo esc_html($value);
echo esc_attr($value);
echo esc_url($value);
```

### ২. নাল চেক করুন

```php
function my_callback($field, $value, $is_disabled) {
    $value = $value ?? '';
    $custom_param = $field['custom_param'] ?? 'default';
    // ...
}
```

### ৩. WP_Error ব্যবহার করুন

```php
function my_validate($value, $field) {
    if (something_wrong($value)) {
        return new WP_Error(
            'error_code',
            'ব্যবহারকারীর জন্য বার্তা'
        );
    }
    return true;
}
```

### ৪. কোড পুনরায় ব্যবহারযোগ্য রাখুন

```php
// একটি ক্লাসে সব কলব্যাক রাখুন
class My_Field_Callbacks {
    
    public static function sanitize_phone($value, $field) {
        return preg_replace('/[^0-9+]/', '', $value);
    }
    
    public static function validate_phone($value, $field) {
        if (!preg_match('/^[0-9+]{10,14}$/', $value)) {
            return new WP_Error('invalid_phone', 'সঠিক ফোন নম্বর দিন।');
        }
        return true;
    }
}

// ব্যবহার
array(
    'id'                => 'phone',
    'type'              => 'text',
    'sanitize_callback' => array('My_Field_Callbacks', 'sanitize_phone'),
    'validate_callback' => array('My_Field_Callbacks', 'validate_phone'),
)
```

---

## পরবর্তী পড়ুন

- [স্যানিটাইজ এবং ভ্যালিডেশন বিস্তারিত](sanitize-validation-callbacks.md)
- [নতুন ফিল্ড টাইপ তৈরি](custom-field-type.md)
- [সকল ফিল্ড টাইপ](all-field-types.md)
