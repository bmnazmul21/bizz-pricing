# BizzPlugin Options Framework - অতিরিক্ত ডকুমেন্টেশন

এই ফোল্ডারে BizzPlugin Options Framework এর বিস্তারিত বাংলা ডকুমেন্টেশন রয়েছে। এখানে কাস্টমাইজেশন, কলব্যাক, স্যানিটাইজেশন, এবং নতুন ফিল্ড টাইপ তৈরির বিস্তারিত গাইড পাবেন।

## ডকুমেন্টেশন সূচি

### মূল ডকুমেন্ট

| ফাইল | বিষয় | বর্ণনা |
|------|-------|--------|
| [callback-type.md](callback-type.md) | কলব্যাক টাইপ | `type => 'callback'` ফিল্ড ব্যবহারের সম্পূর্ণ গাইড |
| [callback-functions.md](callback-functions.md) | কলব্যাক ফাংশন | সকল কলব্যাক ফাংশনের বিস্তারিত |
| [sanitize-validation-callbacks.md](sanitize-validation-callbacks.md) | স্যানিটাইজ ও ভ্যালিডেশন | ডেটা পরিষ্কার ও যাচাই করার গাইড |
| [custom-field-type.md](custom-field-type.md) | নতুন ফিল্ড টাইপ | কাস্টম ফিল্ড টাইপ তৈরির সম্পূর্ণ গাইড |
| [all-field-types.md](all-field-types.md) | সকল ফিল্ড টাইপ | সকল বিল্ট-ইন ফিল্ড টাইপের পূর্ণাঙ্গ রেফারেন্স |

---

## দ্রুত শুরু

### কলব্যাক টাইপ ব্যবহার

```php
array(
    'id'              => 'my_custom_field',
    'type'            => 'callback',
    'title'           => 'কাস্টম ফিল্ড',
    'render_callback' => 'my_render_function',
)

function my_render_function($field, $value, $is_disabled) {
    ?>
    <input 
        type="text" 
        name="<?php echo esc_attr($field['id']); ?>" 
        value="<?php echo esc_attr($value); ?>"
    />
    <?php
}
```

### কাস্টম স্যানিটাইজেশন

```php
array(
    'id'                => 'phone_number',
    'type'              => 'text',
    'title'             => 'ফোন নম্বর',
    'sanitize_callback' => function($value, $field) {
        return preg_replace('/[^0-9+]/', '', $value);
    },
)
```

### কাস্টম ভ্যালিডেশন

```php
array(
    'id'                => 'email_field',
    'type'              => 'email',
    'title'             => 'ইমেইল',
    'validate_callback' => function($value, $field) {
        $domain = substr(strrchr($value, "@"), 1);
        if ($domain !== 'company.com') {
            return new WP_Error('invalid_domain', 'শুধু company.com ইমেইল গ্রহণযোগ্য।');
        }
        return true;
    },
)
```

### নতুন ফিল্ড টাইপ তৈরি

```php
// রেন্ডার হুক
add_action('bizzplugin_render_field_my_rating', function($field, $value, $is_disabled) {
    // HTML রেন্ডার
}, 10, 3);

// স্যানিটাইজেশন ফিল্টার
add_filter('bizzplugin_sanitize_field_my_rating', function($value, $field) {
    return intval($value);
}, 10, 2);
```

---

## প্রধান বৈশিষ্ট্য

### ১. কলব্যাক সিস্টেম

- **render_callback**: কাস্টম HTML রেন্ডার
- **sanitize_callback**: ডেটা পরিষ্কার করা
- **validate_callback**: ডেটা যাচাই করা

### ২. বিল্ট-ইন স্যানিটাইজেশন

| ফিল্ড টাইপ | স্যানিটাইজার |
|------------|--------------|
| text | `sanitize_text_field()` |
| email | `sanitize_email()` |
| url | `esc_url_raw()` |
| number | `floatval()` + রেঞ্জ চেক |
| color | `sanitize_hex_color()` |

### ৩. ফিল্টার হুক

```php
// নির্দিষ্ট ফিল্ড টাইপের জন্য
add_filter('bizzplugin_sanitize_field_text', 'my_sanitize', 10, 2);

// সব ফিল্ডের জন্য
add_filter('bizzplugin_sanitize_field', 'my_sanitize', 10, 3);
```

### ৪. অ্যাকশন হুক

```php
// কাস্টম ফিল্ড টাইপ রেন্ডার
add_action('bizzplugin_render_field_my_type', 'render_my_type', 10, 3);
```

---

## সম্পর্কিত ডকুমেন্টেশন

মূল ডকুমেন্টেশন ফোল্ডারে আরও তথ্য পাবেন:

- `options-framework/doc/bd/` - বাংলা ডকুমেন্টেশন
- `options-framework/doc/en/` - ইংরেজি ডকুমেন্টেশন
- `options-framework/doc/extending-framework.md` - ফ্রেমওয়ার্ক এক্সটেন্ড করা
- `options-framework/doc/fields.md` - ফিল্ড রেফারেন্স

---

## সাহায্য প্রয়োজন?

প্রশ্ন থাকলে বা সমস্যা হলে:

1. প্রথমে এই ডকুমেন্টেশনগুলো ভালো করে পড়ুন
2. `options-framework/example-code.php` দেখুন
3. GitHub Issues এ প্রশ্ন করুন

---

## অবদান রাখুন

ডকুমেন্টেশন উন্নত করতে চাইলে:

1. Repository fork করুন
2. পরিবর্তন করুন
3. Pull request দিন

---

© BizzPlugin - সকল অধিকার সংরক্ষিত
