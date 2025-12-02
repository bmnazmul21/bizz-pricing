# এক্সপোর্ট/ইমপোর্ট সেটিংস

BizzPlugin Options Framework সেটিংস এক্সপোর্ট এবং ইমপোর্ট করার জন্য বিল্ট-ইন ফাংশনালিটি প্রদান করে, যা ব্যাকআপ, রিস্টোর বা কনফিগারেশন ট্রান্সফার সহজ করে।

## এক্সপোর্ট/ইমপোর্ট ব্যবহার করা

### এক্সপোর্ট/ইমপোর্ট অ্যাক্সেস করা

1. আপনার প্লাগইনের সেটিংস প্যানেলে যান
2. নেভিগেশন মেনুতে "এক্সপোর্ট/ইমপোর্ট" এ ক্লিক করুন

### সেটিংস এক্সপোর্ট করা

1. "সেটিংস এক্সপোর্ট করুন" বাটনে ক্লিক করুন
2. একটি JSON ফাইল আপনার কম্পিউটারে ডাউনলোড হবে
3. ফাইলের নাম এই প্যাটার্নে হয়: `{option_name}_export_{date}.json`

### সেটিংস ইমপোর্ট করা

1. "সেটিংস ইমপোর্ট করুন" বাটনে ক্লিক করুন
2. আগে এক্সপোর্ট করা একটি JSON ফাইল নির্বাচন করুন
3. সেটিংস ফর্মে লোড হবে
4. ইমপোর্ট করা সেটিংস প্রয়োগ করতে "সংরক্ষণ করুন" ক্লিক করুন

**গুরুত্বপূর্ণ**: ইমপোর্ট করা সেটিংস স্বয়ংক্রিয়ভাবে সংরক্ষিত হয় না। প্রয়োগ করতে আপনাকে সংরক্ষণ বাটনে ক্লিক করতে হবে।

## এক্সপোর্ট ফাইল ফরম্যাট

এক্সপোর্ট করা JSON ফাইলে থাকে:

```json
{
  "panel_id": "my_plugin_settings",
  "option_name": "my_plugin_options",
  "exported_at": "2024-01-15T12:30:45.000Z",
  "site_url": "https://your-site.com",
  "version": "1.0.0",
  "data": {
    "site_name": "আমার সাইট",
    "enable_feature": "1",
    "primary_color": "#2271b1",
    "posts_per_page": 10
  }
}
```

### এক্সপোর্ট ফিল্ড

| ফিল্ড | বিবরণ |
|-------|-------|
| `panel_id` | যে প্যানেল ID এক্সপোর্ট তৈরি করেছে |
| `option_name` | WordPress অপশন নাম |
| `exported_at` | এক্সপোর্টের ISO 8601 টাইমস্ট্যাম্প |
| `site_url` | সোর্স সাইটের URL |
| `version` | এক্সপোর্টের সময় প্লাগইন ভার্সন |
| `data` | সব সংরক্ষিত অপশন মান |

## প্রোগ্রাম্যাটিক এক্সপোর্ট/ইমপোর্ট

### PHP দিয়ে এক্সপোর্ট

```php
// বর্তমান অপশন পান
$options = get_option('my_plugin_options', array());

// এক্সপোর্ট ডেটা তৈরি করুন
$export_data = array(
    'panel_id'    => 'my_plugin_settings',
    'option_name' => 'my_plugin_options',
    'exported_at' => gmdate('c'),
    'site_url'    => get_site_url(),
    'version'     => MY_PLUGIN_VERSION,
    'data'        => $options,
);

// JSON-এ কনভার্ট করুন
$json = wp_json_encode($export_data, JSON_PRETTY_PRINT);

// ফাইলে সংরক্ষণ বা আউটপুট করুন
file_put_contents('/path/to/export.json', $json);
```

### PHP দিয়ে ইমপোর্ট

```php
// ইমপোর্ট ফাইল পড়ুন
$json = file_get_contents('/path/to/import.json');
$import_data = json_decode($json, true);

// ইমপোর্ট ডেটা ভ্যালিডেট করুন
if (!isset($import_data['data']) || !is_array($import_data['data'])) {
    wp_die('অবৈধ ইমপোর্ট ফাইল');
}

// ঐচ্ছিক: panel_id মিলছে কি না ভ্যালিডেট করুন
if ($import_data['panel_id'] !== 'my_plugin_settings') {
    // মিলছে না - সতর্কতা দেখান
}

// অপশন ইমপোর্ট করুন
update_option('my_plugin_options', $import_data['data']);
```

### REST API দিয়ে এক্সপোর্ট

```bash
curl -X GET "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options" \
  -H "x-api-key: YOUR_API_KEY" \
  -o export.json
```

### REST API দিয়ে ইমপোর্ট

```bash
curl -X POST "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options" \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d @import.json
```

## ব্যবহারের ক্ষেত্র

### ১. আপডেটের আগে ব্যাকআপ

```php
// প্লাগইন আপডেটের আগে অ্যাকশন যোগ করুন
add_action('upgrader_pre_install', function($upgrader, $options) {
    if (isset($options['plugin']) && $options['plugin'] === 'my-plugin/my-plugin.php') {
        // ব্যাকআপ তৈরি করুন
        $options = get_option('my_plugin_options', array());
        $backup = array(
            'timestamp' => time(),
            'version' => MY_PLUGIN_VERSION,
            'data' => $options,
        );
        update_option('my_plugin_options_backup', $backup);
    }
}, 10, 2);
```

### ২. সাইট জুড়ে সেটিংস সিঙ্ক

```php
// সোর্স সাইট থেকে এক্সপোর্ট
function export_settings_to_remote($remote_api_key, $remote_url) {
    $options = get_option('my_plugin_options', array());
    
    $response = wp_remote_post($remote_url . '/wp-json/bizzplugin/v1/options/my_plugin_options', array(
        'headers' => array(
            'x-api-key' => $remote_api_key,
            'Content-Type' => 'application/json',
        ),
        'body' => wp_json_encode($options),
    ));
    
    return !is_wp_error($response);
}
```

### ৩. ডিফল্ট সেটিংস টেমপ্লেট

```php
// টেমপ্লেট ফাইল থেকে ডিফল্ট সেটিংস লোড করুন
function load_default_settings() {
    $template_path = MY_PLUGIN_PATH . 'defaults/settings-template.json';
    
    if (file_exists($template_path)) {
        $json = file_get_contents($template_path);
        $template = json_decode($json, true);
        
        if (isset($template['data'])) {
            update_option('my_plugin_options', $template['data']);
            return true;
        }
    }
    
    return false;
}
```

### ৪. মাল্টিসাইট সেটিংস বিতরণ

```php
// মূল সাইট থেকে সব সাবসাইটে সেটিংস কপি করুন
function distribute_settings_to_network() {
    if (!is_multisite()) {
        return;
    }
    
    $main_options = get_option('my_plugin_options', array());
    $sites = get_sites();
    
    foreach ($sites as $site) {
        switch_to_blog($site->blog_id);
        update_option('my_plugin_options', $main_options);
        restore_current_blog();
    }
}
```

## নিরাপত্তা বিবেচনা

1. **সংবেদনশীল ডেটা**: এক্সপোর্ট ফাইলে সংবেদনশীল ডেটা (API কী, পাসওয়ার্ড) থাকতে পারে। নিরাপদে হ্যান্ডেল করুন।

2. **ভ্যালিডেশন**: সংরক্ষণের আগে সর্বদা ইমপোর্ট করা ডেটা ভ্যালিডেট করুন:
   ```php
   // ডেটা স্ট্রাকচার ভ্যালিডেট করুন
   if (!is_array($import_data['data'])) {
       return new WP_Error('invalid_data', 'অবৈধ ইমপোর্ট ডেটা');
   }
   
   // মান স্যানিটাইজ করুন
   $framework = bizzplugin_framework();
   $panel = $framework->get_panel('my_plugin_settings');
   $sanitized = $ajax_handler->sanitize_options($import_data['data'], $panel);
   ```

3. **প্যানেল ID যাচাই**: এক্সপোর্ট একই প্যানেল থেকে এসেছে কি না চেক করুন:
   ```php
   if ($import_data['panel_id'] !== $current_panel_id) {
       // সতর্কতা দেখান বা ইমপোর্ট প্রত্যাখ্যান করুন
   }
   ```

4. **ফাইল আপলোড নিরাপত্তা**: ইমপোর্টের জন্য ফাইল আপলোড হ্যান্ডেল করার সময়:
   - ফাইল টাইপ যাচাই করুন (শুধু JSON)
   - ফাইল সাইজ সীমা চেক করুন
   - JSON স্ট্রাকচার ভ্যালিডেট করুন

## সমস্যা সমাধান

### ইমপোর্ট কাজ করছে না?

1. **JSON বৈধতা চেক করুন**: ফাইলটি বৈধ JSON কি না নিশ্চিত করুন
2. **ফাইল এনকোডিং চেক করুন**: ফাইল UTF-8 হওয়া উচিত
3. **panel_id যাচাই করুন**: আপনার প্যানেলের সাথে মিলছে কি না নিশ্চিত করুন
4. **ব্রাউজার কনসোল চেক করুন**: JavaScript এরর দেখুন
5. **সংরক্ষণ করতে ভুলবেন না**: ইমপোর্ট করা সেটিংসের জন্য সংরক্ষণ ক্লিক করতে হবে

### এক্সপোর্ট খালি?

1. **অপশন আছে কি না চেক করুন**: `get_option('option_name')` ডেটা রিটার্ন করছে কি না যাচাই করুন
2. **পারমিশন চেক করুন**: ব্যবহারকারীর `manage_options` ক্যাপাবিলিটি প্রয়োজন
3. **JavaScript এরর চেক করুন**: ব্রাউজার কনসোলে দেখুন

---

## পরবর্তী পদক্ষেপ

- [REST API](api.md) - API এন্ডপয়েন্ট
- [ওয়েবহুক](webhooks.md) - ওয়েবহুক নোটিফিকেশন
- [উদাহরণ](examples.md) - কোড উদাহরণ
