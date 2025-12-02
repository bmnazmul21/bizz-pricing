# কলব্যাক টাইপ (Callback Type) - সম্পূর্ণ গাইড

BizzPlugin Options Framework এ `callback` টাইপের ফিল্ড ব্যবহার করে আপনি সম্পূর্ণ কাস্টম ফিল্ড রেন্ডার করতে পারবেন। এই ডকুমেন্টে callback type সম্পর্কে বিস্তারিত আলোচনা করা হলো।

## সূচিপত্র

1. [কলব্যাক টাইপ কী?](#কলব্যাক-টাইপ-কী)
2. [বেসিক সিনট্যাক্স](#বেসিক-সিনট্যাক্স)
3. [পূর্ণাঙ্গ উদাহরণ](#পূর্ণাঙ্গ-উদাহরণ)
4. [কলব্যাক ফাংশনের প্যারামিটার](#কলব্যাক-ফাংশনের-প্যারামিটার)
5. [প্র্যাক্টিক্যাল ব্যবহার](#প্র্যাক্টিক্যাল-ব্যবহার)

---

## কলব্যাক টাইপ কী?

`callback` টাইপ একটি বিশেষ ফিল্ড টাইপ যা আপনাকে নিজের কাস্টম HTML এবং PHP কোড দিয়ে ফিল্ড রেন্ডার করার সুযোগ দেয়। এটি তখন ব্যবহার করা হয় যখন:

- বিল্ট-ইন ফিল্ড টাইপগুলো আপনার চাহিদা পূরণ করতে পারছে না
- সম্পূর্ণ কাস্টম UI প্রয়োজন
- থার্ড-পার্টি লাইব্রেরি ইন্টিগ্রেট করতে চান
- জটিল ইনপুট প্যাটার্ন দরকার

---

## বেসিক সিনট্যাক্স

```php
array(
    'id'              => 'my_callback_field',
    'type'            => 'callback',
    'title'           => 'কাস্টম ফিল্ড',
    'description'     => 'এটি একটি কলব্যাক ফিল্ড।',
    'render_callback' => 'my_custom_render_function',
)
```

### render_callback প্যারামিটার

`render_callback` এ তিন ধরনের ভ্যালু দেওয়া যায়:

1. **ফাংশন নাম (string)**:
```php
'render_callback' => 'my_function_name',
```

2. **অ্যানোনিমাস ফাংশন (closure)**:
```php
'render_callback' => function($field, $value, $disabled) {
    // আপনার কোড
},
```

3. **ক্লাস মেথড (array)**:
```php
'render_callback' => array($this, 'method_name'),
// অথবা স্ট্যাটিক মেথড:
'render_callback' => array('ClassName', 'method_name'),
```

---

## পূর্ণাঙ্গ উদাহরণ

### উদাহরণ ১: সিম্পল কাস্টম ফিল্ড

```php
// ফিল্ড ডেফিনিশন
array(
    'id'              => 'custom_greeting',
    'type'            => 'callback',
    'title'           => 'কাস্টম গ্রিটিং',
    'description'     => 'আপনার কাস্টম গ্রিটিং মেসেজ লিখুন।',
    'render_callback' => 'render_custom_greeting_field',
)

// কলব্যাক ফাংশন
function render_custom_greeting_field($field, $value, $is_disabled) {
    $disabled = $is_disabled ? ' disabled="disabled"' : '';
    ?>
    <div class="custom-greeting-field">
        <input 
            type="text" 
            id="<?php echo esc_attr($field['id']); ?>"
            name="<?php echo esc_attr($field['id']); ?>"
            value="<?php echo esc_attr($value); ?>"
            class="bizzplugin-input"
            placeholder="আপনার মেসেজ লিখুন..."
            <?php echo $disabled; ?>
        />
        <p class="field-preview">
            প্রিভিউ: <strong><?php echo esc_html($value ?: 'কোনো মেসেজ নেই'); ?></strong>
        </p>
    </div>
    <?php
}
```

### উদাহরণ ২: শর্টকোড ডিসপ্লে ফিল্ড (Copy বাটন সহ)

```php
// ফিল্ড ডেফিনিশন
array(
    'id'              => 'plugin_shortcode',
    'type'            => 'callback',
    'title'           => 'প্লাগিন শর্টকোড',
    'description'     => 'এই শর্টকোড কপি করে আপনার পেজ/পোস্টে ব্যবহার করুন।',
    'shortcode'       => '[my_plugin id="123"]', // কাস্টম প্যারামিটার
    'render_callback' => 'render_shortcode_display',
)

// কলব্যাক ফাংশন
function render_shortcode_display($field, $value, $is_disabled) {
    $shortcode = isset($field['shortcode']) ? $field['shortcode'] : '[shortcode]';
    $field_id = esc_attr($field['id']);
    ?>
    <div class="bizzplugin-shortcode-display">
        <div class="shortcode-field-wrap">
            <input 
                type="text" 
                id="<?php echo $field_id; ?>"
                class="bizzplugin-input shortcode-input"
                value="<?php echo esc_attr($shortcode); ?>"
                readonly="readonly"
            />
            <button 
                type="button" 
                class="button copy-shortcode-btn"
                data-copy-target="<?php echo $field_id; ?>"
            >
                <span class="dashicons dashicons-clipboard"></span>
                কপি করুন
            </button>
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('.copy-shortcode-btn').on('click', function(e) {
            e.preventDefault();
            var targetId = $(this).data('copy-target');
            var input = document.getElementById(targetId);
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value);
            
            var $btn = $(this);
            $btn.text('কপি হয়েছে!');
            setTimeout(function() {
                $btn.html('<span class="dashicons dashicons-clipboard"></span> কপি করুন');
            }, 2000);
        });
    });
    </script>
    <?php
}
```

### উদাহরণ ৩: রেটিং ফিল্ড

```php
// ফিল্ড ডেফিনিশন
array(
    'id'              => 'product_rating',
    'type'            => 'callback',
    'title'           => 'প্রোডাক্ট রেটিং',
    'description'     => 'প্রোডাক্টের রেটিং দিন।',
    'max_stars'       => 5, // কাস্টম প্যারামিটার
    'render_callback' => 'render_rating_field',
)

// কলব্যাক ফাংশন
function render_rating_field($field, $value, $is_disabled) {
    $max_stars = isset($field['max_stars']) ? intval($field['max_stars']) : 5;
    $current_value = intval($value);
    $disabled = $is_disabled ? ' disabled="disabled"' : '';
    ?>
    <div class="bizzplugin-rating-field">
        <input 
            type="hidden" 
            id="<?php echo esc_attr($field['id']); ?>"
            name="<?php echo esc_attr($field['id']); ?>"
            value="<?php echo esc_attr($current_value); ?>"
            class="rating-value"
        />
        <div class="rating-stars">
            <?php for ($i = 1; $i <= $max_stars; $i++) : ?>
                <span 
                    class="rating-star <?php echo $i <= $current_value ? 'active' : ''; ?>"
                    data-value="<?php echo $i; ?>"
                    <?php echo $disabled; ?>
                >★</span>
            <?php endfor; ?>
        </div>
        <span class="rating-text"><?php echo $current_value; ?>/<?php echo $max_stars; ?></span>
    </div>
    <style>
    .bizzplugin-rating-field .rating-stars {
        display: inline-flex;
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
    <script>
    jQuery(document).ready(function($) {
        $('.bizzplugin-rating-field .rating-star').on('click', function() {
            if ($(this).attr('disabled')) return;
            
            var value = $(this).data('value');
            var $field = $(this).closest('.bizzplugin-rating-field');
            
            $field.find('.rating-value').val(value).trigger('change');
            $field.find('.rating-star').each(function(index) {
                $(this).toggleClass('active', index < value);
            });
            $field.find('.rating-text').text(value + '/<?php echo $max_stars; ?>');
        });
    });
    </script>
    <?php
}
```

---

## কলব্যাক ফাংশনের প্যারামিটার

কলব্যাক ফাংশন তিনটি প্যারামিটার রিসিভ করে:

| প্যারামিটার | টাইপ | বর্ণনা |
|-------------|------|--------|
| `$field` | array | ফিল্ড কনফিগারেশন অ্যারে (id, title, description সহ সব প্যারামিটার) |
| `$value` | mixed | ফিল্ডের বর্তমান সেভ করা ভ্যালু |
| `$is_disabled` | bool | ফিল্ড ডিসেবল কিনা (প্রিমিয়াম ফিচারের জন্য) |

### $field অ্যারের উপাদান

```php
$field = array(
    'id'              => 'field_id',
    'type'            => 'callback',
    'title'           => 'ফিল্ড টাইটেল',
    'description'     => 'ফিল্ড বর্ণনা',
    'default'         => 'ডিফল্ট ভ্যালু',
    'render_callback' => 'function_name',
    // আপনার যোগ করা যেকোনো কাস্টম প্যারামিটার
    'custom_param_1'  => 'value_1',
    'custom_param_2'  => 'value_2',
);
```

---

## প্র্যাক্টিক্যাল ব্যবহার

### ক্লাস মেথড হিসেবে ব্যবহার

```php
class My_Plugin_Settings {
    
    public function __construct() {
        add_action('init', array($this, 'register_settings'));
    }
    
    public function register_settings() {
        $framework = bizzplugin_framework();
        
        $panel = $framework->create_panel(array(
            'id' => 'my_plugin',
            'title' => 'My Plugin Settings',
            // ... অন্যান্য কনফিগ
        ));
        
        $panel->add_section(array(
            'id' => 'main_section',
            'title' => 'Main Settings',
            'fields' => array(
                array(
                    'id'              => 'my_custom_field',
                    'type'            => 'callback',
                    'title'           => 'কাস্টম ফিল্ড',
                    'render_callback' => array($this, 'render_custom_field'),
                ),
            ),
        ));
    }
    
    public function render_custom_field($field, $value, $is_disabled) {
        // আপনার কাস্টম রেন্ডারিং লজিক
        ?>
        <input 
            type="text" 
            name="<?php echo esc_attr($field['id']); ?>" 
            value="<?php echo esc_attr($value); ?>"
        />
        <?php
    }
}

new My_Plugin_Settings();
```

### অ্যানোনিমাস ফাংশন ব্যবহার

```php
array(
    'id'              => 'inline_field',
    'type'            => 'callback',
    'title'           => 'ইনলাইন ফিল্ড',
    'render_callback' => function($field, $value, $disabled) {
        ?>
        <div class="inline-field">
            <input 
                type="text" 
                name="<?php echo esc_attr($field['id']); ?>" 
                value="<?php echo esc_attr($value); ?>"
                class="bizzplugin-input"
            />
        </div>
        <?php
    },
)
```

---

## গুরুত্বপূর্ণ টিপস

1. **Escaping ব্যবহার করুন**: সবসময় `esc_attr()`, `esc_html()`, `esc_url()` ব্যবহার করুন
2. **নাম অ্যাট্রিবিউট**: `name="<?php echo esc_attr($field['id']); ?>"` অবশ্যই সেট করুন যাতে ভ্যালু সেভ হয়
3. **Disabled স্টেট**: `$is_disabled` চেক করে ফিল্ড ডিসেবল করুন
4. **CSS Conflicts এড়িয়ে চলুন**: ইউনিক ক্লাস নাম ব্যবহার করুন
5. **JavaScript**: `jQuery(document).ready()` এর মধ্যে কোড রাখুন

---

## পরবর্তী পড়ুন

- [কলব্যাক ফাংশন বিস্তারিত](callback-functions.md)
- [স্যানিটাইজ এবং ভ্যালিডেশন কলব্যাক](sanitize-validation-callbacks.md)
- [নতুন ফিল্ড টাইপ তৈরি](custom-field-type.md)
