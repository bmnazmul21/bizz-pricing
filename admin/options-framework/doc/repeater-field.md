# রিপিটার ফিল্ড (Repeater Field)

রিপিটার ফিল্ড BizzPlugin Options Framework-এ একটি শক্তিশালী ফিল্ড টাইপ যা আপনাকে একাধিক ফিল্ড সেট তৈরি করতে দেয়। এটি ব্যবহার করে আপনি ডায়নামিক ডেটা ম্যানেজ করতে পারবেন - যেমন টিম মেম্বার, সোশ্যাল লিংক, FAQ ইত্যাদি।

## বৈশিষ্ট্যসমূহ

- ✅ একাধিক আইটেম যোগ করা যায়
- ✅ যেকোনো আইটেম মুছে ফেলা যায়
- ✅ ড্র্যাগ করে অর্ডার পরিবর্তন করা যায় (Sortable)
- ✅ সর্বোচ্চ ও সর্বনিম্ন আইটেম সংখ্যা সেট করা যায়
- ✅ প্রতিটি আইটেমে বিভিন্ন ধরনের সাব-ফিল্ড থাকতে পারে
- ✅ আইটেম কলাপস/এক্সপান্ড করা যায়
- ✅ **নতুন:** আইটেম যোগ/মুছে ফেলা বন্ধ করা যায় (Fixed items)

## সাপোর্টেড সাব-ফিল্ড টাইপ

রিপিটার ফিল্ডের ভিতরে নিম্নলিখিত ফিল্ড টাইপ ব্যবহার করা যায়:

- `text` - টেক্সট ইনপুট
- `email` - ইমেইল ইনপুট
- `url` - URL ইনপুট
- `number` - নম্বর ইনপুট
- `password` - পাসওয়ার্ড ইনপুট
- `textarea` - মাল্টি-লাইন টেক্সট
- `select` - ড্রপডাউন সিলেক্ট
- `checkbox` - চেকবক্স
- `color` - কালার পিকার
- `image` - ইমেজ আপলোড

## রিপিটার ফিল্ড প্রপার্টি

| প্রপার্টি | টাইপ | আবশ্যক | বিবরণ |
|----------|------|--------|-------|
| `id` | string | হ্যাঁ | ফিল্ডের জন্য অনন্য আইডেন্টিফায়ার |
| `type` | string | হ্যাঁ | `repeater` হতে হবে |
| `title` | string | হ্যাঁ | ফিল্ডের টাইটেল |
| `description` | string | না | সাহায্যকারী বিবরণ |
| `fields` | array | হ্যাঁ | সাব-ফিল্ডের অ্যারে |
| `button_text` | string | না | "Add" বাটনের টেক্সট (ডিফল্ট: "Add Item") |
| `max_items` | int | না | সর্বোচ্চ আইটেম সংখ্যা (0 = সীমাহীন) |
| `min_items` | int | না | সর্বনিম্ন আইটেম সংখ্যা (ডিফল্ট: 0) |
| `sortable` | bool | না | ড্র্যাগ করে সাজানো যাবে কিনা (ডিফল্ট: true) |
| `allow_add` | bool | না | নতুন আইটেম যোগ/মুছে ফেলা যাবে কিনা (ডিফল্ট: true) |
| `default` | array | না | ডিফল্ট আইটেম অ্যারে |

## সাব-ফিল্ড প্রপার্টি

প্রতিটি সাব-ফিল্ডের জন্য নিম্নলিখিত প্রপার্টি আছে:

| প্রপার্টি | টাইপ | আবশ্যক | বিবরণ |
|----------|------|--------|-------|
| `id` | string | হ্যাঁ | সাব-ফিল্ডের অনন্য আইডি |
| `type` | string | হ্যাঁ | ফিল্ড টাইপ |
| `title` | string | না | ফিল্ড লেবেল |
| `description` | string | না | সাহায্যকারী টেক্সট |
| `placeholder` | string | না | ইনপুট প্লেসহোল্ডার |
| `default` | mixed | না | ডিফল্ট মান |
| `options` | array | না | select ফিল্ডের জন্য অপশন |

## allow_add প্যারামিটার ব্যাখ্যা

`allow_add` প্যারামিটার দিয়ে আপনি রিপিটার ফিল্ডে নতুন আইটেম যোগ/মুছে ফেলার অপশন বন্ধ করতে পারবেন। যখন এটি `false` সেট করা হয়:

- "Add Item" বাটন দেখাবে না
- প্রতিটি আইটেমের "Remove" বাটন দেখাবে না
- শুধুমাত্র `default` বা `min_items` দিয়ে সেট করা আইটেমগুলো থাকবে
- ইউজার শুধুমাত্র বিদ্যমান আইটেমের ভ্যালু পরিবর্তন করতে পারবে

### উদাহরণ: নির্দিষ্ট সংখ্যক আইটেম

```php
array(
    'id'          => 'fixed_social_links',
    'type'        => 'repeater',
    'title'       => __('সোশ্যাল লিংক (৩টি নির্দিষ্ট)', 'textdomain'),
    'description' => __('শুধুমাত্র নির্দিষ্ট ৩টি সোশ্যাল লিংক এডিট করুন।', 'textdomain'),
    'allow_add'     => false,  // নতুন আইটেম যোগ/মুছে ফেলা বন্ধ
    'sortable'    => true,   // ড্র্যাগ করে সাজানো যাবে
    'fields'      => array(
        array(
            'id'      => 'platform',
            'type'    => 'select',
            'title'   => __('প্লাটফর্ম', 'textdomain'),
            'options' => array(
                'facebook'  => __('ফেসবুক', 'textdomain'),
                'twitter'   => __('টুইটার/X', 'textdomain'),
                'instagram' => __('ইনস্টাগ্রাম', 'textdomain'),
            ),
        ),
        array(
            'id'          => 'url',
            'type'        => 'url',
            'title'       => __('প্রোফাইল URL', 'textdomain'),
            'placeholder' => 'https://',
        ),
    ),
    'default' => array(
        array('platform' => 'facebook', 'url' => ''),
        array('platform' => 'twitter', 'url' => ''),
        array('platform' => 'instagram', 'url' => ''),
    ),
)
```

উপরের উদাহরণে:
- শুধুমাত্র ৩টি সোশ্যাল লিংক থাকবে
- ইউজার নতুন লিংক যোগ করতে পারবে না
- ইউজার কোনো লিংক মুছে ফেলতে পারবে না
- ইউজার শুধু platform এবং URL এডিট করতে পারবে
- ইউজার ড্র্যাগ করে অর্ডার পরিবর্তন করতে পারবে

## উদাহরণ ১: টিম মেম্বার রিপিটার

```php
array(
    'id'          => 'team_members',
    'type'        => 'repeater',
    'title'       => __('টিম মেম্বার', 'textdomain'),
    'description' => __('টিম মেম্বারদের তথ্য যোগ করুন।', 'textdomain'),
    'button_text' => __('মেম্বার যোগ করুন', 'textdomain'),
    'max_items'   => 10,
    'min_items'   => 0,
    'sortable'    => true,
    'fields'      => array(
        array(
            'id'          => 'name',
            'type'        => 'text',
            'title'       => __('নাম', 'textdomain'),
            'placeholder' => __('নাম লিখুন...', 'textdomain'),
        ),
        array(
            'id'          => 'email',
            'type'        => 'email',
            'title'       => __('ইমেইল', 'textdomain'),
            'placeholder' => 'email@example.com',
        ),
        array(
            'id'          => 'position',
            'type'        => 'text',
            'title'       => __('পদবী', 'textdomain'),
            'placeholder' => __('যেমন: ডেভেলপার', 'textdomain'),
        ),
        array(
            'id'    => 'image',
            'type'  => 'image',
            'title' => __('ছবি', 'textdomain'),
        ),
    ),
    'default' => array(),
)
```

## উদাহরণ ২: সোশ্যাল লিংক রিপিটার

```php
array(
    'id'          => 'social_links',
    'type'        => 'repeater',
    'title'       => __('সোশ্যাল লিংক', 'textdomain'),
    'description' => __('সোশ্যাল মিডিয়া লিংক যোগ করুন।', 'textdomain'),
    'button_text' => __('লিংক যোগ করুন', 'textdomain'),
    'max_items'   => 5,
    'min_items'   => 1,
    'sortable'    => true,
    'fields'      => array(
        array(
            'id'      => 'platform',
            'type'    => 'select',
            'title'   => __('প্লাটফর্ম', 'textdomain'),
            'options' => array(
                'facebook'  => __('ফেসবুক', 'textdomain'),
                'twitter'   => __('টুইটার/X', 'textdomain'),
                'instagram' => __('ইনস্টাগ্রাম', 'textdomain'),
                'linkedin'  => __('লিংকডইন', 'textdomain'),
                'youtube'   => __('ইউটিউব', 'textdomain'),
            ),
        ),
        array(
            'id'          => 'url',
            'type'        => 'url',
            'title'       => __('প্রোফাইল URL', 'textdomain'),
            'placeholder' => 'https://',
        ),
    ),
    'default' => array(
        array(
            'platform' => 'facebook',
            'url'      => '',
        ),
    ),
)
```

## উদাহরণ ৩: FAQ রিপিটার

```php
array(
    'id'          => 'faq_items',
    'type'        => 'repeater',
    'title'       => __('FAQ', 'textdomain'),
    'description' => __('প্রায়শই জিজ্ঞাসিত প্রশ্ন ও উত্তর যোগ করুন।', 'textdomain'),
    'button_text' => __('প্রশ্ন যোগ করুন', 'textdomain'),
    'sortable'    => true,
    'fields'      => array(
        array(
            'id'          => 'question',
            'type'        => 'text',
            'title'       => __('প্রশ্ন', 'textdomain'),
            'placeholder' => __('প্রশ্ন লিখুন...', 'textdomain'),
        ),
        array(
            'id'          => 'answer',
            'type'        => 'textarea',
            'title'       => __('উত্তর', 'textdomain'),
            'placeholder' => __('উত্তর লিখুন...', 'textdomain'),
            'rows'        => 4,
        ),
    ),
    'default' => array(),
)
```

## ডেটা রিট্রিভ করা

রিপিটার ফিল্ডের ডেটা একটি অ্যারে হিসেবে সংরক্ষিত হয়। প্রতিটি আইটেম একটি অ্যাসোসিয়েটিভ অ্যারে।

```php
// অপশন পড়ুন
$options = get_option('your_option_name', array());

// টিম মেম্বার ডেটা
$team_members = isset($options['team_members']) ? $options['team_members'] : array();

// লুপ করে ডেটা ব্যবহার করুন
if (!empty($team_members)) {
    foreach ($team_members as $member) {
        $name = isset($member['name']) ? $member['name'] : '';
        $email = isset($member['email']) ? $member['email'] : '';
        $position = isset($member['position']) ? $member['position'] : '';
        $image_id = isset($member['image']) ? $member['image'] : '';
        
        // ইমেজ URL পেতে
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
        
        // আউটপুট
        echo '<div class="team-member">';
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($name) . '">';
        }
        echo '<h3>' . esc_html($name) . '</h3>';
        echo '<p class="position">' . esc_html($position) . '</p>';
        echo '<p class="email">' . esc_html($email) . '</p>';
        echo '</div>';
    }
}
```

## ফ্রন্টএন্ড উদাহরণ: সোশ্যাল লিংক

```php
$options = get_option('your_option_name', array());
$social_links = isset($options['social_links']) ? $options['social_links'] : array();

if (!empty($social_links)) {
    echo '<div class="social-links">';
    foreach ($social_links as $link) {
        $platform = isset($link['platform']) ? $link['platform'] : '';
        $url = isset($link['url']) ? $link['url'] : '';
        
        if (!empty($url)) {
            // প্লাটফর্ম অনুযায়ী আইকন
            $icon_class = 'dashicons dashicons-share';
            switch ($platform) {
                case 'facebook':
                    $icon_class = 'dashicons dashicons-facebook';
                    break;
                case 'twitter':
                    $icon_class = 'dashicons dashicons-twitter';
                    break;
                // ... অন্যান্য প্লাটফর্ম
            }
            
            echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">';
            echo '<span class="' . esc_attr($icon_class) . '"></span>';
            echo '</a>';
        }
    }
    echo '</div>';
}
```

## সেকশনে রিপিটার যোগ করা

চেইনেবল API দিয়ে:

```php
$panel->add_section(array(
    'id' => 'my_section',
    'title' => __('আমার সেকশন', 'textdomain'),
    'fields' => array(
        array(
            'id'          => 'my_repeater',
            'type'        => 'repeater',
            'title'       => __('আইটেম তালিকা', 'textdomain'),
            'button_text' => __('আইটেম যোগ করুন', 'textdomain'),
            'fields'      => array(
                array(
                    'id'    => 'title',
                    'type'  => 'text',
                    'title' => __('শিরোনাম', 'textdomain'),
                ),
                array(
                    'id'    => 'description',
                    'type'  => 'textarea',
                    'title' => __('বিবরণ', 'textdomain'),
                ),
            ),
        ),
    ),
));
```

## গুরুত্বপূর্ণ টিপস

1. **ইউনিক ID**: প্রতিটি সাব-ফিল্ডের ID অনন্য হতে হবে (রিপিটার ফিল্ডের মধ্যে)।

2. **ডিফল্ট মান**: `min_items` সেট করলে সেই সংখ্যক আইটেম স্বয়ংক্রিয়ভাবে তৈরি হবে।

3. **সর্বোচ্চ সীমা**: `max_items` সেট করলে সেই সংখ্যার বেশি আইটেম যোগ করা যাবে না।

4. **সাজানো**: `sortable => true` রাখলে ড্র্যাগ করে আইটেমের অর্ডার পরিবর্তন করা যাবে।

5. **নির্দিষ্ট আইটেম**: `allow_add => false` সেট করলে শুধুমাত্র ডিফল্ট আইটেমগুলো থাকবে, নতুন যোগ/মুছে ফেলা যাবে না।

6. **ডেটা ফরম্যাট**: সংরক্ষিত ডেটা এরকম হয়:
```php
array(
    0 => array('name' => 'আলি', 'email' => 'ali@example.com'),
    1 => array('name' => 'করিম', 'email' => 'karim@example.com'),
)
```

## সম্পর্কিত ডকুমেন্টেশন

- [ফিল্ড টাইপ](bd/field-types.md) - সব ফিল্ড টাইপের রেফারেন্স
- [সেকশন এবং সাবসেকশন](bd/sections-subsections.md) - সেকশন কনফিগারেশন
- [চেইনেবল API](bd/chainable-api.md) - মডার্ন API ব্যবহার
