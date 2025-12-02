# ওয়েবহুক

ওয়েবহুক আপনার প্লাগইনে সেটিংস সংরক্ষণ করা হলে বাহ্যিক সার্ভিসে নোটিফাই করতে দেয়।

## কনফিগারেশন

### ওয়েবহুক সেট আপ করা

1. আপনার প্লাগইনের সেটিংস প্যানেলে যান
2. "API এবং Webhook" সেকশনে নেভিগেট করুন
3. আপনার ওয়েবহুক URL লিখুন
4. (ঐচ্ছিক) যাচাই করতে "টেস্ট ওয়েবহুক" বাটন ব্যবহার করুন

### ওয়েবহুক URL

ওয়েবহুক URL হতে হবে একটি HTTPS এন্ডপয়েন্ট যা POST রিকোয়েস্ট গ্রহণ করতে পারে।

```
https://your-external-service.com/webhook/endpoint
```

## ওয়েবহুক পেলোড

সেটিংস সংরক্ষণ করা হলে, ওয়েবহুক নিম্নলিখিত JSON পেলোড সহ একটি POST রিকোয়েস্ট গ্রহণ করে:

```json
{
  "event": "settings_saved",
  "option_name": "my_plugin_options",
  "timestamp": "2024-01-15T12:30:45Z",
  "site_url": "https://your-wordpress-site.com",
  "data": {
    "site_name": "আমার সাইট",
    "enable_feature": "1",
    "primary_color": "#2271b1"
  },
  "changed_fields": {
    "site_name": {
      "old": "পুরানো সাইট নাম",
      "new": "আমার সাইট"
    }
  }
}
```

### পেলোড ফিল্ড

| ফিল্ড | টাইপ | বিবরণ |
|-------|------|-------|
| `event` | string | ইভেন্ট টাইপ (এখন পর্যন্ত সর্বদা `settings_saved`) |
| `option_name` | string | WordPress অপশন নাম |
| `timestamp` | string | কখন সংরক্ষণ হয়েছে তার ISO 8601 টাইমস্ট্যাম্প |
| `site_url` | string | WordPress সাইটের URL |
| `data` | object | সব বর্তমান অপশন মান |
| `changed_fields` | object | পুরানো/নতুন মান সহ পরিবর্তিত ফিল্ড |

## ওয়েবহুক হেডার

ওয়েবহুক রিকোয়েস্টে এই হেডারগুলো অন্তর্ভুক্ত থাকে:

| হেডার | বিবরণ |
|-------|-------|
| `Content-Type` | `application/json` |
| `X-BizzPlugin-Event` | ইভেন্ট টাইপ (`settings_saved` বা `webhook_test`) |
| `X-BizzPlugin-Signature` | HMAC সিগনেচার (যদি ওয়েবহুক সিক্রেট কনফিগার করা থাকে) |

## ওয়েবহুক সিক্রেট এবং সিগনেচার যাচাইকরণ

যখন একটি ওয়েবহুক সিক্রেট জেনারেট করা হয়, প্রতিটি ওয়েবহুক রিকোয়েস্টে একটি সিগনেচার হেডার অন্তর্ভুক্ত থাকে যা আপনি রিকোয়েস্টের প্রামাণিকতা যাচাই করতে ব্যবহার করতে পারেন।

### সিগনেচার কীভাবে কাজ করে

1. আপনি প্রথম ওয়েবহুক URL কনফিগার করার সময় একটি ওয়েবহুক সিক্রেট স্বয়ংক্রিয়ভাবে জেনারেট হয়
2. সিক্রেট নিরাপদে WordPress ডাটাবেসে সংরক্ষিত থাকে (`wp_options` টেবিলে `bizzplugin_webhook_secret_{option_name}` হিসেবে)
3. প্রতিটি ওয়েবহুক পেলোড সিক্রেট দিয়ে HMAC-SHA256 ব্যবহার করে সাইন করা হয়
4. সিগনেচার `X-BizzPlugin-Signature` হেডারে অন্তর্ভুক্ত থাকে

### সিক্রেট জেনারেশন এবং স্টোরেজ

- **স্বয়ংক্রিয় জেনারেশন**: আপনি ওয়েবহুক URL সংরক্ষণ করলে একটি ৩২-অক্ষরের ক্রিপ্টোগ্রাফিকভাবে নিরাপদ সিক্রেট জেনারেট হয়
- **স্টোরেজ**: WordPress অপশন টেবিলে `bizzplugin_webhook_secret_{option_name}` কী দিয়ে সংরক্ষিত থাকে
- **প্রদর্শন**: UI-তে সিক্রেট আংশিকভাবে মাস্ক করা থাকে (প্রথম ৮ এবং শেষ ৮ অক্ষর দেখায়)
- **কপি**: সম্পূর্ণ সিক্রেট কপি করতে কপি বাটন ব্যবহার করুন

### সিক্রেট রোটেশন সেরা অনুশীলন

1. **পর্যায়ক্রমে পুনরায় জেনারেট করুন**: প্রতি ৩-৬ মাসে সিক্রেট পুনরায় জেনারেট করার কথা বিবেচনা করুন
2. **কর্মী পরিবর্তনের পরে**: অ্যাক্সেস সহ কেউ চলে গেলে পুনরায় জেনারেট করুন
3. **সন্দেহজনক আপস হলে**: সিক্রেট এক্সপোজ হয়েছে বলে সন্দেহ করলে অবিলম্বে পুনরায় জেনারেট করুন
4. **এন্ডপয়েন্ট আপডেট করুন**: পুনরায় জেনারেশনের পরে, নতুন সিক্রেট দিয়ে আপনার ওয়েবহুক এন্ডপয়েন্ট আপডেট করুন

### নিরাপত্তা সুপারিশ

1. **সিক্রেট নিরাপদে সংরক্ষণ করুন**: কোড রিপোজিটরিতে কখনও সিক্রেট হার্ডকোড করবেন না
2. **এনভায়রনমেন্ট ভেরিয়েবল ব্যবহার করুন**: আপনার রিসিভিং সার্ভারে এনভায়রনমেন্ট ভেরিয়েবলে সিক্রেট সংরক্ষণ করুন
3. **HTTPS ব্যবহার করুন**: ওয়েবহুক এন্ডপয়েন্টের জন্য সর্বদা HTTPS ব্যবহার করুন
4. **টাইমিং-সেফ তুলনা প্রয়োগ করুন**: `hash_equals()` (PHP) বা `crypto.timingSafeEqual()` (Node.js) ব্যবহার করুন

### সিগনেচার যাচাই (PHP উদাহরণ)

```php
<?php
// আপনার ওয়েবহুক সিক্রেট (API এবং Webhook সেটিংস থেকে)
$webhook_secret = 'your-webhook-secret';

// রিকোয়েস্ট হেডার থেকে সিগনেচার পান
$signature = $_SERVER['HTTP_X_BIZZPLUGIN_SIGNATURE'] ?? '';

// কাঁচা POST বডি পান
$payload = file_get_contents('php://input');

// প্রত্যাশিত সিগনেচার গণনা করুন
$expected_signature = hash_hmac('sha256', $payload, $webhook_secret);

// সিগনেচার তুলনা করুন
if (hash_equals($expected_signature, $signature)) {
    // বৈধ রিকোয়েস্ট - ওয়েবহুক প্রক্রিয়া করুন
    $data = json_decode($payload, true);
    
    // ইভেন্ট হ্যান্ডেল করুন
    if ($data['event'] === 'settings_saved') {
        // সংরক্ষিত সেটিংস প্রক্রিয়া করুন
        $changed_fields = $data['changed_fields'];
        // আপনার লজিক এখানে...
    }
    
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    // অবৈধ সিগনেচার - রিকোয়েস্ট প্রত্যাখ্যান করুন
    http_response_code(401);
    echo json_encode(['error' => 'অবৈধ সিগনেচার']);
}
```

### সিগনেচার যাচাই (Node.js উদাহরণ)

```javascript
const crypto = require('crypto');
const express = require('express');
const app = express();

const WEBHOOK_SECRET = 'your-webhook-secret';

app.use(express.raw({ type: 'application/json' }));

app.post('/webhook', (req, res) => {
    const signature = req.headers['x-bizzplugin-signature'];
    const payload = req.body.toString();
    
    // প্রত্যাশিত সিগনেচার গণনা করুন
    const expectedSignature = crypto
        .createHmac('sha256', WEBHOOK_SECRET)
        .update(payload)
        .digest('hex');
    
    // সিগনেচার যাচাই করুন
    if (crypto.timingSafeEqual(
        Buffer.from(signature),
        Buffer.from(expectedSignature)
    )) {
        // বৈধ রিকোয়েস্ট
        const data = JSON.parse(payload);
        console.log('সেটিংস সংরক্ষিত:', data.changed_fields);
        res.json({ success: true });
    } else {
        // অবৈধ সিগনেচার
        res.status(401).json({ error: 'অবৈধ সিগনেচার' });
    }
});

app.listen(3000);
```

## টেস্ট ওয়েবহুক

একটি টেস্ট রিকোয়েস্ট পাঠাতে API এবং Webhook সেকশনে "টেস্ট ওয়েবহুক" বাটন ব্যবহার করুন।

### টেস্ট পেলোড

```json
{
  "event": "webhook_test",
  "option_name": "my_plugin_options",
  "timestamp": "2024-01-15T12:30:45Z",
  "site_url": "https://your-wordpress-site.com",
  "message": "এটি BizzPlugin Options Framework থেকে একটি টেস্ট ওয়েবহুক"
}
```

## ব্যবহারের ক্ষেত্র

### ১. বাহ্যিক সার্ভিসে সেটিংস সিঙ্ক

```php
// বাহ্যিক সার্ভিস ওয়েবহুক হ্যান্ডলার
$data = json_decode(file_get_contents('php://input'), true);

if ($data['event'] === 'settings_saved') {
    // নতুন সেটিংস দিয়ে বাহ্যিক সার্ভিস আপডেট করুন
    $api_client->updateSettings([
        'site_name' => $data['data']['site_name'],
        'theme_color' => $data['data']['primary_color'],
    ]);
}
```

### ২. CI/CD পাইপলাইন ট্রিগার

```javascript
// GitHub Actions ওয়েবহুক ট্রিগার
app.post('/webhook', (req, res) => {
    const data = req.body;
    
    if (data.event === 'settings_saved') {
        // GitHub ওয়ার্কফ্লো ট্রিগার করুন
        axios.post(
            'https://api.github.com/repos/owner/repo/dispatches',
            {
                event_type: 'settings_updated',
                client_payload: data.data
            },
            {
                headers: {
                    'Authorization': `token ${GITHUB_TOKEN}`,
                    'Accept': 'application/vnd.github.v3+json'
                }
            }
        );
    }
    
    res.json({ success: true });
});
```

### ৩. নোটিফিকেশন পাঠানো

```php
// সেটিংস পরিবর্তন হলে Slack নোটিফিকেশন পাঠান
$data = json_decode(file_get_contents('php://input'), true);

if ($data['event'] === 'settings_saved' && !empty($data['changed_fields'])) {
    $changes = [];
    foreach ($data['changed_fields'] as $field => $values) {
        $changes[] = "`{$field}`: {$values['old']} → {$values['new']}";
    }
    
    $message = "{$data['site_url']}-এ সেটিংস আপডেট হয়েছে:\n" . implode("\n", $changes);
    
    // Slack-এ পোস্ট করুন
    $slack_webhook_url = 'https://hooks.slack.com/services/...';
    file_get_contents($slack_webhook_url, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode(['text' => $message]),
        ],
    ]));
}
```

### ৪. অডিট লগিং

```php
// ডাটাবেসে সব সেটিংস পরিবর্তন লগ করুন
$data = json_decode(file_get_contents('php://input'), true);

if ($data['event'] === 'settings_saved') {
    $db->insert('settings_audit_log', [
        'site_url' => $data['site_url'],
        'option_name' => $data['option_name'],
        'changed_fields' => json_encode($data['changed_fields']),
        'timestamp' => $data['timestamp'],
    ]);
}
```

## সেরা অনুশীলন

1. **সর্বদা প্রোডাকশনে সিগনেচার যাচাই করুন**
2. **ওয়েবহুক এন্ডপয়েন্টের জন্য HTTPS ব্যবহার করুন**
3. **টাইমআউট এড়াতে দ্রুত রেসপন্ড করুন** (< ৩০ সেকেন্ড)
4. **রিসিভিং এন্ডে রিট্রাই লজিক প্রয়োগ করুন**
5. **ডিবাগিংয়ের জন্য ওয়েবহুক ইভেন্ট লগ করুন**
6. **এরর সুন্দরভাবে হ্যান্ডেল করুন** এবং উপযুক্ত স্ট্যাটাস কোড রিটার্ন করুন

## সমস্যা সমাধান

### ওয়েবহুক ট্রিগার হচ্ছে না?

1. ওয়েবহুক URL অ্যাক্সেসযোগ্য কি না চেক করুন
2. URL `https://` দিয়ে শুরু হচ্ছে কি না যাচাই করুন
3. ডিবাগ করতে "টেস্ট ওয়েবহুক" বাটন ব্যবহার করুন
4. এরর জন্য সার্ভার লগ চেক করুন

### সিগনেচার যাচাই ব্যর্থ হচ্ছে?

1. সঠিক ওয়েবহুক সিক্রেট ব্যবহার করছেন কি না নিশ্চিত করুন
2. কাঁচা রিকোয়েস্ট বডি পড়ছেন কি না নিশ্চিত করুন
3. যাচাইয়ের আগে পেলোড মডিফাই করবেন না

---

## পরবর্তী পদক্ষেপ

- [REST API](api.md) - API ইন্টিগ্রেশন
- [এক্সপোর্ট/ইমপোর্ট](export-import.md) - ব্যাকআপ এবং রিস্টোর
- [উদাহরণ](examples.md) - আরও কোড উদাহরণ
