# BizzPlugin Options Framework এক্সটেন্ড করার সম্পূর্ণ গাইড

এই ডকুমেন্টেশনে আমরা শিখব কিভাবে BizzPlugin Options Framework-এ নতুন এলিমেন্ট, ফিল্ড, সেকশন এবং কাস্টম ফিচার যোগ করা যায়। এই গাইড নতুন ডেভেলপারদের জন্য স্টেপ বাই স্টেপ লেখা হয়েছে।

## সূচিপত্র

1. [প্রয়োজনীয়তা](#প্রয়োজনীয়তা)
2. [বেসিক ধারণা](#বেসিক-ধারণা)
3. [প্যানেল-ভিত্তিক হুক](#প্যানেল-ভিত্তিক-হুক)
4. [নতুন সেকশন যোগ করা](#নতুন-সেকশন-যোগ-করা)
5. [নতুন ফিল্ড যোগ করা](#নতুন-ফিল্ড-যোগ-করা)
6. [কাস্টম ফিল্ড টাইপ তৈরি](#কাস্টম-ফিল্ড-টাইপ-তৈরি)
7. [শর্টকোড ফিল্ড উদাহরণ (Copy বাটন সহ)](#শর্টকোড-ফিল্ড-উদাহরণ-copy-বাটন-সহ)
8. [অন্য প্লাগিন থেকে নির্দিষ্ট প্যানেলে কাজ করা](#অন্য-প্লাগিন-থেকে-নির্দিষ্ট-প্যানেলে-কাজ-করা)
9. [সেরা অভ্যাস](#সেরা-অভ্যাস)

---

## প্রয়োজনীয়তা

এই গাইড অনুসরণ করার আগে নিশ্চিত করুন:

- WordPress 5.0+ ইনস্টল আছে
- PHP 7.4+ চালু আছে
- BizzPlugin Options Framework সঠিকভাবে লোড হয়েছে

---

## বেসিক ধারণা

### Framework কাঠামো বুঝুন

```
BizzPlugin Framework
├── Panel (প্যানেল) - সেটিংস পেজ
│   ├── Section (সেকশন) - ট্যাব/মেনু আইটেম
│   │   ├── Field (ফিল্ড) - ইনপুট এলিমেন্ট
│   │   └── Subsection (সাবসেকশন) - সাব-মেনু
│   │       └── Field (ফিল্ড)
```

### গুরুত্বপূর্ণ হুক প্যাটার্ন

ফ্রেমওয়ার্কে দুই ধরনের হুক আছে:

1. **জেনেরিক হুক**: সব প্যানেলে কাজ করে
2. **প্যানেল-নির্দিষ্ট হুক**: নির্দিষ্ট প্যানেলে কাজ করে (রিকমেন্ডেড)

```php
// জেনেরিক (সব প্যানেলে)
add_filter('bizzplugin_section_fields', ...);

// প্যানেল-নির্দিষ্ট (শুধু 'my_panel' প্যানেলে)
add_filter('bizzplugin_section_fields_my_panel', ...);
```

**সবসময় প্যানেল-নির্দিষ্ট হুক ব্যবহার করুন** - এতে অন্য প্লাগিনের প্যানেলে কোনো প্রভাব পড়বে না।

---

## প্যানেল-ভিত্তিক হুক

### উপলব্ধ প্যানেল-নির্দিষ্ট ফিল্টার

| ফিল্টার নাম | কাজ |
|------------|-----|
| `bizzplugin_panel_config_{panel_id}` | প্যানেল কনফিগারেশন পরিবর্তন |
| `bizzplugin_panel_sections_{panel_id}` | সেকশন যোগ/পরিবর্তন |
| `bizzplugin_section_fields_{panel_id}` | ফিল্ড যোগ/পরিবর্তন |
| `bizzplugin_is_premium_{panel_id}` | প্রিমিয়াম স্ট্যাটাস নির্ধারণ |

### উপলব্ধ প্যানেল-নির্দিষ্ট অ্যাকশন

| অ্যাকশন নাম | কাজ |
|------------|-----|
| `bizzplugin_render_panel_{panel_id}` | কাস্টম প্যানেল রেন্ডারিং |
| `bizzplugin_render_field_{field_type}` | কাস্টম ফিল্ড রেন্ডারিং |

---

## নতুন সেকশন যোগ করা

### পদ্ধতি ১: ফিল্টার ব্যবহার করে (অন্য প্লাগিন থেকে)

```php
<?php
/**
 * অন্য প্লাগিন থেকে 'my_target_panel' প্যানেলে নতুন সেকশন যোগ
 */
add_filter('bizzplugin_panel_sections_my_target_panel', function($sections, $panel_id) {
    
    // নতুন সেকশন যোগ করুন
    $sections['my_addon_section'] = array(
        'id'          => 'my_addon_section',
        'title'       => __('আমার অ্যাডঅন সেটিংস', 'my-textdomain'),
        'description' => __('এখানে অ্যাডঅনের সেটিংস কনফিগার করুন।', 'my-textdomain'),
        'icon'        => 'dashicons dashicons-admin-plugins',
        'fields'      => array(
            array(
                'id'      => 'addon_enable',
                'type'    => 'switch',
                'title'   => __('অ্যাডঅন সক্রিয় করুন', 'my-textdomain'),
                'default' => '0',
            ),
            array(
                'id'          => 'addon_text',
                'type'        => 'text',
                'title'       => __('অ্যাডঅন টেক্সট', 'my-textdomain'),
                'description' => __('আপনার কাস্টম টেক্সট লিখুন।', 'my-textdomain'),
                'default'     => '',
            ),
        ),
    );
    
    return $sections;
}, 10, 2);
```

### পদ্ধতি ২: Chainable API ব্যবহার করে

```php
<?php
// Framework রেডি হলে
add_action('bizzplugin_framework_loaded', function($framework) {
    
    // টার্গেট প্যানেল নিন
    $panel = $framework->get_panel('my_target_panel');
    
    if ($panel) {
        // Chainable API দিয়ে সেকশন যোগ করুন
        $panel->add_section(array(
            'id'     => 'my_new_section',
            'title'  => __('নতুন সেকশন', 'my-textdomain'),
            'icon'   => 'dashicons dashicons-admin-generic',
            'fields' => array(
                array(
                    'id'      => 'new_field',
                    'type'    => 'text',
                    'title'   => __('নতুন ফিল্ড', 'my-textdomain'),
                    'default' => '',
                ),
            ),
        ));
    }
});
```

---

## নতুন ফিল্ড যোগ করা

### বিদ্যমান সেকশনে ফিল্ড যোগ

```php
<?php
/**
 * 'my_target_panel' প্যানেলের 'general' সেকশনে নতুন ফিল্ড যোগ
 */
add_filter('bizzplugin_section_fields_my_target_panel', function($fields, $section_id, $panel_id) {
    
    // শুধু 'general' সেকশনে ফিল্ড যোগ করুন
    if ($section_id === 'general') {
        $fields[] = array(
            'id'          => 'my_custom_field',
            'type'        => 'text',
            'title'       => __('কাস্টম ফিল্ড', 'my-textdomain'),
            'description' => __('এটি আমার কাস্টম ফিল্ড।', 'my-textdomain'),
            'default'     => '',
            'placeholder' => __('এখানে লিখুন...', 'my-textdomain'),
        );
    }
    
    return $fields;
}, 10, 3);
```

### নির্দিষ্ট ফিল্ডের পরে ফিল্ড যোগ

```php
<?php
add_filter('bizzplugin_section_fields_my_target_panel', function($fields, $section_id, $panel_id) {
    
    if ($section_id === 'general') {
        $new_fields = array();
        
        foreach ($fields as $field) {
            $new_fields[] = $field;
            
            // 'site_title' ফিল্ডের পরে নতুন ফিল্ড যোগ
            if ($field['id'] === 'site_title') {
                $new_fields[] = array(
                    'id'      => 'site_subtitle',
                    'type'    => 'text',
                    'title'   => __('সাইট সাবটাইটেল', 'my-textdomain'),
                    'default' => '',
                );
            }
        }
        
        return $new_fields;
    }
    
    return $fields;
}, 10, 3);
```

---

## কাস্টম ফিল্ড টাইপ তৈরি

### স্টেপ ১: ফিল্ড রেন্ডার অ্যাকশন রেজিস্টার করুন

```php
<?php
/**
 * কাস্টম 'my_rating' ফিল্ড টাইপ
 */
add_action('bizzplugin_render_field_my_rating', function($field, $value, $is_disabled) {
    $max_rating = isset($field['max']) ? intval($field['max']) : 5;
    $disabled = $is_disabled ? ' disabled="disabled"' : '';
    ?>
    <div class="bizzplugin-rating-field">
        <input 
            type="hidden" 
            id="<?php echo esc_attr($field['id']); ?>"
            name="<?php echo esc_attr($field['id']); ?>"
            value="<?php echo esc_attr($value); ?>"
            class="rating-value"
        />
        <div class="rating-stars">
            <?php for ($i = 1; $i <= $max_rating; $i++) : ?>
                <span 
                    class="rating-star <?php echo $i <= $value ? 'active' : ''; ?>"
                    data-value="<?php echo $i; ?>"
                    <?php echo $disabled; ?>
                >★</span>
            <?php endfor; ?>
        </div>
    </div>
    <?php
}, 10, 3);
```

### স্টেপ ২: JavaScript যোগ করুন

```php
<?php
add_action('admin_footer', function() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('.bizzplugin-rating-field').on('click', '.rating-star', function() {
            if ($(this).attr('disabled')) return;
            
            var value = $(this).data('value');
            var $field = $(this).closest('.bizzplugin-rating-field');
            
            $field.find('.rating-value').val(value);
            $field.find('.rating-star').each(function(index) {
                $(this).toggleClass('active', index < value);
            });
        });
    });
    </script>
    <?php
});
```

### স্টেপ ৩: CSS যোগ করুন

```php
<?php
add_action('admin_head', function() {
    ?>
    <style>
    .bizzplugin-rating-field .rating-stars {
        display: flex;
        gap: 5px;
    }
    .bizzplugin-rating-field .rating-star {
        font-size: 24px;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }
    .bizzplugin-rating-field .rating-star.active {
        color: #ffb900;
    }
    .bizzplugin-rating-field .rating-star:hover {
        color: #ffb900;
    }
    </style>
    <?php
});
```

### স্টেপ ৪: ফিল্ড ব্যবহার করুন

```php
<?php
array(
    'id'      => 'product_rating',
    'type'    => 'my_rating',  // কাস্টম টাইপ
    'title'   => __('প্রোডাক্ট রেটিং', 'my-textdomain'),
    'max'     => 5,
    'default' => 3,
)
```

---

## শর্টকোড ফিল্ড উদাহরণ (Copy বাটন সহ)

এই উদাহরণে আমরা একটি read-only শর্টকোড ফিল্ড তৈরি করব যেখানে Copy বাটন থাকবে।

### সম্পূর্ণ কোড

```php
<?php
/**
 * Plugin Name: My Shortcode Display
 * Description: Shows plugin shortcode with copy button
 */

// Framework রেডি হলে ফিল্ড যোগ করুন
add_action('init', 'my_add_shortcode_field');

function my_add_shortcode_field() {
    // Framework চেক করুন
    if (!function_exists('bizzplugin_framework')) {
        return;
    }
    
    // কাস্টম 'shortcode_display' ফিল্ড টাইপ রেন্ডার করুন
    add_action('bizzplugin_render_field_shortcode_display', 'render_shortcode_display_field', 10, 3);
    
    // টার্গেট প্যানেলে ফিল্ড যোগ করুন
    add_filter('bizzplugin_section_fields_my_target_panel', 'add_shortcode_field_to_panel', 10, 3);
}

/**
 * শর্টকোড ডিসপ্লে ফিল্ড রেন্ডার
 */
function render_shortcode_display_field($field, $value, $is_disabled) {
    $shortcode = isset($field['shortcode']) ? $field['shortcode'] : '[my_shortcode]';
    $field_id = esc_attr($field['id']);
    ?>
    <div class="bizzplugin-shortcode-display-wrapper">
        <div class="bizzplugin-shortcode-field">
            <input 
                type="text" 
                id="<?php echo $field_id; ?>"
                class="bizzplugin-input bizzplugin-shortcode-input"
                value="<?php echo esc_attr($shortcode); ?>"
                readonly="readonly"
            />
            <button 
                type="button" 
                class="button bizzplugin-copy-shortcode-btn"
                data-copy-target="<?php echo $field_id; ?>"
                title="<?php esc_attr_e('Copy Shortcode', 'my-textdomain'); ?>"
            >
                <span class="dashicons dashicons-clipboard"></span>
                <span class="copy-text"><?php esc_html_e('Copy', 'my-textdomain'); ?></span>
            </button>
        </div>
        <?php if (!empty($field['usage_hint'])) : ?>
            <p class="bizzplugin-shortcode-hint">
                <?php echo esc_html($field['usage_hint']); ?>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * প্যানেলে শর্টকোড ফিল্ড যোগ
 */
function add_shortcode_field_to_panel($fields, $section_id, $panel_id) {
    // শুধু 'general' সেকশনে যোগ করুন
    if ($section_id === 'general') {
        $fields[] = array(
            'id'          => 'plugin_shortcode',
            'type'        => 'shortcode_display',
            'title'       => __('প্লাগিন শর্টকোড', 'my-textdomain'),
            'description' => __('এই শর্টকোড কপি করে আপনার পেজ/পোস্টে ব্যবহার করুন।', 'my-textdomain'),
            'shortcode'   => '[my_plugin id="123"]',
            'usage_hint'  => __('আপনি এই শর্টকোড যেকোনো পেজ বা পোস্টে পেস্ট করতে পারেন।', 'my-textdomain'),
        );
    }
    
    return $fields;
}

/**
 * CSS স্টাইল যোগ করুন
 */
add_action('admin_head', function() {
    ?>
    <style>
    .bizzplugin-shortcode-display-wrapper {
        max-width: 500px;
    }
    .bizzplugin-shortcode-field {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .bizzplugin-shortcode-input {
        flex: 1;
        font-family: monospace;
        background-color: #f0f0f1;
        border: 1px solid #c3c4c7;
        padding: 8px 12px;
        color: #1e1e1e;
    }
    .bizzplugin-copy-shortcode-btn {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px !important;
    }
    .bizzplugin-copy-shortcode-btn .dashicons {
        font-size: 16px;
        width: 16px;
        height: 16px;
    }
    .bizzplugin-copy-shortcode-btn.copied {
        background-color: #00a32a !important;
        border-color: #00a32a !important;
        color: #fff !important;
    }
    .bizzplugin-shortcode-hint {
        margin-top: 8px;
        color: #646970;
        font-size: 12px;
    }
    </style>
    <?php
});

/**
 * JavaScript যোগ করুন
 */
add_action('admin_footer', function() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Copy বাটন ক্লিক হ্যান্ডলার
        $(document).on('click', '.bizzplugin-copy-shortcode-btn', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var targetId = $btn.data('copy-target');
            var $input = $('#' + targetId);
            var shortcode = $input.val();
            
            // Clipboard API ব্যবহার করুন
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(shortcode).then(function() {
                    showCopiedFeedback($btn);
                }).catch(function() {
                    fallbackCopy($input, $btn);
                });
            } else {
                fallbackCopy($input, $btn);
            }
        });
        
        // Fallback copy method
        function fallbackCopy($input, $btn) {
            $input.select();
            $input[0].setSelectionRange(0, 99999);
            document.execCommand('copy');
            showCopiedFeedback($btn);
        }
        
        // Copy হয়েছে ফিডব্যাক দেখান
        function showCopiedFeedback($btn) {
            var originalText = $btn.find('.copy-text').text();
            
            $btn.addClass('copied');
            $btn.find('.copy-text').text('Copied!');
            $btn.find('.dashicons').removeClass('dashicons-clipboard').addClass('dashicons-yes');
            
            setTimeout(function() {
                $btn.removeClass('copied');
                $btn.find('.copy-text').text(originalText);
                $btn.find('.dashicons').removeClass('dashicons-yes').addClass('dashicons-clipboard');
            }, 2000);
        }
    });
    </script>
    <?php
});
```

### ব্যবহার উদাহরণ

উপরের কোড আপনার প্লাগইনে যোগ করলে, 'my_target_panel' প্যানেলের 'general' সেকশনে একটি শর্টকোড ফিল্ড দেখাবে। ফিল্ডটি দেখতে এরকম হবে:

```
+--------------------------------------------------+
| প্লাগিন শর্টকোড                                    |
| +------------------------------------+ +--------+ |
| | [my_plugin id="123"]               | | Copy   | |
| +------------------------------------+ +--------+ |
| আপনি এই শর্টকোড যেকোনো পেজ বা পোস্টে পেস্ট করতে পারেন।|
+--------------------------------------------------+
```

---

## অন্য প্লাগিন থেকে নির্দিষ্ট প্যানেলে কাজ করা

### সঠিক উপায় (রিকমেন্ডেড)

```php
<?php
/**
 * Plugin Name: My Addon Plugin
 * Description: Adds features to Target Plugin
 */

// Framework loaded হলে কাজ করুন
add_action('bizzplugin_framework_loaded', function($framework) {
    
    // টার্গেট প্যানেল ID জানা থাকতে হবে
    $target_panel_id = 'target_plugin_panel';
    
    // প্যানেল আছে কিনা চেক করুন
    $panel = $framework->get_panel($target_panel_id);
    
    if (!$panel) {
        return; // টার্গেট প্যানেল নেই, বাহির হয়ে যান
    }
    
    // এখন নিরাপদে ফিল্ড/সেকশন যোগ করুন
    $panel->add_section(array(
        'id'     => 'my_addon',
        'title'  => __('Addon Settings', 'my-addon'),
        'icon'   => 'dashicons dashicons-admin-plugins',
        'fields' => array(
            array(
                'id'      => 'addon_option',
                'type'    => 'switch',
                'title'   => __('Enable Addon', 'my-addon'),
                'default' => '0',
            ),
        ),
    ));
});
```

### ফিল্টার ব্যবহার করে (বিকল্প উপায়)

```php
<?php
// শুধু নির্দিষ্ট প্যানেলে ফিল্ড যোগ করুন
add_filter('bizzplugin_section_fields_target_plugin_panel', function($fields, $section_id, $panel_id) {
    
    // শুধু 'settings' সেকশনে
    if ($section_id === 'settings') {
        $fields[] = array(
            'id'      => 'addon_feature',
            'type'    => 'checkbox',
            'title'   => __('Addon Feature', 'my-addon'),
            'label'   => __('Enable this addon feature', 'my-addon'),
            'default' => '0',
        );
    }
    
    return $fields;
}, 10, 3);
```

### ভুল উপায় (এড়িয়ে চলুন)

```php
<?php
// ❌ এভাবে করবেন না - সব প্যানেলে প্রভাব পড়বে!
add_filter('bizzplugin_section_fields', function($fields, $section_id, $panel_id) {
    // এটি সব প্লাগইনের সব প্যানেলে চলবে
    $fields[] = array(
        'id'   => 'my_field',
        'type' => 'text',
        ...
    );
    return $fields;
}, 10, 3);

// ✅ এভাবে করুন - শুধু নির্দিষ্ট প্যানেলে
add_filter('bizzplugin_section_fields_my_specific_panel', function($fields, $section_id, $panel_id) {
    // শুধু 'my_specific_panel' প্যানেলে চলবে
    ...
}, 10, 3);
```

---

## সেরা অভ্যাস

### ১. সবসময় প্যানেল-নির্দিষ্ট হুক ব্যবহার করুন

```php
// ✅ সঠিক
add_filter('bizzplugin_section_fields_my_panel', ...);
add_filter('bizzplugin_panel_config_my_panel', ...);

// ❌ ভুল
add_filter('bizzplugin_section_fields', ...);
```

### ২. Framework রেডি হওয়া পর্যন্ত অপেক্ষা করুন

```php
// ✅ সঠিক
add_action('bizzplugin_framework_loaded', function($framework) {
    // এখানে কাজ করুন
});

// ❌ ভুল - Framework লোড না হতে পারে
add_action('init', function() {
    $panel = bizzplugin_framework()->get_panel('my_panel');
});
```

### ৩. প্যানেল আছে কিনা চেক করুন

```php
// ✅ সঠিক
$panel = $framework->get_panel('target_panel');
if ($panel) {
    $panel->add_field(...);
}

// ❌ ভুল
$framework->get_panel('target_panel')->add_field(...); // NULL হলে error হবে
```

### ৪. Sanitization নিশ্চিত করুন

```php
// কাস্টম ফিল্ডে সবসময় escaping ব্যবহার করুন
echo esc_attr($value);
echo esc_html($label);
echo esc_url($url);
```

### ৫. Translation-ready কোড লিখুন

```php
// ✅ সঠিক
'title' => __('My Field', 'my-textdomain'),

// ❌ ভুল
'title' => 'My Field',
```

---

## পরবর্তী পদক্ষেপ

- [Filters & Hooks](en/filters-hooks.md) - সব উপলব্ধ হুকের তালিকা
- [Field Types](en/field-types.md) - সব ফিল্ড টাইপ
- [Examples](en/examples.md) - আরও কোড উদাহরণ
- [Webhook Details](weebhook-details/) - ওয়েবহুক ডকুমেন্টেশন
