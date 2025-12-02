# কোড উদাহরণ - CSS Splitting Implementation

## 📁 ফাইল স্ট্রাকচার তৈরি করুন

```bash
# Terminal commands
mkdir -p options-framework/assets/css/fields
mkdir -p options-framework/assets/css/components
```

---

## 1️⃣ `class-bizzplugin-panel.php` এ যোগ করুন

### Property যোগ করুন (ক্লাসের শুরুতে):

```php
/**
 * Used field types in this panel
 * @var array
 */
private $used_field_types = array();
```

### Methods যোগ করুন:

```php
/**
 * Track field type usage
 * 
 * @param string $type Field type
 * @param array $field Optional field data for repeater sub-fields
 */
private function track_field_type($type, $field = array()) {
    if (!in_array($type, $this->used_field_types)) {
        $this->used_field_types[] = $type;
    }
    
    // For repeater, also track sub-field types
    if ($type === 'repeater' && !empty($field['fields'])) {
        foreach ($field['fields'] as $sub_field) {
            $sub_type = isset($sub_field['type']) ? $sub_field['type'] : 'text';
            if (!in_array($sub_type, $this->used_field_types)) {
                $this->used_field_types[] = $sub_type;
            }
        }
    }
}

/**
 * Get all used field types
 * 
 * @return array List of field types used in this panel
 */
public function get_used_field_types() {
    return $this->used_field_types;
}

/**
 * Collect all field types from sections
 * Call this after all sections are added
 */
public function collect_field_types() {
    foreach ($this->sections as $section) {
        // Section fields
        if (!empty($section['fields'])) {
            foreach ($section['fields'] as $field) {
                $type = isset($field['type']) ? $field['type'] : 'text';
                $this->track_field_type($type, $field);
            }
        }
        
        // Subsection fields
        if (!empty($section['subsections'])) {
            foreach ($section['subsections'] as $subsection) {
                if (!empty($subsection['fields'])) {
                    foreach ($subsection['fields'] as $field) {
                        $type = isset($field['type']) ? $field['type'] : 'text';
                        $this->track_field_type($type, $field);
                    }
                }
            }
        }
    }
    
    return $this->used_field_types;
}
```

### `add_field()` মেথডে যোগ করুন (optional - real-time tracking):

```php
public function add_field($section_id, $field) {
    // ... existing validation code ...
    
    // Track field type for CSS loading
    $field_type = isset($field['type']) ? $field['type'] : 'text';
    $this->track_field_type($field_type, $field);
    
    // ... rest of existing code ...
}
```

---

## 2️⃣ `class-bizzplugin-framework.php` মডিফাই করুন

### নতুন Property যোগ করুন:

```php
/**
 * Field type to CSS file mapping
 * @var array
 */
private $field_css_map = array(
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
    'heading'        => 'html.css',
    'divider'        => 'html.css',
    'link'           => 'html.css',
);
```

### `enqueue_assets()` মেথড আপডেট করুন:

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
    
    // Enqueue WordPress media uploader
    wp_enqueue_media();
    
    // Enqueue color picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    
    // Enqueue jQuery UI for datepicker
    wp_enqueue_script('jquery-ui-datepicker');
    
    // Framework Core CSS (always load)
    wp_enqueue_style(
        'bizzplugin-framework-style',
        $this->framework_url . 'assets/css/framework.css',
        array(),
        self::VERSION
    );
    
    // Collect and enqueue field-specific CSS
    $used_types = $current_panel->collect_field_types();
    $this->enqueue_field_css($used_types);
    
    // Framework JS
    wp_enqueue_script(
        'bizzplugin-framework-script',
        $this->framework_url . 'assets/js/framework.js',
        array('jquery', 'wp-color-picker', 'jquery-ui-datepicker'),
        self::VERSION,
        true
    );
    
    // ... rest of localize_script code ...
}

/**
 * Enqueue field-specific CSS files
 * 
 * @param array $field_types Array of field types used
 */
private function enqueue_field_css($field_types) {
    $loaded_files = array();
    
    foreach ($field_types as $type) {
        // Check if this field type has a CSS mapping
        if (!isset($this->field_css_map[$type])) {
            continue;
        }
        
        $css_file = $this->field_css_map[$type];
        
        // Skip if already loaded (multiple types may share same CSS)
        if (in_array($css_file, $loaded_files)) {
            continue;
        }
        
        $css_path = $this->framework_path . '/assets/css/fields/' . $css_file;
        
        // Only enqueue if file exists
        if (file_exists($css_path)) {
            $handle = 'bizzplugin-field-' . str_replace('.css', '', $css_file);
            
            wp_enqueue_style(
                $handle,
                $this->framework_url . 'assets/css/fields/' . $css_file,
                array('bizzplugin-framework-style'), // Dependency
                self::VERSION
            );
            
            $loaded_files[] = $css_file;
        }
    }
    
    // Allow other plugins to enqueue additional field CSS
    do_action('bizzplugin_enqueue_field_css', $field_types, $loaded_files);
}
```

---

## 3️⃣ CSS ফাইল তৈরির উদাহরণ

### `assets/css/fields/date.css`

```css
/**
 * BizzPlugin Framework - Date Field CSS
 * 
 * @package BizzPlugin_Options_Framework
 * @since 1.0.0
 */

/* =============================================
   Date Picker Input
   ============================================= */

.bizzplugin-date-picker {
    max-width: 200px;
}

/* =============================================
   jQuery UI Datepicker
   ============================================= */

.ui-datepicker {
    background: var(--bizzplugin-bg-white);
    border: 1px solid var(--bizzplugin-border-dark);
    border-radius: var(--bizzplugin-radius);
    box-shadow: var(--bizzplugin-shadow-lg);
    padding: 10px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    font-size: 14px;
    z-index: 100000 !important;
    display: none;
}

.ui-datepicker-header {
    background: var(--bizzplugin-bg);
    border: none;
    border-radius: var(--bizzplugin-radius-sm);
    padding: 10px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ui-datepicker-title {
    font-weight: 600;
    color: #1d2327;
}

.ui-datepicker-prev,
.ui-datepicker-next {
    cursor: pointer;
    top: 12px;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    background-color: transparent;
}

.ui-datepicker-prev:hover,
.ui-datepicker-next:hover {
    background-color: #e0e0e0;
}

.ui-datepicker-prev span,
.ui-datepicker-next span {
    display: none;
}

.ui-datepicker-prev::after {
    content: "‹";
    font-size: 20px;
    line-height: 24px;
    display: block;
    text-align: center;
    color: #1d2327;
}

.ui-datepicker-next::after {
    content: "›";
    font-size: 20px;
    line-height: 24px;
    display: block;
    text-align: center;
    color: #1d2327;
}

.ui-datepicker table {
    width: 100%;
    margin: 0;
}

.ui-datepicker th {
    padding: 8px 4px;
    color: #646970;
    font-weight: 500;
    font-size: 12px;
    text-transform: uppercase;
}

.ui-datepicker td {
    padding: 2px;
}

.ui-datepicker td a,
.ui-datepicker td span {
    display: block;
    padding: 6px;
    text-align: center;
    text-decoration: none;
    color: #1d2327;
    border-radius: 4px;
}

.ui-datepicker td a:hover {
    background: #f0f6fc;
    color: #2271b1;
}

.ui-datepicker td .ui-state-active {
    background: #2271b1;
    color: #fff;
}

.ui-datepicker td .ui-state-highlight {
    background: #f0f0f1;
    color: #1d2327;
}

.ui-datepicker td.ui-datepicker-today a {
    background: #e2e4e7;
    font-weight: 600;
}

.ui-datepicker select.ui-datepicker-month,
.ui-datepicker select.ui-datepicker-year {
    padding: 4px 8px;
    border: 1px solid #8c8f94;
    border-radius: 4px;
    background: #fff;
    margin: 0 4px;
    min-width: 60px;
    text-align: center;
}
```

### `assets/css/fields/repeater.css`

```css
/**
 * BizzPlugin Framework - Repeater Field CSS
 * 
 * @package BizzPlugin_Options_Framework
 * @since 1.0.0
 */

/* =============================================
   Repeater Container
   ============================================= */

.bizzplugin-repeater-wrap {
    width: 100%;
}

.bizzplugin-repeater-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 15px;
}

/* =============================================
   Repeater Item
   ============================================= */

.bizzplugin-repeater-item {
    background: var(--bizzplugin-bg);
    border: 1px solid var(--bizzplugin-border);
    border-radius: var(--bizzplugin-radius);
    overflow: hidden;
    transition: all 0.2s ease;
}

.bizzplugin-repeater-item:hover {
    border-color: var(--bizzplugin-border-dark);
}

/* =============================================
   Item Header
   ============================================= */

.bizzplugin-repeater-item-header {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background: var(--bizzplugin-bg-white);
    border-bottom: 1px solid var(--bizzplugin-border);
    cursor: default;
    gap: 10px;
}

.bizzplugin-repeater-item.collapsed .bizzplugin-repeater-item-header {
    border-bottom: none;
}

.bizzplugin-repeater-item-handle {
    cursor: move;
    color: var(--bizzplugin-text-secondary);
    font-size: 18px;
    width: 18px;
    height: 18px;
    transition: color 0.2s ease;
}

.bizzplugin-repeater-item-handle:hover {
    color: var(--bizzplugin-primary);
}

.bizzplugin-repeater-item-title {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
    color: var(--bizzplugin-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* =============================================
   Item Actions
   ============================================= */

.bizzplugin-repeater-item-actions {
    display: flex;
    align-items: center;
    gap: 5px;
}

.bizzplugin-repeater-item-toggle,
.bizzplugin-repeater-item-remove {
    background: transparent;
    border: none;
    padding: 5px;
    cursor: pointer;
    color: var(--bizzplugin-text-secondary);
    border-radius: var(--bizzplugin-radius-sm);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bizzplugin-repeater-item-toggle:hover {
    background: var(--bizzplugin-bg);
    color: var(--bizzplugin-primary);
}

.bizzplugin-repeater-item-remove:hover {
    background: #fef2f2;
    color: var(--bizzplugin-error);
}

.bizzplugin-repeater-item-toggle .dashicons,
.bizzplugin-repeater-item-remove .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

/* =============================================
   Item Content
   ============================================= */

.bizzplugin-repeater-item-content {
    padding: 20px 15px;
}

.bizzplugin-repeater-item.collapsed .bizzplugin-repeater-item-content {
    display: none;
}

/* =============================================
   Sub-fields
   ============================================= */

.bizzplugin-repeater-subfield {
    margin-bottom: 15px;
}

.bizzplugin-repeater-subfield:last-child {
    margin-bottom: 0;
}

.bizzplugin-repeater-subfield-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: var(--bizzplugin-text);
    margin-bottom: 6px;
}

.bizzplugin-repeater-subfield-input {
    width: 100%;
}

.bizzplugin-repeater-subfield-input .bizzplugin-input,
.bizzplugin-repeater-subfield-input .bizzplugin-textarea,
.bizzplugin-repeater-subfield-input .bizzplugin-select {
    width: 100%;
}

.bizzplugin-repeater-subfield-desc {
    margin: 6px 0 0 0;
    font-size: 12px;
    color: var(--bizzplugin-text-secondary);
}

/* Image field in repeater */
.bizzplugin-repeater-subfield .bizzplugin-image-upload {
    max-width: none;
}

.bizzplugin-repeater-subfield .bizzplugin-image-preview {
    max-width: 150px;
}

/* Color picker in repeater */
.bizzplugin-repeater-subfield .wp-picker-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

/* =============================================
   Footer & Add Button
   ============================================= */

.bizzplugin-repeater-footer {
    display: flex;
}

.bizzplugin-repeater-footer .bizzplugin-repeater-add {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: nowrap;
    justify-content: flex-start;
}

.bizzplugin-repeater-add .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

/* =============================================
   Sortable Placeholder
   ============================================= */

.bizzplugin-repeater-placeholder {
    background: var(--bizzplugin-primary-light);
    border: 2px dashed var(--bizzplugin-primary);
    border-radius: var(--bizzplugin-radius);
    min-height: 60px;
    margin-bottom: 15px;
}

/* =============================================
   Template (Hidden)
   ============================================= */

.bizzplugin-repeater-template {
    display: none !important;
}

/* =============================================
   Responsive
   ============================================= */

@media screen and (max-width: 782px) {
    .bizzplugin-repeater-item-header {
        padding: 10px 12px;
    }
    
    .bizzplugin-repeater-item-content {
        padding: 15px 12px;
    }
    
    .bizzplugin-repeater-item-title {
        font-size: 13px;
    }
}
```

---

## 4️⃣ Optional: Components CSS

### `assets/css/components/api-section.css`

```css
/**
 * BizzPlugin Framework - API Section CSS
 */

.bizzplugin-api-card {
    background: var(--bizzplugin-bg-white);
    border: 1px solid var(--bizzplugin-border);
    border-radius: var(--bizzplugin-radius);
    margin-bottom: 20px;
    overflow: hidden;
}

/* ... API section styles from framework.css lines 888-1193 ... */
```

---

## 🔗 সম্পর্কিত ডকুমেন্ট

- [CSS Splitting Implementation](./css-splitting-implementation.md)
- [Field Type CSS Mapping](./field-type-css-mapping.md)

---

*শেষ আপডেট: December 2024*
