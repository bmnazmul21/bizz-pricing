# CSS Splitting Implementation Plan (সিএসএস বিভাজন বাস্তবায়ন পরিকল্পনা)

## 🎯 লক্ষ্য (Objective)

বর্তমানে `framework.css` ফাইলে সমস্ত CSS একসাথে আছে। আমাদের লক্ষ্য হলো CSS কে ফিল্ড-টাইপ অনুযায়ী আলাদা আলাদা ফাইলে বিভক্ত করা, যাতে শুধুমাত্র প্রয়োজনীয় CSS লোড হয়।

---

## 📁 প্রস্তাবিত CSS ফাইল স্ট্রাকচার

```
options-framework/
└── assets/
    └── css/
        ├── framework.css          # মূল/কমন CSS (Core styles)
        ├── fields/                 # ফিল্ড-ভিত্তিক CSS
        │   ├── text.css           # text, email, url, number, password
        │   ├── textarea.css       
        │   ├── select.css         # select, multi_select
        │   ├── checkbox.css       # checkbox, checkbox_group
        │   ├── radio.css          
        │   ├── switch.css         # on_off, switch
        │   ├── color.css          
        │   ├── date.css           # date picker
        │   ├── image.css          # image upload
        │   ├── file.css           # file upload
        │   ├── image-select.css   
        │   ├── option-select.css  
        │   ├── post-select.css    
        │   ├── slider.css         # slider, range
        │   ├── repeater.css       
        │   ├── plugins.css        
        │   └── html.css           # html, info, notice
        └── components/            # কম্পোনেন্ট-ভিত্তিক CSS (Optional)
            ├── navigation.css
            ├── sidebar.css
            ├── api-section.css
            └── export-import.css
```

---

## 🔍 বর্তমান CSS বিশ্লেষণ (Analysis)

### `framework.css` এ যা আছে:

| সেকশন | লাইন রেঞ্জ | বিবরণ |
|--------|-----------|--------|
| CSS Variables | 11-34 | কমন variables - মেইনে থাকবে |
| Framework Container | 40-54 | কমন - মেইনে থাকবে |
| Navigation Sidebar | 60-278 | কমন - মেইনে থাকবে |
| Content Area | 284-320 | কমন - মেইনে থাকবে |
| Sections | 327-389 | কমন - মেইনে থাকবে |
| Fields (General) | 395-461 | কমন - মেইনে থাকবে |
| Input Fields | 467-531 | text/textarea/select জন্য আলাদা |
| Checkbox & Radio | 537-565 | আলাদা ফাইল |
| Toggle Switch | 571-639 | আলাদা ফাইল |
| Color Picker | 645-647 | আলাদা ফাইল |
| Date Picker | 653-655 | আলাদা ফাইল |
| Image Upload | 661-704 | আলাদা ফাইল |
| File Upload | 710-728 | আলাদা ফাইল |
| Image Select | 734-769 | আলাদা ফাইল |
| Option Select | 775-817 | আলাদা ফাইল |
| Right Sidebar | 823-882 | কমন/সাইডবারে |
| API Section | 888-1193 | api-section.css |
| Footer | 1199-1260 | কমন - মেইনে থাকবে |
| Notifications | 1279-1354 | কমন - মেইনে থাকবে |
| Responsive | 1360-1417 | প্রতিটি ফাইলে নিজস্ব |
| jQuery UI Datepicker | 1444-1566 | date.css |
| Plugins Field | 1572-1737 | plugins.css |
| Slider/Range | 1743-1855 | slider.css |
| Sidebar Plugins | 1861-1982 | sidebar.css বা plugins.css |
| Search | 1988-2103 | কমন - মেইনে থাকবে |
| Repeater | 2109-2309 | repeater.css |

---

## 📝 বাস্তবায়ন ধাপ (Implementation Steps)

### ধাপ ১: ফিল্ড টাইপ ট্র্যাকিং সিস্টেম

`class-bizzplugin-panel.php` এ একটি প্রোপার্টি এবং মেথড যোগ করতে হবে:

```php
/**
 * Used field types in this panel
 */
private $used_field_types = array();

/**
 * Track field type usage
 */
private function track_field_type($type) {
    if (!in_array($type, $this->used_field_types)) {
        $this->used_field_types[] = $type;
    }
}

/**
 * Get all used field types
 */
public function get_used_field_types() {
    return $this->used_field_types;
}
```

### ধাপ ২: ফিল্ড সংগ্রহের সময় টাইপ ট্র্যাক করা

`add_field()` বা `add_section()` মেথডে:

```php
public function add_field($section_id, $field) {
    // ... existing code ...
    
    // Track field type
    $field_type = isset($field['type']) ? $field['type'] : 'text';
    $this->track_field_type($field_type);
    
    // For repeater fields, track sub-field types
    if ($field_type === 'repeater' && !empty($field['fields'])) {
        foreach ($field['fields'] as $sub_field) {
            $sub_type = isset($sub_field['type']) ? $sub_field['type'] : 'text';
            $this->track_field_type($sub_type);
        }
    }
    
    // ... rest of the code ...
}
```

### ধাপ ৩: CSS ফাইল এনকিউ করার লজিক

`class-bizzplugin-framework.php` এর `enqueue_assets()` মেথডে মডিফিকেশন:

```php
/**
 * Enqueue assets
 */
public function enqueue_assets($hook) {
    // Check if we're on a registered options page
    $current_panel = null;
    foreach ($this->panels as $panel) {
        if ($panel->is_current_page($hook)) {
            $current_panel = $panel;
            break;
        }
    }
    
    if (!$current_panel) {
        return;
    }
    
    // ... existing media, color picker code ...
    
    // Enqueue core framework CSS
    wp_enqueue_style(
        'bizzplugin-framework-style',
        $this->framework_url . 'assets/css/framework.css',
        array(),
        self::VERSION
    );
    
    // Get used field types and enqueue field-specific CSS
    $used_types = $current_panel->get_used_field_types();
    $this->enqueue_field_css($used_types);
    
    // ... rest of the code ...
}

/**
 * Enqueue field-specific CSS files
 */
private function enqueue_field_css($field_types) {
    // Map field types to CSS files
    $css_map = array(
        'text'           => 'text.css',
        'email'          => 'text.css',
        'url'            => 'text.css',
        'number'         => 'text.css',
        'password'       => 'text.css',
        'textarea'       => 'textarea.css',
        'select'         => 'select.css',
        'multi_select'   => 'select.css',
        'checkbox'       => 'checkbox.css',
        'checkbox_group' => 'checkbox.css',
        'radio'          => 'radio.css',
        'on_off'         => 'switch.css',
        'switch'         => 'switch.css',
        'color'          => 'color.css',
        'date'           => 'date.css',
        'image'          => 'image.css',
        'file'           => 'file.css',
        'image_select'   => 'image-select.css',
        'option_select'  => 'option-select.css',
        'post_select'    => 'post-select.css',
        'slider'         => 'slider.css',
        'range'          => 'slider.css',
        'repeater'       => 'repeater.css',
        'plugins'        => 'plugins.css',
        'html'           => 'html.css',
        'info'           => 'html.css',
        'notice'         => 'html.css',
    );
    
    $loaded_files = array();
    
    foreach ($field_types as $type) {
        if (isset($css_map[$type])) {
            $css_file = $css_map[$type];
            
            // Skip if already loaded
            if (in_array($css_file, $loaded_files)) {
                continue;
            }
            
            $css_path = $this->framework_path . '/assets/css/fields/' . $css_file;
            
            // Only enqueue if file exists
            if (file_exists($css_path)) {
                wp_enqueue_style(
                    'bizzplugin-field-' . str_replace('.css', '', $css_file),
                    $this->framework_url . 'assets/css/fields/' . $css_file,
                    array('bizzplugin-framework-style'),
                    self::VERSION
                );
                
                $loaded_files[] = $css_file;
            }
        }
    }
}
```

---

## 🔄 Repeater ফিল্ডের বিশেষ হ্যান্ডলিং

Repeater ফিল্ডের ভিতরে অন্যান্য ফিল্ড থাকে, তাই:

1. **Repeater ফিল্ড ডিফাইন করার সময়**:
```php
array(
    'id'     => 'my_repeater',
    'type'   => 'repeater',
    'title'  => 'My Items',
    'fields' => array(
        array('id' => 'title', 'type' => 'text'),
        array('id' => 'date', 'type' => 'date'),
        array('id' => 'image', 'type' => 'image'),
    )
)
```

2. **ট্র্যাকিং লজিক**:
   - `repeater.css` লোড হবে
   - `text.css` লোড হবে
   - `date.css` লোড হবে
   - `image.css` লোড হবে

---

## 📋 মেইন CSS এ যা থাকবে (`framework.css`)

```css
/* কমন স্টাইল যা থাকবে: */

/* 1. CSS Variables */
:root {
    --bizzplugin-primary: #2271b1;
    /* ... সব variables ... */
}

/* 2. Framework Container */
.bizzplugin-framework-wrap { }
.bizzplugin-framework-container { }

/* 3. Navigation (Left Sidebar) */
.bizzplugin-nav { }
.bizzplugin-nav-menu { }
/* ... navigation related ... */

/* 4. Content Area */
.bizzplugin-content { }
.bizzplugin-section { }
.bizzplugin-section-header { }

/* 5. Field Base Styles */
.bizzplugin-field { }
.bizzplugin-field-header { }
.bizzplugin-field-title { }
.bizzplugin-field-desc { }
.bizzplugin-field-content { }

/* 6. Footer & Buttons */
.bizzplugin-footer { }
#bizzplugin-save-options { }

/* 7. Notifications */
.bizzplugin-notification { }
.bizzplugin-notice { }

/* 8. Search */
.bizzplugin-search-wrap { }

/* 9. Responsive (base) */
@media screen and (max-width: 1200px) { }
@media screen and (max-width: 782px) { }

/* 10. Animations */
@keyframes bizzFadeIn { }
@keyframes bizzSpin { }
```

---

## 📁 ফিল্ড CSS ফাইলের উদাহরণ

### `fields/date.css`

```css
/**
 * BizzPlugin Framework - Date Picker Field CSS
 */

/* Date Input */
.bizzplugin-date-picker {
    max-width: 200px;
}

/* jQuery UI Datepicker Styles */
.ui-datepicker {
    background: var(--bizzplugin-bg-white);
    border: 1px solid var(--bizzplugin-border-dark);
    /* ... সব datepicker styles ... */
}

/* ... বাকি datepicker CSS ... */
```

### `fields/repeater.css`

```css
/**
 * BizzPlugin Framework - Repeater Field CSS
 */

.bizzplugin-repeater-wrap {
    width: 100%;
}

.bizzplugin-repeater-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 15px;
}

/* ... বাকি repeater CSS ... */

/* Responsive */
@media screen and (max-width: 782px) {
    .bizzplugin-repeater-item-header {
        padding: 10px 12px;
    }
}
```

---

## 🛠️ বাস্তবায়নের জন্য মডিফাই করার ফাইলসমূহ

| ফাইল | কী পরিবর্তন করতে হবে |
|------|---------------------|
| `class-bizzplugin-panel.php` | ফিল্ড টাইপ ট্র্যাকিং প্রোপার্টি ও মেথড যোগ |
| `class-bizzplugin-framework.php` | `enqueue_assets()` মেথডে কন্ডিশনাল CSS লোডিং |
| `assets/css/framework.css` | শুধু কমন CSS রাখা, বাকি সরিয়ে নেওয়া |
| `assets/css/fields/*.css` | নতুন ফিল্ড-ভিত্তিক CSS ফাইল তৈরি |

---

## ✅ সুবিধাসমূহ

1. **পারফরম্যান্স উন্নতি**: শুধু প্রয়োজনীয় CSS লোড হবে
2. **মেইনটেনেবিলিটি**: ফিল্ড-ভিত্তিক CSS সহজে এডিট করা যাবে
3. **ক্যাশিং**: ছোট ফাইল ভালো ক্যাশ হয়
4. **মডুলারিটি**: নতুন ফিল্ড টাইপ যোগ করা সহজ

---

## ⚠️ সতর্কতা

1. CSS Variables সব ফাইলে অ্যাক্সেসিবল রাখতে `framework.css` প্রথমে লোড হতে হবে
2. ফিল্ড CSS এ `array('bizzplugin-framework-style')` dependency রাখতে হবে
3. ফাইল এক্সিস্ট চেক করে তারপর enqueue করতে হবে

---

## 🔗 সম্পর্কিত ফাইল

- `class-bizzplugin-framework.php` - Asset loading
- `class-bizzplugin-panel.php` - Field rendering
- `assets/css/framework.css` - Current CSS

---

## 📅 বাস্তবায়ন টাইমলাইন সাজেশন

1. **Phase 1**: CSS ফাইল বিভক্ত করা (সব CSS আলাদা ফাইলে)
2. **Phase 2**: ফিল্ড টাইপ ট্র্যাকিং সিস্টেম
3. **Phase 3**: কন্ডিশনাল CSS লোডিং
4. **Phase 4**: টেস্টিং ও অপ্টিমাইজেশন

---

*শেষ আপডেট: December 2024*
