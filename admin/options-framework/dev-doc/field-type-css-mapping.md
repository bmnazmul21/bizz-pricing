# ফিল্ড টাইপ থেকে CSS ম্যাপিং রেফারেন্স

## 📋 সম্পূর্ণ ফিল্ড টাইপ তালিকা ও CSS ম্যাপিং

| ফিল্ড টাইপ | CSS ফাইল | মন্তব্য |
|-----------|----------|--------|
| `text` | `fields/text.css` | বেসিক ইনপুট |
| `email` | `fields/text.css` | text এর সাথে একই |
| `url` | `fields/text.css` | text এর সাথে একই |
| `number` | `fields/text.css` | text এর সাথে একই |
| `password` | `fields/text.css` | text এর সাথে একই |
| `textarea` | `fields/textarea.css` | |
| `select` | `fields/select.css` | |
| `multi_select` | `fields/select.css` | select এর সাথে একই |
| `checkbox` | `fields/checkbox.css` | |
| `checkbox_group` | `fields/checkbox.css` | checkbox এর সাথে একই |
| `radio` | `fields/radio.css` | |
| `on_off` | `fields/switch.css` | Toggle switch |
| `switch` | `fields/switch.css` | on_off এর alias |
| `color` | `fields/color.css` | Color picker |
| `date` | `fields/date.css` | jQuery UI Datepicker সহ |
| `image` | `fields/image.css` | Media uploader |
| `file` | `fields/file.css` | File upload |
| `image_select` | `fields/image-select.css` | Image based selection |
| `option_select` | `fields/option-select.css` | Text based selection |
| `post_select` | `fields/post-select.css` | Post selector |
| `slider` | `fields/slider.css` | Range slider |
| `range` | `fields/slider.css` | slider এর alias |
| `repeater` | `fields/repeater.css` | Repeater + sub-fields |
| `plugins` | `fields/plugins.css` | Plugin cards |
| `html` | `fields/html.css` | Static HTML content |
| `info` | `fields/html.css` | Information display |
| `notice` | `fields/html.css` | Notice/alert |
| `callback` | - | Custom rendering, CSS প্রয়োজন হলে ম্যানুয়ালি লোড করতে হবে |
| `heading` | `fields/html.css` | Section heading |
| `divider` | `fields/html.css` | Divider line |
| `link` | `fields/html.css` | External link |

---

## 🎨 CSS গ্রুপিং

### Group 1: Text-like Fields (`text.css`)
- text, email, url, number, password
- সব বেসিক `.bizzplugin-input` স্টাইল

### Group 2: Selection Fields
- **`select.css`**: select, multi_select
- **`checkbox.css`**: checkbox, checkbox_group
- **`radio.css`**: radio buttons

### Group 3: Media Fields
- **`image.css`**: image upload/preview
- **`file.css`**: file upload

### Group 4: Picker Fields
- **`color.css`**: WordPress color picker
- **`date.css`**: jQuery UI datepicker

### Group 5: Complex Fields
- **`repeater.css`**: Repeater with nested fields
- **`slider.css`**: Range/slider inputs
- **`plugins.css`**: Plugin recommendation cards

### Group 6: Display-only Fields (`html.css`)
- html, info, notice, heading, divider, link
- Non-saveable fields

---

## 📝 PHP Implementation Map

```php
/**
 * Field type to CSS file mapping
 * Use this in class-bizzplugin-framework.php
 */
private $field_css_map = array(
    // Text-like fields
    'text'           => 'text.css',
    'email'          => 'text.css',
    'url'            => 'text.css',
    'number'         => 'text.css',
    'password'       => 'text.css',
    
    // Textarea
    'textarea'       => 'textarea.css',
    
    // Selection fields
    'select'         => 'select.css',
    'multi_select'   => 'select.css',
    'checkbox'       => 'checkbox.css',
    'checkbox_group' => 'checkbox.css',
    'radio'          => 'radio.css',
    
    // Toggle fields
    'on_off'         => 'switch.css',
    'switch'         => 'switch.css',
    
    // Picker fields
    'color'          => 'color.css',
    'date'           => 'date.css',
    
    // Media fields
    'image'          => 'image.css',
    'file'           => 'file.css',
    'image_select'   => 'image-select.css',
    
    // Other selection
    'option_select'  => 'option-select.css',
    'post_select'    => 'post-select.css',
    
    // Complex fields
    'slider'         => 'slider.css',
    'range'          => 'slider.css',
    'repeater'       => 'repeater.css',
    'plugins'        => 'plugins.css',
    
    // Display-only fields
    'html'           => 'html.css',
    'info'           => 'html.css',
    'notice'         => 'html.css',
    'heading'        => 'html.css',
    'divider'        => 'html.css',
    'link'           => 'html.css',
);
```

---

## 🔗 সম্পর্কিত ডকুমেন্ট

- [CSS Splitting Implementation](./css-splitting-implementation.md)
- [Code Examples](./code-examples.md)

---

*শেষ আপডেট: December 2024*
