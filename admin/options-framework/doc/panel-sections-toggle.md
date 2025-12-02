# প্যানেল সেকশান অন/অফ করা

BizzPlugin Options Framework-এ ডিফল্ট দুটি সেকশান থাকে: **Export/Import** এবং **API & Webhook**। এই সেকশানগুলো প্যানেল থেকে অফ (লুকানো) করার উপায় আছে।

## ডিফল্ট সেকশানসমূহ

- **Export/Import**: সেটিংস এক্সপোর্ট ও ইম্পোর্ট করার জন্য
- **API & Webhook**: REST API এবং Webhook কনফিগার করার জন্য

## সেকশান অফ করার উপায়

### ১. প্যানেল তৈরির সময়

```php
$framework = bizzplugin_framework();

$panel = $framework->create_panel(array(
    'id'                 => 'my_plugin_settings',
    'title'              => __('My Plugin Settings', 'textdomain'),
    'option_name'        => 'my_plugin_options',
    'show_export_import' => false,  // Export/Import সেকশান বন্ধ
    'show_api'           => false,  // API & Webhook সেকশান বন্ধ
));
```

### ২. চেইনেবল মেথড দিয়ে

```php
$framework = bizzplugin_framework();

$panel = $framework->create_panel(array(
    'id'          => 'my_plugin_settings',
    'title'       => __('My Plugin Settings', 'textdomain'),
    'option_name' => 'my_plugin_options',
));

// Export/Import সেকশান বন্ধ করুন
$panel->disable_export_import();

// API & Webhook সেকশান বন্ধ করুন
$panel->disable_api();
```

### ৩. আলাদা আলাদা অন/অফ

```php
// শুধু Export/Import বন্ধ, API সচল রাখুন
$panel->disable_export_import();
$panel->enable_api();

// অথবা শুধু API বন্ধ, Export/Import সচল রাখুন
$panel->enable_export_import();
$panel->disable_api();
```

## উপলব্ধ মেথডসমূহ

| মেথড | বিবরণ |
|------|-------|
| `disable_export_import()` | Export/Import সেকশান বন্ধ করে |
| `enable_export_import($enable = true)` | Export/Import সেকশান চালু করে |
| `is_export_import_enabled()` | Export/Import সেকশান চালু আছে কিনা দেখে |
| `disable_api()` | API & Webhook সেকশান বন্ধ করে |
| `enable_api($enable = true)` | API & Webhook সেকশান চালু করে |
| `is_api_enabled()` | API সেকশান চালু আছে কিনা দেখে |

## সম্পূর্ণ উদাহরণ

```php
add_action('init', function() {
    $framework = bizzplugin_framework();
    
    // প্যানেল তৈরি করুন (শুধু কাস্টম সেকশানসহ, কোন ডিফল্ট সেকশান নেই)
    $panel = $framework->create_panel(array(
        'id'                 => 'minimal_settings',
        'title'              => __('Minimal Settings', 'textdomain'),
        'option_name'        => 'minimal_options',
        'show_export_import' => false,
        'show_api'           => false,
        'enable_search'      => false,  // সার্চও বন্ধ করা যায়
    ));
    
    // আপনার কাস্টম সেকশান যোগ করুন
    $panel->add_section(array(
        'id'     => 'general',
        'title'  => __('General', 'textdomain'),
        'icon'   => 'dashicons-admin-generic',
        'fields' => array(
            array(
                'id'      => 'site_name',
                'type'    => 'text',
                'title'   => __('Site Name', 'textdomain'),
                'default' => get_bloginfo('name'),
            ),
        ),
    ));
});
```

## কখন ব্যবহার করবেন?

### Export/Import বন্ধ করতে পারেন যখন:
- সেটিংস সংবেদনশীল (যেমন: API কী, লাইসেন্স কী)
- মাল্টিসাইটে প্রতিটি সাইটের আলাদা সেটিংস দরকার
- সাধারণ ব্যবহারকারীদের জন্য Export/Import প্রয়োজন নেই

### API & Webhook বন্ধ করতে পারেন যখন:
- বাইরের অ্যাপ্লিকেশন থেকে সেটিংস পরিবর্তনের প্রয়োজন নেই
- সিকিউরিটির কারণে API এক্সপোজ করতে চান না
- Webhook ইন্টিগ্রেশন দরকার নেই

## সম্পর্কিত ডকুমেন্টেশন

- [প্যানেল কনফিগারেশন](bd/panel-configuration.md)
- [চেইনেবল API](bd/chainable-api.md)
- [Export/Import বিস্তারিত](bd/export-import.md)
- [API & Webhook বিস্তারিত](bd/api.md)
