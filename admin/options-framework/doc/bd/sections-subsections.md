# সেকশন এবং সাবসেকশন

এই ডকুমেন্ট সেকশন এবং সাবসেকশন ব্যবহার করে আপনার সেটিংস কীভাবে সংগঠিত করবেন তা ব্যাখ্যা করে।

## কাঠামো বোঝা

BizzPlugin Options Framework একটি হায়ারার্কিক্যাল কাঠামো ব্যবহার করে:

```
প্যানেল
├── সেকশন ১
│   ├── ফিল্ডসমূহ
│   └── সাবসেকশনসমূহ
│       ├── সাবসেকশন ১.১
│       │   └── ফিল্ডসমূহ
│       └── সাবসেকশন ১.২
│           └── ফিল্ডসমূহ
├── সেকশন ২
│   └── ফিল্ডসমূহ
└── সেকশন ৩
    └── সাবসেকশনসমূহ
        └── সাবসেকশন ৩.১
            └── ফিল্ডসমূহ
```

## সেকশন তৈরি করা

সেকশন হল আপনার সেটিংসের শীর্ষ-স্তরের সংগঠন। প্রতিটি সেকশন নেভিগেশন সাইডবারে একটি ট্যাব হিসেবে দেখায়।

### সেকশন প্রপার্টি

| প্রপার্টি | টাইপ | আবশ্যক | বিবরণ |
|----------|------|--------|-------|
| `id` | string | হ্যাঁ | সেকশনের জন্য অনন্য আইডেন্টিফায়ার |
| `title` | string | হ্যাঁ | নেভিগেশনে প্রদর্শিত শিরোনাম |
| `description` | string | না | সেকশন কন্টেন্টের উপরে দেখানো বিবরণ |
| `icon` | string | না | WordPress ড্যাশআইকন ক্লাস |
| `fields` | array | না | ফিল্ড কনফিগারেশনের অ্যারে |
| `subsections` | array | না | সাবসেকশন কনফিগারেশনের অ্যারে |
| `hide_reset_button` | bool | না | সত্য হলে, "সেকশন রিসেট" বাটন লুকায় |

### মৌলিক সেকশন উদাহরণ

```php
$sections = array(
    array(
        'id'          => 'general',
        'title'       => __('সাধারণ সেটিংস', 'textdomain'),
        'description' => __('সাধারণ প্লাগইন সেটিংস কনফিগার করুন।', 'textdomain'),
        'icon'        => 'dashicons dashicons-admin-generic',
        'fields'      => array(
            array(
                'id'      => 'site_name',
                'type'    => 'text',
                'title'   => __('সাইট নাম', 'textdomain'),
                'default' => '',
            ),
            array(
                'id'      => 'enable_feature',
                'type'    => 'switch',
                'title'   => __('ফিচার সক্রিয়', 'textdomain'),
                'default' => '1',
            ),
        ),
    ),
    array(
        'id'          => 'appearance',
        'title'       => __('চেহারা', 'textdomain'),
        'description' => __('ভিজ্যুয়াল সেটিংস কাস্টমাইজ করুন।', 'textdomain'),
        'icon'        => 'dashicons dashicons-admin-appearance',
        'fields'      => array(
            array(
                'id'      => 'primary_color',
                'type'    => 'color',
                'title'   => __('প্রাথমিক রঙ', 'textdomain'),
                'default' => '#2271b1',
            ),
        ),
    ),
);
```

## সাবসেকশন তৈরি করা

সাবসেকশন আপনাকে একটি সেকশনের মধ্যে ফিল্ডগুলো আরও সংগঠিত করতে দেয়। যখন একটি সেকশনে সাবসেকশন থাকে, তারা নেভিগেশনে নেস্টেড আইটেম হিসেবে দেখায়।

### সাবসেকশন প্রপার্টি

| প্রপার্টি | টাইপ | আবশ্যক | বিবরণ |
|----------|------|--------|-------|
| `id` | string | হ্যাঁ | সাবসেকশনের জন্য অনন্য আইডেন্টিফায়ার |
| `title` | string | হ্যাঁ | নেভিগেশনে প্রদর্শিত শিরোনাম |
| `description` | string | না | সাবসেকশন কন্টেন্টের উপরে দেখানো বিবরণ |
| `icon` | string | না | WordPress ড্যাশআইকন ক্লাস |
| `fields` | array | হ্যাঁ | ফিল্ড কনফিগারেশনের অ্যারে |
| `hide_reset_button` | bool | না | সত্য হলে, "সেকশন রিসেট" বাটন লুকায় |

### সাবসেকশন সহ সেকশন

```php
array(
    'id'          => 'basic',
    'title'       => __('মৌলিক সেটিংস', 'textdomain'),
    'description' => __('মৌলিক কনফিগারেশন অপশন।', 'textdomain'),
    'icon'        => 'dashicons dashicons-admin-settings',
    'fields'      => array(
        // প্রধান সেকশন ফিল্ড (সেকশনে ক্লিক করলে দেখায়)
        array(
            'id'      => 'main_field',
            'type'    => 'text',
            'title'   => __('প্রধান ফিল্ড', 'textdomain'),
            'default' => '',
        ),
    ),
    'subsections' => array(
        array(
            'id'          => 'text_settings',
            'title'       => __('টেক্সট সেটিংস', 'textdomain'),
            'description' => __('টেক্সট অপশন কনফিগার করুন।', 'textdomain'),
            'fields'      => array(
                array(
                    'id'      => 'font_size',
                    'type'    => 'number',
                    'title'   => __('ফন্ট সাইজ', 'textdomain'),
                    'default' => 16,
                    'min'     => 10,
                    'max'     => 30,
                ),
                array(
                    'id'      => 'font_family',
                    'type'    => 'select',
                    'title'   => __('ফন্ট ফ্যামিলি', 'textdomain'),
                    'default' => 'system',
                    'options' => array(
                        'system'   => __('সিস্টেম ডিফল্ট', 'textdomain'),
                        'roboto'   => 'Roboto',
                        'open-sans' => 'Open Sans',
                    ),
                ),
            ),
        ),
        array(
            'id'          => 'color_settings',
            'title'       => __('কালার সেটিংস', 'textdomain'),
            'description' => __('কালার অপশন কনফিগার করুন।', 'textdomain'),
            'fields'      => array(
                array(
                    'id'      => 'text_color',
                    'type'    => 'color',
                    'title'   => __('টেক্সট কালার', 'textdomain'),
                    'default' => '#333333',
                ),
                array(
                    'id'      => 'bg_color',
                    'type'    => 'color',
                    'title'   => __('ব্যাকগ্রাউন্ড কালার', 'textdomain'),
                    'default' => '#ffffff',
                ),
            ),
        ),
    ),
),
```

## চেইনেবল API ব্যবহার করা

আপনি ডাইনামিকভাবে সেকশন এবং সাবসেকশন তৈরি করতে চেইনেবল API ব্যবহার করতে পারেন:

### সেকশন যোগ

```php
$panel->add_section(array(
    'id'          => 'general',
    'title'       => __('সাধারণ', 'textdomain'),
    'description' => __('সাধারণ সেটিংস।', 'textdomain'),
    'icon'        => 'dashicons dashicons-admin-generic',
    'fields'      => array(
        array(
            'id'      => 'site_name',
            'type'    => 'text',
            'title'   => __('সাইট নাম', 'textdomain'),
            'default' => '',
        ),
    ),
));
```

### বিদ্যমান সেকশনে ফিল্ড যোগ

```php
$panel->add_field('general', array(
    'id'      => 'site_email',
    'type'    => 'email',
    'title'   => __('সাইট ইমেইল', 'textdomain'),
    'default' => '',
));
```

### বিদ্যমান সেকশনে সাবসেকশন যোগ

```php
$panel->add_subsection('general', array(
    'id'          => 'advanced',
    'title'       => __('অ্যাডভান্সড অপশন', 'textdomain'),
    'description' => __('অ্যাডভান্সড কনফিগারেশন।', 'textdomain'),
    'fields'      => array(
        array(
            'id'      => 'debug_mode',
            'type'    => 'checkbox',
            'title'   => __('ডিবাগ মোড', 'textdomain'),
            'default' => '0',
            'label'   => __('ডিবাগ সক্রিয়', 'textdomain'),
        ),
    ),
));
```

### সাবসেকশনে ফিল্ড যোগ

```php
$panel->add_subsection_field('general', 'advanced', array(
    'id'      => 'log_level',
    'type'    => 'select',
    'title'   => __('লগ লেভেল', 'textdomain'),
    'default' => 'error',
    'options' => array(
        'error'   => __('শুধু এরর', 'textdomain'),
        'warning' => __('সতর্কতা', 'textdomain'),
        'info'    => __('সব তথ্য', 'textdomain'),
    ),
));
```

## সেকশন রিমুভ করা

```php
// সম্পূর্ণ সেকশন রিমুভ
$panel->remove_section('section_id');

// সেকশন থেকে ফিল্ড রিমুভ
$panel->remove_field('section_id', 'field_id');

// সেকশন থেকে সাবসেকশন রিমুভ
$panel->remove_subsection('section_id', 'subsection_id');
```

## শুধু-সেকশন বনাম শুধু-ফিল্ড

### শুধু ফিল্ড সহ সেকশন (সাবসেকশন ছাড়া)

যখন একটি সেকশনে শুধু ফিল্ড থাকে, তাতে ক্লিক করলে সেই ফিল্ডগুলো সরাসরি দেখায়।

```php
array(
    'id'     => 'simple_section',
    'title'  => __('সাধারণ', 'textdomain'),
    'icon'   => 'dashicons dashicons-admin-generic',
    'fields' => array(
        // এখানে ফিল্ড
    ),
    // কোন 'subsections' কী নেই
)
```

### শুধু সাবসেকশন সহ সেকশন (প্রধান ফিল্ড ছাড়া)

যখন একটি সেকশনে শুধু সাবসেকশন থাকে, তাতে ক্লিক করলে সাবসেকশন দেখাতে এক্সপ্যান্ড হয়।

```php
array(
    'id'          => 'complex_section',
    'title'       => __('জটিল', 'textdomain'),
    'icon'        => 'dashicons dashicons-admin-settings',
    // কোন 'fields' কী নেই বা খালি অ্যারে
    'subsections' => array(
        array(
            'id'     => 'subsection_1',
            'title'  => __('সাব ১', 'textdomain'),
            'fields' => array(/* ফিল্ড */),
        ),
        array(
            'id'     => 'subsection_2',
            'title'  => __('সাব ২', 'textdomain'),
            'fields' => array(/* ফিল্ড */),
        ),
    ),
)
```

### ফিল্ড এবং সাবসেকশন উভয় সহ সেকশন

যখন একটি সেকশনে উভয়ই থাকে, সেকশনে ক্লিক করলে প্রধান ফিল্ড দেখায়, এবং সাবসেকশনগুলো এক্সপ্যান্ডেবল।

```php
array(
    'id'          => 'mixed_section',
    'title'       => __('মিশ্র', 'textdomain'),
    'icon'        => 'dashicons dashicons-admin-generic',
    'fields'      => array(
        // প্রধান সেকশন ফিল্ড
    ),
    'subsections' => array(
        // নেস্টেড সাবসেকশন
    ),
)
```

## রিসেট বাটন লুকানো

আপনি নির্দিষ্ট সেকশন বা সাবসেকশনের জন্য "সেকশন রিসেট" বাটন লুকাতে পারেন:

```php
array(
    'id'                => 'info_section',
    'title'             => __('তথ্য', 'textdomain'),
    'icon'              => 'dashicons dashicons-info',
    'hide_reset_button' => true,  // রিসেট বাটন লুকান
    'fields'            => array(
        array(
            'id'      => 'info_content',
            'type'    => 'html',
            'title'   => __('সম্পর্কে', 'textdomain'),
            'content' => '<p>এখানে তথ্য কন্টেন্ট।</p>',
        ),
    ),
)
```

## সেরা অনুশীলন

1. **যৌক্তিক গ্রুপিং**: সম্পর্কিত সেটিংস একসাথে গ্রুপ করুন
2. **আইকন ব্যবহার করুন**: ভিজ্যুয়াল নেভিগেশনের জন্য ড্যাশআইকন যোগ করুন
3. **বর্ণনামূলক নাম**: স্পষ্ট সেকশন/সাবসেকশন নাম ব্যবহার করুন
4. **গভীরতা সীমিত করুন**: গভীর নেস্টিং এড়িয়ে চলুন (সর্বোচ্চ ২ লেভেল: সেকশন → সাবসেকশন)
5. **ফিল্ড ব্যালেন্স করুন**: খুব বেশি ফিল্ড দিয়ে সেকশন ওভারলোড করবেন না
6. **জটিল সেটিংসের জন্য সাবসেকশন ব্যবহার করুন**: জটিল সেকশন ভেঙে ফেলুন

## বিল্ট-ইন সেকশন

ফ্রেমওয়ার্ক স্বয়ংক্রিয়ভাবে প্রতিটি প্যানেলে এই সেকশনগুলো যোগ করে:

- **এক্সপোর্ট/ইমপোর্ট**: সেটিংস এক্সপোর্ট এবং ইমপোর্ট
- **API এবং Webhook**: REST API এবং ওয়েবহুক কনফিগার করুন

এগুলো অভ্যন্তরীণভাবে হ্যান্ডেল করা হয় এবং সংজ্ঞায়িত করার প্রয়োজন নেই।

---

## পরবর্তী পদক্ষেপ

- [প্যানেল কনফিগারেশন](panel-configuration.md) - প্যানেল ব্র্যান্ডিং কনফিগার করুন
- [চেইনেবল API](chainable-api.md) - চেইনেবল API শিখুন
- [ফিল্ড টাইপ](field-types.md) - উপলব্ধ ফিল্ড টাইপ
