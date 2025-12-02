# নতুন ফিল্ড টাইপ তৈরি - সম্পূর্ণ গাইড

BizzPlugin Options Framework এ আপনি নিজের কাস্টম ফিল্ড টাইপ তৈরি করতে পারেন। এই ডকুমেন্টে ধাপে ধাপে নতুন ফিল্ড টাইপ তৈরির প্রক্রিয়া বর্ণনা করা হলো।

## সূচিপত্র

1. [কাস্টম ফিল্ড টাইপ কেন তৈরি করবেন?](#কাস্টম-ফিল্ড-টাইপ-কেন-তৈরি-করবেন)
2. [তৈরির ধাপসমূহ](#তৈরির-ধাপসমূহ)
3. [সম্পূর্ণ উদাহরণ: Rating Field](#সম্পূর্ণ-উদাহরণ-rating-field)
4. [উদাহরণ: Icon Picker Field](#উদাহরণ-icon-picker-field)
5. [উদাহরণ: Tag Input Field](#উদাহরণ-tag-input-field)
6. [কাস্টম স্যানিটাইজেশন যোগ করা](#কাস্টম-স্যানিটাইজেশন-যোগ-করা)
7. [JavaScript ইন্টিগ্রেশন](#javascript-ইন্টিগ্রেশন)
8. [CSS স্টাইলিং](#css-স্টাইলিং)
9. [সেরা অভ্যাস](#সেরা-অভ্যাস)

---

## কাস্টম ফিল্ড টাইপ কেন তৈরি করবেন?

কাস্টম ফিল্ড টাইপ তৈরি করা উচিত যখন:

- বিল্ট-ইন ফিল্ড টাইপগুলো আপনার চাহিদা পূরণ করে না
- বারবার একই ধরনের কাস্টম ফিল্ড ব্যবহার করতে হয়
- থার্ড-পার্টি লাইব্রেরি ইন্টিগ্রেট করতে চান
- বিশেষ UI/UX প্রয়োজন

---

## তৈরির ধাপসমূহ

নতুন ফিল্ড টাইপ তৈরিতে তিনটি প্রধান ধাপ:

### ধাপ ১: রেন্ডার হুক রেজিস্টার করুন

```php
add_action('bizzplugin_render_field_my_field_type', 'render_my_field_type', 10, 3);

function render_my_field_type($field, $value, $is_disabled) {
    // HTML রেন্ডার করুন
}
```

### ধাপ ২: স্যানিটাইজেশন ফিল্টার যোগ করুন (ঐচ্ছিক)

```php
add_filter('bizzplugin_sanitize_field_my_field_type', 'sanitize_my_field_type', 10, 2);

function sanitize_my_field_type($value, $field) {
    // স্যানিটাইজ করে রিটার্ন করুন
    return $value;
}
```

### ধাপ ৩: CSS এবং JavaScript যোগ করুন

```php
add_action('admin_enqueue_scripts', function() {
    wp_enqueue_style('my-field-style', ...);
    wp_enqueue_script('my-field-script', ...);
});
```

---

## সম্পূর্ণ উদাহরণ: Rating Field

এখানে একটি স্টার রেটিং ফিল্ড তৈরির সম্পূর্ণ কোড দেওয়া হলো।

### ফাইল স্ট্রাকচার

```
my-plugin/
├── includes/
│   └── fields/
│       └── class-rating-field.php
├── assets/
│   ├── css/
│   │   └── rating-field.css
│   └── js/
│       └── rating-field.js
└── my-plugin.php
```

### class-rating-field.php

```php
<?php
/**
 * Rating Field Type
 * 
 * @package MyPlugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class My_Rating_Field {
    
    /**
     * Constructor
     */
    public function __construct() {
        // রেন্ডার হুক
        add_action('bizzplugin_render_field_rating', array($this, 'render_field'), 10, 3);
        
        // স্যানিটাইজেশন ফিল্টার
        add_filter('bizzplugin_sanitize_field_rating', array($this, 'sanitize_field'), 10, 2);
        
        // স্ক্রিপ্ট এবং স্টাইল
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    /**
     * ফিল্ড রেন্ডার
     */
    public function render_field($field, $value, $is_disabled) {
        // ডিফল্ট প্যারামিটার
        $max_stars = isset($field['max']) ? intval($field['max']) : 5;
        $allow_half = isset($field['allow_half']) ? (bool) $field['allow_half'] : false;
        $star_size = isset($field['size']) ? $field['size'] : 'medium';
        
        // বর্তমান ভ্যালু
        $current_value = is_numeric($value) ? floatval($value) : 0;
        
        // ডিসেবল অ্যাট্রিবিউট
        $disabled_attr = $is_disabled ? ' disabled="disabled"' : '';
        $disabled_class = $is_disabled ? ' disabled' : '';
        
        ?>
        <div class="bizzplugin-rating-field<?php echo esc_attr($disabled_class); ?>" 
             data-max="<?php echo esc_attr($max_stars); ?>"
             data-allow-half="<?php echo $allow_half ? '1' : '0'; ?>"
             data-size="<?php echo esc_attr($star_size); ?>">
            
            <input 
                type="hidden" 
                id="<?php echo esc_attr($field['id']); ?>"
                name="<?php echo esc_attr($field['id']); ?>"
                value="<?php echo esc_attr($current_value); ?>"
                class="rating-value"
                <?php echo $disabled_attr; ?>
            />
            
            <div class="rating-stars-container">
                <?php for ($i = 1; $i <= $max_stars; $i++) : ?>
                    <?php
                    $star_class = 'rating-star';
                    if ($i <= $current_value) {
                        $star_class .= ' active full';
                    } elseif ($allow_half && ($i - 0.5) <= $current_value) {
                        $star_class .= ' active half';
                    }
                    ?>
                    <span 
                        class="<?php echo esc_attr($star_class); ?>"
                        data-value="<?php echo $i; ?>"
                        data-half-value="<?php echo $i - 0.5; ?>"
                    >
                        <span class="star-empty">☆</span>
                        <span class="star-full">★</span>
                        <?php if ($allow_half) : ?>
                            <span class="star-half">★</span>
                        <?php endif; ?>
                    </span>
                <?php endfor; ?>
            </div>
            
            <span class="rating-text">
                <span class="rating-current"><?php echo esc_html($current_value); ?></span>
                / <?php echo esc_html($max_stars); ?>
            </span>
            
            <?php if (!$is_disabled) : ?>
                <button type="button" class="button-link rating-clear" title="<?php esc_attr_e('রেটিং মুছুন', 'my-plugin'); ?>">
                    <span class="dashicons dashicons-no-alt"></span>
                </button>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * ফিল্ড স্যানিটাইজ
     */
    public function sanitize_field($value, $field) {
        $max_stars = isset($field['max']) ? intval($field['max']) : 5;
        $allow_half = isset($field['allow_half']) ? (bool) $field['allow_half'] : false;
        
        // ফ্লোট হিসেবে নিন
        $value = floatval($value);
        
        // রেঞ্জ চেক
        if ($value < 0) {
            $value = 0;
        }
        if ($value > $max_stars) {
            $value = $max_stars;
        }
        
        // হাফ অনুমোদিত না হলে রাউন্ড করুন
        if (!$allow_half) {
            $value = round($value);
        } else {
            // ০.৫ এর মাল্টিপলে রাউন্ড
            $value = round($value * 2) / 2;
        }
        
        return $value;
    }
    
    /**
     * Assets লোড
     */
    public function enqueue_assets($hook) {
        // শুধু সেটিংস পেজে লোড করুন
        if (strpos($hook, 'bizzplugin') === false && strpos($hook, 'settings') === false) {
            return;
        }
        
        wp_enqueue_style(
            'bizzplugin-rating-field',
            plugin_dir_url(__FILE__) . '../../assets/css/rating-field.css',
            array(),
            '1.0.0'
        );
        
        wp_enqueue_script(
            'bizzplugin-rating-field',
            plugin_dir_url(__FILE__) . '../../assets/js/rating-field.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
}

// ইনিশিয়ালাইজ
new My_Rating_Field();
```

### rating-field.css

```css
/* Rating Field Styles */
.bizzplugin-rating-field {
    display: flex;
    align-items: center;
    gap: 10px;
}

.bizzplugin-rating-field.disabled {
    opacity: 0.6;
    pointer-events: none;
}

.rating-stars-container {
    display: flex;
    gap: 4px;
}

.rating-star {
    position: relative;
    font-size: 24px;
    cursor: pointer;
    user-select: none;
    transition: transform 0.1s ease;
}

/* সাইজ ভেরিয়েশন */
.bizzplugin-rating-field[data-size="small"] .rating-star {
    font-size: 16px;
}

.bizzplugin-rating-field[data-size="large"] .rating-star {
    font-size: 32px;
}

.rating-star:hover {
    transform: scale(1.1);
}

.rating-star .star-empty,
.rating-star .star-full,
.rating-star .star-half {
    position: absolute;
    left: 0;
    top: 0;
}

.rating-star .star-empty {
    color: #ddd;
}

.rating-star .star-full,
.rating-star .star-half {
    color: #ffb900;
    opacity: 0;
}

.rating-star.active .star-full {
    opacity: 1;
}

.rating-star.active.half .star-full {
    opacity: 0;
}

.rating-star.active.half .star-half {
    opacity: 1;
    clip-path: inset(0 50% 0 0);
}

/* হোভার স্টেট */
.rating-star.hover .star-full {
    opacity: 0.5;
}

.rating-text {
    font-size: 14px;
    color: #666;
    min-width: 50px;
}

.rating-clear {
    color: #dc3232;
    padding: 2px;
}

.rating-clear:hover {
    color: #a00;
}
```

### rating-field.js

```javascript
/**
 * Rating Field JavaScript
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        initRatingFields();
    });
    
    function initRatingFields() {
        $(document).on('click', '.bizzplugin-rating-field:not(.disabled) .rating-star', function(e) {
            var $star = $(this);
            var $field = $star.closest('.bizzplugin-rating-field');
            var allowHalf = $field.data('allow-half') === 1;
            var value = parseFloat($star.data('value'));
            
            // হাফ স্টার চেক
            if (allowHalf) {
                var starRect = this.getBoundingClientRect();
                var clickX = e.clientX - starRect.left;
                var starWidth = starRect.width;
                
                if (clickX < starWidth / 2) {
                    value = parseFloat($star.data('half-value'));
                }
            }
            
            updateRating($field, value);
        });
        
        // হোভার ইফেক্ট
        $(document).on('mouseenter', '.bizzplugin-rating-field:not(.disabled) .rating-star', function() {
            var $star = $(this);
            var $field = $star.closest('.bizzplugin-rating-field');
            var value = parseFloat($star.data('value'));
            
            $field.find('.rating-star').each(function(index) {
                $(this).toggleClass('hover', index < value);
            });
        });
        
        $(document).on('mouseleave', '.bizzplugin-rating-field:not(.disabled) .rating-stars-container', function() {
            $(this).find('.rating-star').removeClass('hover');
        });
        
        // ক্লিয়ার বাটন
        $(document).on('click', '.bizzplugin-rating-field .rating-clear', function(e) {
            e.preventDefault();
            var $field = $(this).closest('.bizzplugin-rating-field');
            updateRating($field, 0);
        });
    }
    
    function updateRating($field, value) {
        var max = $field.data('max');
        var allowHalf = $field.data('allow-half') === 1;
        
        // ভ্যালু আপডেট
        $field.find('.rating-value').val(value).trigger('change');
        $field.find('.rating-current').text(value);
        
        // স্টার আপডেট
        $field.find('.rating-star').each(function(index) {
            var starValue = index + 1;
            var $star = $(this);
            
            $star.removeClass('active full half');
            
            if (starValue <= value) {
                $star.addClass('active full');
            } else if (allowHalf && (starValue - 0.5) <= value && (starValue - 0.5) > (value - 1)) {
                $star.addClass('active half');
            }
        });
    }
    
})(jQuery);
```

### ফিল্ড ব্যবহার

```php
// ফিল্ড ডেফিনিশন
array(
    'id'         => 'product_rating',
    'type'       => 'rating',
    'title'      => 'প্রোডাক্ট রেটিং',
    'description'=> 'প্রোডাক্টের রেটিং দিন।',
    'default'    => 0,
    'max'        => 5,
    'allow_half' => true,
    'size'       => 'medium', // small, medium, large
)
```

---

## উদাহরণ: Icon Picker Field

Dashicons থেকে আইকন সিলেক্ট করার ফিল্ড।

```php
// রেন্ডার হুক
add_action('bizzplugin_render_field_icon_picker', function($field, $value, $is_disabled) {
    $icons = array(
        'dashicons-admin-site',
        'dashicons-admin-post',
        'dashicons-admin-media',
        'dashicons-admin-links',
        'dashicons-admin-page',
        'dashicons-admin-comments',
        'dashicons-admin-appearance',
        'dashicons-admin-plugins',
        'dashicons-admin-users',
        'dashicons-admin-tools',
        'dashicons-admin-settings',
        'dashicons-admin-network',
        'dashicons-admin-generic',
        'dashicons-admin-home',
        'dashicons-heart',
        'dashicons-star-filled',
        'dashicons-star-empty',
        'dashicons-flag',
        'dashicons-warning',
        'dashicons-yes',
        'dashicons-no',
        'dashicons-plus',
        'dashicons-minus',
    );
    
    $disabled = $is_disabled ? ' disabled' : '';
    ?>
    <div class="bizzplugin-icon-picker<?php echo $disabled; ?>">
        <input 
            type="hidden" 
            id="<?php echo esc_attr($field['id']); ?>"
            name="<?php echo esc_attr($field['id']); ?>"
            value="<?php echo esc_attr($value); ?>"
            class="icon-value"
        />
        
        <div class="selected-icon">
            <?php if (!empty($value)) : ?>
                <span class="dashicons <?php echo esc_attr($value); ?>"></span>
            <?php else : ?>
                <span class="no-icon">আইকন নেই</span>
            <?php endif; ?>
        </div>
        
        <button type="button" class="button icon-picker-toggle" <?php echo $is_disabled ? 'disabled' : ''; ?>>
            আইকন বাছাই করুন
        </button>
        
        <div class="icon-picker-dropdown" style="display: none;">
            <div class="icon-search">
                <input type="text" placeholder="আইকন খুঁজুন..." class="icon-search-input">
            </div>
            <div class="icon-grid">
                <?php foreach ($icons as $icon) : ?>
                    <span class="icon-option <?php echo $value === $icon ? 'selected' : ''; ?>" 
                          data-icon="<?php echo esc_attr($icon); ?>"
                          title="<?php echo esc_attr($icon); ?>">
                        <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
                    </span>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button-link icon-clear">আইকন সরান</button>
        </div>
    </div>
    
    <style>
    .bizzplugin-icon-picker {
        position: relative;
    }
    .bizzplugin-icon-picker .selected-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-right: 10px;
        vertical-align: middle;
    }
    .bizzplugin-icon-picker .selected-icon .dashicons {
        font-size: 24px;
        width: 24px;
        height: 24px;
    }
    .icon-picker-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 100;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-width: 300px;
        margin-top: 5px;
    }
    .icon-search {
        margin-bottom: 10px;
    }
    .icon-search-input {
        width: 100%;
    }
    .icon-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 5px;
        max-height: 200px;
        overflow-y: auto;
    }
    .icon-option {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: 1px solid #ddd;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .icon-option:hover,
    .icon-option.selected {
        background: #2271b1;
        border-color: #2271b1;
        color: #fff;
    }
    .icon-clear {
        margin-top: 10px;
        color: #dc3232;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // টগল ড্রপডাউন
        $('.icon-picker-toggle').on('click', function() {
            $(this).closest('.bizzplugin-icon-picker').find('.icon-picker-dropdown').toggle();
        });
        
        // আইকন সিলেক্ট
        $('.icon-option').on('click', function() {
            var $picker = $(this).closest('.bizzplugin-icon-picker');
            var icon = $(this).data('icon');
            
            $picker.find('.icon-value').val(icon).trigger('change');
            $picker.find('.selected-icon').html('<span class="dashicons ' + icon + '"></span>');
            $picker.find('.icon-option').removeClass('selected');
            $(this).addClass('selected');
            $picker.find('.icon-picker-dropdown').hide();
        });
        
        // সার্চ
        $('.icon-search-input').on('input', function() {
            var search = $(this).val().toLowerCase();
            $(this).closest('.icon-picker-dropdown').find('.icon-option').each(function() {
                var icon = $(this).data('icon').toLowerCase();
                $(this).toggle(icon.indexOf(search) > -1);
            });
        });
        
        // ক্লিয়ার
        $('.icon-clear').on('click', function() {
            var $picker = $(this).closest('.bizzplugin-icon-picker');
            $picker.find('.icon-value').val('').trigger('change');
            $picker.find('.selected-icon').html('<span class="no-icon">আইকন নেই</span>');
            $picker.find('.icon-option').removeClass('selected');
            $picker.find('.icon-picker-dropdown').hide();
        });
        
        // বাইরে ক্লিক করলে বন্ধ
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.bizzplugin-icon-picker').length) {
                $('.icon-picker-dropdown').hide();
            }
        });
    });
    </script>
    <?php
}, 10, 3);

// স্যানিটাইজেশন
add_filter('bizzplugin_sanitize_field_icon_picker', function($value, $field) {
    return sanitize_text_field($value);
}, 10, 2);
```

---

## উদাহরণ: Tag Input Field

ট্যাগ ইনপুট করার ফিল্ড।

```php
add_action('bizzplugin_render_field_tags', function($field, $value, $is_disabled) {
    $tags = is_array($value) ? $value : array();
    $placeholder = isset($field['placeholder']) ? $field['placeholder'] : 'ট্যাগ লিখুন...';
    $max_tags = isset($field['max_tags']) ? intval($field['max_tags']) : 0;
    $disabled = $is_disabled ? ' disabled' : '';
    ?>
    <div class="bizzplugin-tags-field<?php echo $disabled; ?>" 
         data-max-tags="<?php echo esc_attr($max_tags); ?>">
        
        <div class="tags-container">
            <?php foreach ($tags as $index => $tag) : ?>
                <span class="tag-item">
                    <?php echo esc_html($tag); ?>
                    <input type="hidden" name="<?php echo esc_attr($field['id']); ?>[]" value="<?php echo esc_attr($tag); ?>">
                    <button type="button" class="tag-remove">&times;</button>
                </span>
            <?php endforeach; ?>
            
            <input 
                type="text" 
                class="tag-input"
                placeholder="<?php echo esc_attr($placeholder); ?>"
                <?php echo $is_disabled ? 'disabled' : ''; ?>
            />
        </div>
        
        <?php if ($max_tags > 0) : ?>
            <p class="tags-info">
                সর্বোচ্চ <?php echo $max_tags; ?>টি ট্যাগ। 
                বর্তমান: <span class="tag-count"><?php echo count($tags); ?></span>
            </p>
        <?php endif; ?>
    </div>
    
    <style>
    .bizzplugin-tags-field {
        max-width: 500px;
    }
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #fff;
        min-height: 40px;
    }
    .tag-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 8px;
        background: #2271b1;
        color: #fff;
        border-radius: 3px;
        font-size: 13px;
    }
    .tag-remove {
        background: none;
        border: none;
        color: #fff;
        cursor: pointer;
        padding: 0;
        font-size: 16px;
        line-height: 1;
        opacity: 0.7;
    }
    .tag-remove:hover {
        opacity: 1;
    }
    .tag-input {
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        flex: 1;
        min-width: 100px;
        padding: 4px !important;
    }
    .tags-info {
        margin-top: 5px;
        font-size: 12px;
        color: #666;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // এন্টার প্রেস করলে ট্যাগ যোগ
        $('.bizzplugin-tags-field .tag-input').on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTag($(this));
            }
        });
        
        // ব্লার হলে ট্যাগ যোগ
        $('.bizzplugin-tags-field .tag-input').on('blur', function() {
            if ($(this).val().trim()) {
                addTag($(this));
            }
        });
        
        // ট্যাগ রিমুভ
        $(document).on('click', '.bizzplugin-tags-field .tag-remove', function() {
            var $field = $(this).closest('.bizzplugin-tags-field');
            $(this).closest('.tag-item').remove();
            updateTagCount($field);
        });
        
        function addTag($input) {
            var $field = $input.closest('.bizzplugin-tags-field');
            var maxTags = parseInt($field.data('max-tags')) || 0;
            var currentTags = $field.find('.tag-item').length;
            var tag = $input.val().trim().replace(',', '');
            var fieldName = $field.find('input[type="hidden"]').first().attr('name') || 
                           $input.closest('.bizzplugin-field').find('input[type="hidden"]').first().attr('name');
            
            if (!fieldName) {
                fieldName = $field.closest('.bizzplugin-field').data('field-id') || 'tags';
                fieldName += '[]';
            }
            
            if (!tag) return;
            
            // ডুপ্লিকেট চেক
            var exists = false;
            $field.find('.tag-item').each(function() {
                if ($(this).text().trim().replace('×', '') === tag) {
                    exists = true;
                    return false;
                }
            });
            
            if (exists) {
                $input.val('');
                return;
            }
            
            // ম্যাক্স চেক
            if (maxTags > 0 && currentTags >= maxTags) {
                alert('সর্বোচ্চ ' + maxTags + 'টি ট্যাগ যোগ করা যায়।');
                return;
            }
            
            // ট্যাগ যোগ
            var tagHtml = '<span class="tag-item">' + 
                         tag + 
                         '<input type="hidden" name="' + fieldName + '" value="' + tag + '">' +
                         '<button type="button" class="tag-remove">&times;</button>' +
                         '</span>';
            
            $input.before(tagHtml);
            $input.val('');
            updateTagCount($field);
        }
        
        function updateTagCount($field) {
            var count = $field.find('.tag-item').length;
            $field.find('.tag-count').text(count);
            
            // ফর্ম চেঞ্জ ট্রিগার
            $field.find('input[type="hidden"]').first().trigger('change');
        }
    });
    </script>
    <?php
}, 10, 3);

// স্যানিটাইজেশন
add_filter('bizzplugin_sanitize_field_tags', function($value, $field) {
    if (!is_array($value)) {
        return array();
    }
    
    $sanitized = array();
    foreach ($value as $tag) {
        $clean = sanitize_text_field(trim($tag));
        if (!empty($clean) && !in_array($clean, $sanitized)) {
            $sanitized[] = $clean;
        }
    }
    
    // ম্যাক্স ট্যাগ চেক
    $max_tags = isset($field['max_tags']) ? intval($field['max_tags']) : 0;
    if ($max_tags > 0 && count($sanitized) > $max_tags) {
        $sanitized = array_slice($sanitized, 0, $max_tags);
    }
    
    return $sanitized;
}, 10, 2);
```

---

## সেরা অভ্যাস

### ১. নামকরণ কনভেনশন

```php
// ফিল্ড টাইপ: snake_case
'type' => 'my_custom_field'

// ক্লাস নাম: Pascal_Case
class My_Custom_Field {}

// ফাংশন: prefix_সহ
function my_plugin_render_custom_field() {}
```

### ২. সবসময় Escaping করুন

```php
// আউটপুটে
echo esc_attr($value);
echo esc_html($label);
echo esc_url($url);

// HTML অ্যাট্রিবিউটে
<input name="<?php echo esc_attr($field['id']); ?>">
```

### ৩. ডিসেবল স্টেট হ্যান্ডল করুন

```php
function render_my_field($field, $value, $is_disabled) {
    $disabled = $is_disabled ? ' disabled="disabled"' : '';
    ?>
    <input type="text" <?php echo $disabled; ?> />
    <?php
}
```

### ৪. ডিফল্ট ভ্যালু সেট করুন

```php
$max = isset($field['max']) ? intval($field['max']) : 100;
$placeholder = $field['placeholder'] ?? '';
```

### ৫. অ্যাক্সেসিবিলিটি

```php
<label for="<?php echo esc_attr($field['id']); ?>">
    <?php echo esc_html($field['title']); ?>
</label>
<input 
    id="<?php echo esc_attr($field['id']); ?>"
    aria-describedby="<?php echo esc_attr($field['id']); ?>-desc"
/>
<p id="<?php echo esc_attr($field['id']); ?>-desc">
    <?php echo esc_html($field['description']); ?>
</p>
```

---

## পরবর্তী পড়ুন

- [সকল ফিল্ড টাইপ](all-field-types.md)
- [কলব্যাক টাইপ](callback-type.md)
- [স্যানিটাইজ এবং ভ্যালিডেশন](sanitize-validation-callbacks.md)
