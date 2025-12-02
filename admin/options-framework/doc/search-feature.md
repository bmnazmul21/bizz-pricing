# সেটিংস সার্চ ফিচার

এই ডকুমেন্টেশনে BizzPlugin Option Framework এর সেটিংস সার্চ ফিচার সম্পর্কে বিস্তারিত আলোচনা করা হয়েছে।

## পরিচিতি

সেটিংস সার্চ ফিচার ব্যবহারকারীদের দ্রুত তাদের প্রয়োজনীয় সেটিং খুঁজে পেতে সাহায্য করে। এই ফিচারটি ডিফল্টভাবে সক্রিয় থাকে, তবে ডেভেলপাররা চাইলে এটি বন্ধ করতে পারেন।

## ডিফল্ট আচরণ

সার্চ ফিচারটি ডিফল্টভাবে **সক্রিয়** (enabled) থাকে। অর্থাৎ, আপনি যদি কিছু নির্দিষ্ট না করেন, তাহলে সার্চ ফিল্ড স্বয়ংক্রিয়ভাবে দেখা যাবে।

## সার্চ ফিচার বন্ধ করার উপায়

### পদ্ধতি ১: প্যানেল তৈরির সময় (create_panel)

প্যানেল তৈরি করার সময় `enable_search` প্যারামিটার ব্যবহার করে সার্চ বন্ধ করতে পারেন:

```php
$framework = bizzplugin_framework();

$panel = $framework->create_panel(array(
    'id' => 'my_plugin_settings',
    'title' => __('My Plugin Settings', 'my-plugin'),
    'menu_title' => __('My Plugin', 'my-plugin'),
    'menu_slug' => 'my-plugin-settings',
    'capability' => 'manage_options',
    'icon' => 'dashicons-admin-settings',
    'position' => 50,
    'option_name' => 'my_plugin_options',
    'enable_search' => false,  // সার্চ বন্ধ করুন
));
```

### পদ্ধতি ২: চেইনেবল মেথড ব্যবহার করে

প্যানেল তৈরির পরেও চেইনেবল মেথড ব্যবহার করে সার্চ বন্ধ বা চালু করতে পারেন:

```php
$framework = bizzplugin_framework();

$panel = $framework->create_panel(array(
    'id' => 'my_plugin_settings',
    'title' => __('My Plugin Settings', 'my-plugin'),
    'option_name' => 'my_plugin_options',
));

// সার্চ বন্ধ করুন
$panel->disable_search();

// অথবা সার্চ চালু করুন
$panel->enable_search();

// অথবা true/false দিয়ে নিয়ন্ত্রণ করুন
$panel->enable_search(false);  // বন্ধ
$panel->enable_search(true);   // চালু
```

## সার্চ স্ট্যাটাস চেক করা

আপনার কোডে সার্চ সক্রিয় আছে কি না তা চেক করতে পারেন:

```php
if ($panel->is_search_enabled()) {
    // সার্চ সক্রিয় আছে
} else {
    // সার্চ বন্ধ আছে
}
```

## সার্চ কিভাবে কাজ করে

সার্চ ফিচারটি নিম্নলিখিত জায়গায় সার্চ করে:

1. **সেকশন টাইটেল** - প্রতিটি সেকশনের নাম
2. **সেকশন বিবরণ** - সেকশনের ডেসক্রিপশন
3. **সেকশন আইডি** - সেকশনের ইউনিক আইডি
4. **সাব-সেকশন** - সকল সাব-সেকশনের টাইটেল ও বিবরণ
5. **ফিল্ড টাইটেল** - প্রতিটি ফিল্ডের লেবেল
6. **ফিল্ড বিবরণ** - ফিল্ডের হেল্প টেক্সট
7. **ফিল্ড আইডি** - ফিল্ডের ইউনিক আইডি

## ব্যবহারকারীর জন্য

ব্যবহারকারীরা সার্চ বক্সে টাইপ করার সাথে সাথে:

1. ম্যাচিং সেকশনগুলো হাইলাইট হয়ে যাবে
2. নন-ম্যাচিং সেকশনগুলো লুকিয়ে যাবে
3. সার্চ রেজাল্টের সংখ্যা দেখা যাবে
4. ক্রস (×) বাটনে ক্লিক করে সার্চ ক্লিয়ার করা যাবে

## উদাহরণ কোড

### সম্পূর্ণ উদাহরণ - সার্চ বন্ধ করা

```php
// functions.php বা main plugin file এ

add_action('init', 'my_plugin_settings_init');

function my_plugin_settings_init() {
    $framework = bizzplugin_framework();
    
    $panel = $framework->create_panel(array(
        'id' => 'my_plugin_settings',
        'title' => __('My Plugin Settings', 'my-plugin'),
        'menu_title' => __('My Plugin', 'my-plugin'),
        'menu_slug' => 'my-plugin-settings',
        'capability' => 'manage_options',
        'option_name' => 'my_plugin_options',
        'enable_search' => false,  // সার্চ বন্ধ
    ));
    
    // সেকশন এবং ফিল্ড যোগ করুন...
    $panel->add_section(array(
        'id' => 'general',
        'title' => __('General Settings', 'my-plugin'),
        'fields' => array(
            array(
                'id' => 'site_name',
                'type' => 'text',
                'title' => __('Site Name', 'my-plugin'),
            ),
        ),
    ));
}
```

### সম্পূর্ণ উদাহরণ - সার্চ চালু রাখা (ডিফল্ট)

```php
add_action('init', 'my_plugin_settings_init');

function my_plugin_settings_init() {
    $framework = bizzplugin_framework();
    
    // সার্চ ডিফল্টভাবে চালু থাকবে
    $panel = $framework->create_panel(array(
        'id' => 'my_plugin_settings',
        'title' => __('My Plugin Settings', 'my-plugin'),
        'option_name' => 'my_plugin_options',
        // enable_search দেওয়া হয়নি, তাই ডিফল্ট true হবে
    ));
    
    // অথবা স্পষ্টভাবে উল্লেখ করুন
    $panel->enable_search(true);
}
```

## API রেফারেন্স

### create_panel() প্যারামিটার

| প্যারামিটার | টাইপ | ডিফল্ট | বিবরণ |
|------------|------|-------|-------|
| `enable_search` | `boolean` | `true` | সার্চ ফিচার সক্রিয় করতে `true`, বন্ধ করতে `false` |

### চেইনেবল মেথড

| মেথড | বিবরণ |
|------|-------|
| `enable_search(true/false)` | সার্চ চালু বা বন্ধ করুন |
| `disable_search()` | সার্চ বন্ধ করুন |
| `is_search_enabled()` | সার্চ সক্রিয় আছে কি না রিটার্ন করে (boolean) |

## প্রশ্ন ও উত্তর

**প্রশ্ন: সার্চ কি সব প্যানেলে আলাদাভাবে নিয়ন্ত্রণ করা যায়?**

উত্তর: হ্যাঁ, প্রতিটি প্যানেলের জন্য আলাদাভাবে সার্চ চালু বা বন্ধ করা যায়।

**প্রশ্ন: সার্চ কি রিয়েল-টাইমে কাজ করে?**

উত্তর: হ্যাঁ, টাইপ করার সাথে সাথে রেজাল্ট দেখা যায় (debounced)।

**প্রশ্ন: সার্চ কি কেস-সেনসিটিভ?**

উত্তর: না, সার্চ কেস-ইনসেনসিটিভ। অর্থাৎ "General" এবং "general" একই রেজাল্ট দেবে।

## সাপোর্ট

কোনো সমস্যা বা প্রশ্ন থাকলে GitHub Issues এ জানান:
https://github.com/codersaiful/bizzplugin-option-framework/issues
