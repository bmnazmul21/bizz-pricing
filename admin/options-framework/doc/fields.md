# ফিল্ড টাইপ গাইড

এই ডকুমেন্টে BizzPlugin Options Framework এ ব্যবহৃত সকল ফিল্ড টাইপের বিস্তারিত বর্ণনা দেওয়া হলো।

## সাধারণ ফিল্ড প্যারামিটার

সব ফিল্ডে এই কমন প্যারামিটারগুলো ব্যবহার করা যায়:

| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `id` | string | ফিল্ডের ইউনিক আইডি (আবশ্যক) |
| `type` | string | ফিল্ডের টাইপ (আবশ্যক) |
| `title` | string | ফিল্ডের লেবেল/টাইটেল |
| `description` | string | ফিল্ডের বর্ণনা |
| `default` | mixed | ডিফল্ট ভ্যালু |
| `class` | string | কাস্টম CSS ক্লাস |
| `premium` | bool | প্রিমিয়াম ফিল্ড কিনা |
| `dependency` | array | কন্ডিশনাল ফিল্ডের জন্য |
| `sanitize_callback` | callable | কাস্টম স্যানিটাইজেশন ফাংশন |
| `validate_callback` | callable | কাস্টম ভ্যালিডেশন ফাংশন |
| `callback` | callable | রেন্ডার করার পর কল হবে |

---

## 1. Text Field (টেক্সট ফিল্ড)

সাধারণ টেক্সট ইনপুট।

```php
array(
    'id' => 'site_title',
    'type' => 'text',
    'title' => 'Site Title',
    'description' => 'Enter your site title.',
    'default' => 'My Website',
    'placeholder' => 'Enter title...',
)
```

### অতিরিক্ত প্যারামিটার:
| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `placeholder` | string | প্লেসহোল্ডার টেক্সট |
| `minlength` | int | মিনিমাম ক্যারেক্টার |
| `maxlength` | int | ম্যাক্সিমাম ক্যারেক্টার |
| `pattern` | string | রেগুলার এক্সপ্রেশন প্যাটার্ন |

---

## 2. Textarea Field (টেক্সটএরিয়া ফিল্ড)

বড় টেক্সট ইনপুট।

```php
array(
    'id' => 'site_description',
    'type' => 'textarea',
    'title' => 'Site Description',
    'description' => 'Enter a brief description.',
    'default' => '',
    'rows' => 5,
    'placeholder' => 'Enter description...',
)
```

### অতিরিক্ত প্যারামিটার:
| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `rows` | int | সারির সংখ্যা (ডিফল্ট: 5) |
| `placeholder` | string | প্লেসহোল্ডার টেক্সট |

---

## 3. Number Field (নম্বর ফিল্ড)

নম্বর/সংখ্যা ইনপুট।

```php
array(
    'id' => 'posts_per_page',
    'type' => 'number',
    'title' => 'Posts Per Page',
    'description' => 'Number of posts to display.',
    'default' => 10,
    'min' => 1,
    'max' => 100,
    'step' => 1,
)
```

### অতিরিক্ত প্যারামিটার:
| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `min` | number | মিনিমাম ভ্যালু |
| `max` | number | ম্যাক্সিমাম ভ্যালু |
| `step` | number | স্টেপ ভ্যালু |

---

## 4. Email Field (ইমেইল ফিল্ড)

ইমেইল ইনপুট (ভ্যালিডেশন সহ)।

```php
array(
    'id' => 'admin_email',
    'type' => 'email',
    'title' => 'Admin Email',
    'description' => 'Enter admin email address.',
    'default' => get_option('admin_email'),
    'placeholder' => 'admin@example.com',
)
```

---

## 5. URL Field (URL ফিল্ড)

URL ইনপুট।

```php
array(
    'id' => 'site_url',
    'type' => 'url',
    'title' => 'Site URL',
    'description' => 'Enter your website URL.',
    'default' => home_url(),
    'placeholder' => 'https://example.com',
)
```

---

## 6. Password Field (পাসওয়ার্ড ফিল্ড)

পাসওয়ার্ড ইনপুট (হিডেন ক্যারেক্টার)।

```php
array(
    'id' => 'api_key',
    'type' => 'password',
    'title' => 'API Key',
    'description' => 'Enter your API key.',
    'default' => '',
    'placeholder' => 'Enter API key...',
)
```

---

## 7. Select Field (সিলেক্ট/ড্রপডাউন)

সিঙ্গেল সিলেকশন ড্রপডাউন।

```php
array(
    'id' => 'layout_style',
    'type' => 'select',
    'title' => 'Layout Style',
    'description' => 'Choose layout style.',
    'default' => 'full-width',
    'options' => array(
        'full-width' => 'Full Width',
        'boxed' => 'Boxed',
        'framed' => 'Framed',
    ),
)
```

### অতিরিক্ত প্যারামিটার:
| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `options` | array | key => value অপশন অ্যারে |

---

## 8. Multi Select Field (মাল্টি সিলেক্ট)

একাধিক অপশন সিলেক্ট করা যায়।

```php
array(
    'id' => 'selected_categories',
    'type' => 'multi_select',
    'title' => 'Select Categories',
    'description' => 'Select multiple categories.',
    'default' => array(),
    'options' => array(
        'cat_1' => 'Category 1',
        'cat_2' => 'Category 2',
        'cat_3' => 'Category 3',
    ),
)
```

### সেভ হওয়া ভ্যালু:
Array হিসেবে সেভ হয়: `array('cat_1', 'cat_2')`

---

## 9. Checkbox Field (চেকবক্স)

সিঙ্গেল চেকবক্স (true/false বা 1/0)।

```php
array(
    'id' => 'enable_feature',
    'type' => 'checkbox',
    'title' => 'Enable Feature',
    'description' => 'Check to enable this feature.',
    'default' => '0',
    'label' => 'Yes, enable this feature',
)
```

### অতিরিক্ত প্যারামিটার:
| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `label` | string | চেকবক্সের পাশের লেবেল |

### সেভ হওয়া ভ্যালু:
- চেক করা: `'1'`
- আনচেক করা: `'0'`

---

## 10. Checkbox Group (চেকবক্স গ্রুপ)

একাধিক চেকবক্স একসাথে।

```php
array(
    'id' => 'enabled_modules',
    'type' => 'checkbox_group',
    'title' => 'Enabled Modules',
    'description' => 'Select which modules to enable.',
    'default' => array('module_1', 'module_2'),
    'options' => array(
        'module_1' => 'Module One',
        'module_2' => 'Module Two',
        'module_3' => 'Module Three',
    ),
)
```

### সেভ হওয়া ভ্যালু:
Array হিসেবে সেভ হয়: `array('module_1', 'module_2')`

---

## 11. Radio Field (রেডিও বাটন)

একটি অপশন সিলেক্ট।

```php
array(
    'id' => 'feature_mode',
    'type' => 'radio',
    'title' => 'Feature Mode',
    'description' => 'Select feature mode.',
    'default' => 'standard',
    'options' => array(
        'standard' => 'Standard Mode',
        'advanced' => 'Advanced Mode',
        'expert' => 'Expert Mode',
    ),
)
```

---

## 12. Switch/On-Off Toggle (সুইচ/অন-অফ টগল)

সুন্দর স্টাইলের টগল সুইচ (true/false)। `switch` বা `on_off` দুইটাই ব্যবহার করা যায়।

```php
array(
    'id' => 'enable_feature_1',
    'type' => 'switch', // অথবা 'on_off' ব্যবহার করতে পারেন
    'title' => 'Enable Feature One',
    'description' => 'Turn on/off this feature.',
    'default' => '1',
    'on_label' => 'Enabled',
    'off_label' => 'Disabled',
)
```

### অতিরিক্ত প্যারামিটার:
| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `on_label` | string | অন অবস্থার লেবেল (ডিফল্ট: On) |
| `off_label` | string | অফ অবস্থার লেবেল (ডিফল্ট: Off) |

### সেভ হওয়া ভ্যালু:
- অন: `'1'`
- অফ: `'0'`

---

## 13. Color Picker (কালার পিকার)

রঙ সিলেক্ট করার জন্য।

```php
array(
    'id' => 'primary_color',
    'type' => 'color',
    'title' => 'Primary Color',
    'description' => 'Select primary color.',
    'default' => '#2271b1',
)
```

### সেভ হওয়া ভ্যালু:
Hex কালার কোড: `'#2271b1'`

---

## 14. Date Picker (ডেট পিকার)

তারিখ সিলেক্ট করার জন্য।

```php
array(
    'id' => 'publish_date',
    'type' => 'date',
    'title' => 'Publish Date',
    'description' => 'Select a date.',
    'default' => '',
    'placeholder' => 'Select date...',
)
```

### সেভ হওয়া ভ্যালু:
YYYY-MM-DD ফরম্যাটে: `'2024-01-15'`

---

## 14.5 Slider/Range Field (স্লাইডার ফিল্ড)

সুন্দর ডিজাইনের স্লাইডার দিয়ে সংখ্যা সিলেক্ট করা যায়। রিয়েল-টাইমে সিলেক্টেড নাম্বার দেখা যায়।

```php
array(
    'id' => 'border_radius',
    'type' => 'slider', // বা 'range' ব্যবহার করা যায়
    'title' => 'Border Radius',
    'description' => 'Set the border radius value.',
    'default' => 10,
    'min' => 0,
    'max' => 50,
    'step' => 1,
    'unit' => 'px', // ইউনিট লেবেল (ঐচ্ছিক)
)
```

### অতিরিক্ত প্যারামিটার:
| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `min` | number | মিনিমাম ভ্যালু (ডিফল্ট: 0) |
| `max` | number | ম্যাক্সিমাম ভ্যালু (ডিফল্ট: 100) |
| `step` | number | স্টেপ ভ্যালু (ডিফল্ট: 1) |
| `unit` | string | ইউনিট লেবেল (px, %, em, ইত্যাদি) |

### সেভ হওয়া ভ্যালু:
সংখ্যা হিসেবে: `10`

### বৈশিষ্ট্য:
- ✅ রিয়েল-টাইম মান প্রদর্শন
- ✅ প্রোগ্রেস ট্র্যাক ভিজুয়ালাইজেশন
- ✅ কাস্টমাইজেবল রেঞ্জ এবং স্টেপ
- ✅ ইউনিট লেবেল সাপোর্ট

---

## 15. Image Upload (ইমেজ আপলোড)

ইমেজ আপলোড করার জন্য (WordPress Media Library ব্যবহার করে)।

```php
array(
    'id' => 'logo_image',
    'type' => 'image',
    'title' => 'Logo Image',
    'description' => 'Upload your site logo.',
    'default' => '',
)
```

### সেভ হওয়া ভ্যালু:
Attachment ID (integer): `123`

### ইমেজ URL পেতে:
```php
$logo_id = bizzplugin_get_option('logo_image');
$logo_url = wp_get_attachment_image_url($logo_id, 'full');
```

---

## 16. File Upload (ফাইল আপলোড)

যেকোনো ফাইল আপলোড করার জন্য।

```php
array(
    'id' => 'attachment_file',
    'type' => 'file',
    'title' => 'Attachment File',
    'description' => 'Upload a file attachment.',
    'default' => '',
)
```

### সেভ হওয়া ভ্যালু:
Attachment ID (integer): `456`

---

## 17. Image Select (ইমেজ সিলেক্ট)

ইমেজ দিয়ে অপশন সিলেক্ট।

```php
array(
    'id' => 'layout_template',
    'type' => 'image_select',
    'title' => 'Layout Template',
    'description' => 'Select a layout template.',
    'default' => 'sidebar-right',
    'options' => array(
        'sidebar-left' => plugin_dir_url(__FILE__) . 'images/sidebar-left.svg',
        'no-sidebar' => plugin_dir_url(__FILE__) . 'images/no-sidebar.svg',
        'sidebar-right' => plugin_dir_url(__FILE__) . 'images/sidebar-right.svg',
    ),
)
```

### সেভ হওয়া ভ্যালু:
সিলেক্টেড অপশনের key: `'sidebar-right'`

---

## 17.5. Option Select (অপশন সিলেক্ট)

টেক্সট দিয়ে অপশন সিলেক্ট। `image_select` এর মতোই, তবে ইমেজের পরিবর্তে টেক্সট লেবেল থাকে।

```php
array(
    'id' => 'display_mode',
    'type' => 'option_select',
    'title' => 'Display Mode',
    'description' => 'Select a display mode.',
    'default' => 'grid',
    'options' => array(
        'grid' => 'Grid View',
        'list' => 'List View',
        'compact' => 'Compact View',
        'expanded' => 'Expanded View',
    ),
)
```

### অতিরিক্ত প্যারামিটার:
| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `options` | array | key => label অপশন অ্যারে |

### সেভ হওয়া ভ্যালু:
সিলেক্টেড অপশনের key: `'grid'`

### বৈশিষ্ট্য:
- ✅ টেক্সট-ভিত্তিক অপশন সিলেকশন
- ✅ একটি মাত্র অপশন সিলেক্ট করা যায়
- ✅ সুন্দর বাটন স্টাইল ডিজাইন
- ✅ হোভার এবং সিলেক্টেড স্টেট

---

## 18. Post Select (পোস্ট সিলেক্ট)

পোস্ট টাইপ থেকে পোস্ট সিলেক্ট।

### সিঙ্গেল সিলেকশন:
```php
array(
    'id' => 'featured_post',
    'type' => 'post_select',
    'title' => 'Featured Post',
    'description' => 'Select a post to feature.',
    'post_type' => 'post',
    'default' => '',
)
```

### মাল্টিপল সিলেকশন:
```php
array(
    'id' => 'featured_pages',
    'type' => 'post_select',
    'title' => 'Featured Pages',
    'description' => 'Select multiple pages.',
    'post_type' => 'page',
    'multiple' => true,
    'default' => array(),
)
```

### অতিরিক্ত প্যারামিটার:
| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `post_type` | string | পোস্ট টাইপ (post, page, custom) |
| `multiple` | bool | মাল্টিপল সিলেকশন |

### সেভ হওয়া ভ্যালু:
- সিঙ্গেল: Post ID (integer): `42`
- মাল্টিপল: Array of Post IDs: `array(42, 56, 78)`

---

## 19. HTML Field (HTML ফিল্ড)

কাস্টম HTML কন্টেন্ট দেখানোর জন্য। এটা কোনো ভ্যালু সেভ করে না।

```php
array(
    'id' => 'notice_box',
    'type' => 'html',
    'title' => 'Notice',
    'content' => '<div class="bizzplugin-notice bizzplugin-notice-info">
        <p><strong>Information:</strong></p>
        <p>This is a custom notice box.</p>
    </div>',
)
```

### অতিরিক্ত প্যারামিটার:
| প্যারামিটার | টাইপ | বর্ণনা |
|------------|------|--------|
| `content` | string | HTML কন্টেন্ট |

---

## 20. Callback Field (কলব্যাক ফিল্ড)

কাস্টম রেন্ডারিং এর জন্য।

```php
array(
    'id' => 'custom_field',
    'type' => 'callback',
    'title' => 'Custom Field',
    'render_callback' => 'my_custom_render_function',
)

function my_custom_render_function($field, $value, $is_disabled) {
    echo '<div class="my-custom-field">';
    echo '<input type="text" name="' . esc_attr($field['id']) . '" value="' . esc_attr($value) . '">';
    echo '<p>This is a custom rendered field</p>';
    echo '</div>';
}
```

---

## কন্ডিশনাল ফিল্ড (Conditional Fields)

একটি ফিল্ড অন্য ফিল্ডের ভ্যালুর উপর নির্ভর করে দেখানো/লুকানো যায়।

```php
// প্যারেন্ট ফিল্ড
array(
    'id' => 'enable_feature',
    'type' => 'on_off',
    'title' => 'Enable Feature',
    'default' => '0',
),

// কন্ডিশনাল ফিল্ড (শুধু enable_feature = 1 হলে দেখাবে)
array(
    'id' => 'feature_option',
    'type' => 'select',
    'title' => 'Feature Option',
    'default' => 'option_1',
    'options' => array(
        'option_1' => 'Option 1',
        'option_2' => 'Option 2',
    ),
    'dependency' => array(
        'field' => 'enable_feature',
        'value' => '1',
    ),
),
```

### মাল্টিপল ভ্যালু কন্ডিশন:
```php
'dependency' => array(
    'field' => 'layout_style',
    'value' => 'boxed,framed', // কমা দিয়ে আলাদা করুন
),
```

---

## প্রিমিয়াম ফিল্ড

প্রিমিয়াম ভার্সনে এক্সক্লুসিভ ফিল্ড:

```php
array(
    'id' => 'premium_feature',
    'type' => 'on_off',
    'title' => 'Premium Feature',
    'description' => 'This is a premium only feature.',
    'default' => '0',
    'premium' => true, // এই লাইন যোগ করুন
),
```

প্রিমিয়াম না থাকলে:
- ফিল্ডে "Premium" ব্যাজ দেখাবে
- ফিল্ড ডিসেবল থাকবে
- ভ্যালু সেভ হবে না

---

## কাস্টম স্যানিটাইজেশন

```php
array(
    'id' => 'custom_field',
    'type' => 'text',
    'title' => 'Custom Field',
    'sanitize_callback' => function($value, $field) {
        // আপনার স্যানিটাইজেশন লজিক
        return strtoupper(sanitize_text_field($value));
    },
)
```

---

## কাস্টম ভ্যালিডেশন

```php
array(
    'id' => 'phone_number',
    'type' => 'text',
    'title' => 'Phone Number',
    'validate_callback' => function($value, $field) {
        if (!preg_match('/^[0-9]{10,11}$/', $value)) {
            return new WP_Error('invalid_phone', 'Invalid phone number format.');
        }
        return true;
    },
)
```

---

## পরবর্তী ধাপ

- [API এবং Webhook গাইড](./api.md)
- [কাস্টমাইজেশন গাইড](./customization.md)
