# ফিল্টার এবং হুক

এই ডকুমেন্ট BizzPlugin Options Framework-এ সব উপলব্ধ ফিল্টার এবং অ্যাকশন হুকের একটি সম্পূর্ণ রেফারেন্স প্রদান করে।

## অ্যাকশন হুক

### ফ্রেমওয়ার্ক ইনিশিয়ালাইজেশন

#### bizzplugin_framework_loaded

ফ্রেমওয়ার্ক সম্পূর্ণ লোড এবং প্রস্তুত হলে ফায়ার হয়।

```php
add_action('bizzplugin_framework_loaded', function($framework) {
    // ফ্রেমওয়ার্ক ব্যবহারের জন্য প্রস্তুত
    // $framework হল BizzPlugin_Framework ইনস্ট্যান্স
});
```

#### bizzplugin_panel_created

একটি প্যানেল তৈরি হলে ফায়ার হয়।

```php
add_action('bizzplugin_panel_created', function($panel, $panel_id) {
    // প্যানেল তৈরি হয়েছে
    // অন্য প্লাগইন থেকে ফিল্ড/সেকশন যোগ করতে এটি ব্যবহার করুন
    if ($panel_id === 'target_panel') {
        $panel->add_field('general', array(
            'id'      => 'extra_field',
            'type'    => 'text',
            'title'   => __('অতিরিক্ত ফিল্ড', 'textdomain'),
            'default' => '',
        ));
    }
}, 10, 2);
```

### অপশন সংরক্ষণ

#### bizzplugin_options_saved

AJAX এর মাধ্যমে অপশন সংরক্ষণের পরে ফায়ার হয়।

```php
add_action('bizzplugin_options_saved', function($option_name, $new_options, $old_options, $panel_id) {
    // অপশন সংরক্ষিত হয়েছে
    // পুরানো এবং নতুন মান তুলনা করুন, সাইড ইফেক্ট ট্রিগার করুন, ইত্যাদি
    
    if (isset($new_options['cache_enabled']) && $new_options['cache_enabled'] !== $old_options['cache_enabled']) {
        // ক্যাশ সেটিং পরিবর্তিত - ক্যাশ ক্লিয়ার করুন
        my_plugin_clear_cache();
    }
}, 10, 4);
```

### নেভিগেশন হুক

#### bizzplugin_nav_before_menu

নেভিগেশন মেনুর আগে কন্টেন্ট যোগ করুন।

```php
add_action('bizzplugin_nav_before_menu', function($panel_id) {
    if ($panel_id === 'my_plugin') {
        echo '<div class="custom-nav-header">কাস্টম কন্টেন্ট</div>';
    }
});
```

#### bizzplugin_nav_after_menu

নেভিগেশন মেনুর পরে কন্টেন্ট যোগ করুন।

```php
add_action('bizzplugin_nav_after_menu', function($panel_id) {
    if ($panel_id === 'my_plugin') {
        echo '<div class="custom-nav-footer">অতিরিক্ত লিঙ্ক</div>';
    }
});
```

### সাইডবার হুক

#### bizzplugin_sidebar_top

সাইডবারের উপরে কন্টেন্ট যোগ করুন।

```php
add_action('bizzplugin_sidebar_top', function($panel_id) {
    echo '<div class="custom-sidebar-widget">উপরের উইজেট</div>';
});
```

#### bizzplugin_sidebar_middle

সাইডবারের মাঝে কন্টেন্ট যোগ করুন।

```php
add_action('bizzplugin_sidebar_middle', function($panel_id) {
    echo '<div class="custom-sidebar-widget">মাঝের উইজেট</div>';
});
```

#### bizzplugin_sidebar_bottom

সাইডবারের নিচে কন্টেন্ট যোগ করুন।

```php
add_action('bizzplugin_sidebar_bottom', function($panel_id) {
    echo '<div class="custom-sidebar-widget">নিচের উইজেট</div>';
});
```

### কাস্টম ফিল্ড রেন্ডারিং

#### bizzplugin_render_field_{type}

একটি কাস্টম ফিল্ড টাইপ রেন্ডার করুন।

```php
// একটি কাস্টম ফিল্ড টাইপ রেজিস্টার করুন
add_action('bizzplugin_render_field_my_custom', function($field, $value, $is_disabled) {
    $disabled = $is_disabled ? ' disabled="disabled"' : '';
    ?>
    <div class="my-custom-field-wrapper">
        <input 
            type="text" 
            id="<?php echo esc_attr($field['id']); ?>"
            name="<?php echo esc_attr($field['id']); ?>"
            value="<?php echo esc_attr($value); ?>"
            class="my-custom-input"
            <?php echo $disabled; ?>
        />
        <span class="my-custom-suffix"><?php echo esc_html($field['suffix'] ?? ''); ?></span>
    </div>
    <?php
}, 10, 3);

// ব্যবহার
array(
    'id'      => 'my_field',
    'type'    => 'my_custom',  // অ্যাকশন নামের সাথে মিলে
    'title'   => 'আমার ফিল্ড',
    'default' => '',
    'suffix'  => 'একক',
)
```

#### bizzplugin_render_custom_field

অন্যত্র হ্যান্ডেল না হওয়া কাস্টম ফিল্ড টাইপের জন্য ফলব্যাক।

```php
add_action('bizzplugin_render_custom_field', function($field, $value, $is_disabled) {
    $field_type = $field['type'];
    
    if ($field_type === 'special_field') {
        // বিশেষ ফিল্ড রেন্ডার করুন
    }
}, 10, 3);
```

### কাস্টম প্যানেল রেন্ডারিং

#### bizzplugin_render_panel_{panel_id}

একটি নির্দিষ্ট প্যানেলের জন্য প্যানেল রেন্ডারিং সম্পূর্ণ প্রতিস্থাপন করুন।

```php
add_action('bizzplugin_render_panel_my_plugin', function($panel, $options, $sections, $current_section, $current_subsection) {
    // সম্পূর্ণ কাস্টম প্যানেল রেন্ডারিং
    ?>
    <div class="my-custom-panel">
        <h1>কাস্টম প্যানেল</h1>
        <!-- আপনার কাস্টম HTML -->
    </div>
    <?php
}, 10, 5);
```

---

## ফিল্টার হুক

### প্যানেল কনফিগারেশন ফিল্টার

#### bizzplugin_panel_config_{panel_id} (প্রস্তাবিত)

একটি নির্দিষ্ট প্যানেলের জন্য প্যানেল কনফিগারেশন মডিফাই করুন। এটি প্যানেল-নির্দিষ্ট পরিবর্তনের জন্য প্রস্তাবিত পদ্ধতি।

```php
add_filter('bizzplugin_panel_config_my_plugin', function($config, $panel_id) {
    // প্যানেল কনফিগ মডিফাই করুন
    $config['logo'] = MY_PLUGIN_URL . 'assets/logo.png';
    $config['version'] = '2.0.0';
    $config['is_premium'] = license_is_valid();
    
    // রিসোর্স যোগ করুন
    $config['resources'][] = array(
        'icon'  => 'dashicons dashicons-video-alt3',
        'title' => __('ভিডিও টিউটোরিয়াল', 'textdomain'),
        'url'   => 'https://example.com/videos',
    );
    
    return $config;
}, 10, 2);
```

### সেকশন ফিল্টার

#### bizzplugin_panel_sections_{panel_id}

একটি নির্দিষ্ট প্যানেলের জন্য সেকশন মডিফাই করুন।

```php
add_filter('bizzplugin_panel_sections_my_plugin', function($sections, $panel_id) {
    // একটি নতুন সেকশন যোগ করুন
    $sections['my_addon_section'] = array(
        'id'     => 'my_addon_section',
        'title'  => __('অ্যাডঅন সেটিংস', 'textdomain'),
        'icon'   => 'dashicons dashicons-admin-plugins',
        'fields' => array(
            array(
                'id'      => 'addon_enabled',
                'type'    => 'switch',
                'title'   => __('অ্যাডঅন সক্রিয়', 'textdomain'),
                'default' => '0',
            ),
        ),
    );
    
    return $sections;
}, 10, 2);
```

### ফিল্ড ফিল্টার

#### bizzplugin_section_fields_{panel_id}

একটি নির্দিষ্ট প্যানেলের মধ্যে একটি নির্দিষ্ট সেকশনের জন্য ফিল্ড মডিফাই করুন।

```php
add_filter('bizzplugin_section_fields_my_plugin', function($fields, $section_id, $panel_id) {
    if ($section_id === 'general') {
        // জেনারেল সেকশনে ফিল্ড যোগ করুন
        $fields[] = array(
            'id'      => 'injected_field',
            'type'    => 'text',
            'title'   => __('ইনজেক্ট করা ফিল্ড', 'textdomain'),
            'default' => '',
        );
    }
    
    return $fields;
}, 10, 3);
```

#### bizzplugin_section_fields (জেনেরিক)

সব প্যানেলের জন্য জেনেরিক ফিল্টার (প্রস্তাবিত নয় - প্যানেল-নির্দিষ্ট ফিল্টার ব্যবহার করুন)।

```php
add_filter('bizzplugin_section_fields', function($fields, $section_id, $panel_id) {
    // শুধুমাত্র আমাদের প্যানেল হলে মডিফাই করুন
    if ($panel_id !== 'my_plugin') {
        return $fields;
    }
    
    // ফিল্ড মডিফাই করুন
    return $fields;
}, 10, 3);
```

### প্রিমিয়াম স্ট্যাটাস ফিল্টার

#### bizzplugin_is_premium_{panel_id}

একটি নির্দিষ্ট প্যানেলের জন্য প্রিমিয়াম স্ট্যাটাস নিয়ন্ত্রণ করুন।

```php
add_filter('bizzplugin_is_premium_my_plugin', function($is_premium, $panel_id) {
    // লাইসেন্স চেক করুন
    return my_plugin_license_is_valid();
}, 10, 2);
```

---

## ফিল্টার নামকরণ কনভেনশন

ফ্রেমওয়ার্ক এই প্যাটার্নে প্যানেল-নির্দিষ্ট ফিল্টার ব্যবহার করে:

```
{filter_name}_{panel_id}
```

এটি ফ্রেমওয়ার্ক ব্যবহারকারী একাধিক প্লাগইনকে সংঘর্ষ ছাড়া সহাবস্থান করতে দেয়।

| জেনেরিক ফিল্টার | প্যানেল-নির্দিষ্ট ফিল্টার |
|----------------|------------------------|
| `bizzplugin_panel_config` | `bizzplugin_panel_config_{panel_id}` |
| `bizzplugin_panel_sections` | `bizzplugin_panel_sections_{panel_id}` |
| `bizzplugin_section_fields` | `bizzplugin_section_fields_{panel_id}` |
| `bizzplugin_is_premium` | `bizzplugin_is_premium_{panel_id}` |

**সর্বদা প্যানেল-নির্দিষ্ট ফিল্টার পছন্দ করুন** অন্য প্লাগইনকে প্রভাবিত করা এড়াতে।

---

## অ্যাডঅন প্লাগইন দিয়ে এক্সটেন্ড করা

### উদাহরণ: অ্যাডঅন থেকে ফিল্ড যোগ করা

```php
<?php
/**
 * Plugin Name: আমার প্লাগইন অ্যাডঅন
 */

// ফ্রেমওয়ার্ক প্রস্তুত হওয়ার জন্য অপেক্ষা করুন
add_action('bizzplugin_framework_loaded', function($framework) {
    // মূল প্লাগইনের প্যানেল পান
    $panel = $framework->get_panel('my_main_plugin');
    
    if ($panel) {
        // চেইনেবল API ব্যবহার করে অ্যাডঅন সেকশন যোগ করুন
        $panel->add_section(array(
            'id'     => 'addon_settings',
            'title'  => __('অ্যাডঅন সেটিংস', 'my-addon'),
            'icon'   => 'dashicons dashicons-admin-plugins',
            'fields' => array(
                array(
                    'id'      => 'addon_feature',
                    'type'    => 'switch',
                    'title'   => __('অ্যাডঅন ফিচার', 'my-addon'),
                    'default' => '0',
                ),
            ),
        ));
    }
});
```

### উদাহরণ: বিদ্যমান ফিল্ড মডিফাই করা

```php
// ফিল্ড মডিফাই করতে ফিল্টার ব্যবহার করুন
add_filter('bizzplugin_section_fields_my_plugin', function($fields, $section_id, $panel_id) {
    if ($section_id === 'general') {
        // বিদ্যমান ফিল্ড মডিফাই করুন
        foreach ($fields as &$field) {
            if ($field['id'] === 'existing_field') {
                $field['description'] .= ' ' . __('(অ্যাডঅন দ্বারা মডিফাইড)', 'my-addon');
            }
        }
        
        // বিদ্যমানটির পরে নতুন ফিল্ড যোগ করুন
        $new_fields = array();
        foreach ($fields as $field) {
            $new_fields[] = $field;
            if ($field['id'] === 'existing_field') {
                $new_fields[] = array(
                    'id'      => 'addon_related_field',
                    'type'    => 'text',
                    'title'   => __('সম্পর্কিত ফিল্ড', 'my-addon'),
                    'default' => '',
                );
            }
        }
        $fields = $new_fields;
    }
    
    return $fields;
}, 10, 3);
```

### উদাহরণ: কাস্টম ফিল্ড টাইপ

```php
// কাস্টম ফিল্ড টাইপ রেজিস্টার করুন
add_action('bizzplugin_render_field_icon_picker', function($field, $value, $is_disabled) {
    $icons = array(
        'dashicons-admin-site',
        'dashicons-admin-media',
        'dashicons-admin-links',
        'dashicons-admin-comments',
    );
    ?>
    <div class="icon-picker-field">
        <?php foreach ($icons as $icon) : ?>
            <label class="icon-option <?php echo $value === $icon ? 'selected' : ''; ?>">
                <input 
                    type="radio" 
                    name="<?php echo esc_attr($field['id']); ?>"
                    value="<?php echo esc_attr($icon); ?>"
                    <?php checked($value, $icon); ?>
                    <?php disabled($is_disabled, true); ?>
                />
                <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
            </label>
        <?php endforeach; ?>
    </div>
    <?php
}, 10, 3);
```

---

## সেরা অনুশীলন

1. **সর্বদা প্যানেল-নির্দিষ্ট ফিল্টার ব্যবহার করুন** অন্য প্লাগইনের সাথে সংঘর্ষ এড়াতে
2. **জেনেরিক ফিল্টার ব্যবহার করার সময় panel_id চেক করুন**
3. **সাইড ইফেক্টের জন্য অ্যাকশন হুক ব্যবহার করুন** (লগিং, ক্যাশ ক্লিয়ারিং, ইত্যাদি)
4. **ডেটা মডিফাই করতে ফিল্টার ব্যবহার করুন** (কনফিগারেশন, ফিল্ড, ইত্যাদি)
5. **আপনার হুক ডকুমেন্ট করুন** অন্যদের এক্সটেন্ড করে এমন প্লাগইন তৈরি করার সময়
6. **বিদ্যমান ফিল্ড মডিফাই করার সময় পুঙ্খানুপুঙ্খভাবে টেস্ট করুন**
7. **ফ্রি এবং প্রিমিয়াম উভয় ভার্সনের সাথে সামঞ্জস্যতা বজায় রাখুন**

---

## পরবর্তী পদক্ষেপ

- [REST API](api.md) - API ইন্টিগ্রেশন
- [ওয়েবহুক](webhooks.md) - ওয়েবহুক কনফিগারেশন
- [উদাহরণ](examples.md) - আরও কোড উদাহরণ
