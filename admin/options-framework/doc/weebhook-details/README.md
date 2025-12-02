# BizzPlugin Framework - ওয়েবহুক ডকুমেন্টেশন

## ওয়েবহুক সিস্টেম ওভারভিউ

BizzPlugin Options Framework এখন advanced webhook সাপোর্ট প্রদান করে:

- **মাল্টিপল ওয়েবহুক URL**: একাধিক endpoint-এ নোটিফিকেশন পাঠান
- **কাস্টম JSON পেলোড**: নিজের মতো JSON স্ট্রাকচার ডিফাইন করুন
- **Authentication সাপোর্ট**: Bearer Token, Basic Auth, এবং API Key
- **Dynamic Shortcodes**: পেলোডে ডাইনামিক ডাটা যোগ করুন

---

## ওয়েবহুক কনফিগারেশন

### মাল্টিপল ওয়েবহুক

প্রতিটি সেটিংস প্যানেলে একাধিক ওয়েবহুক কনফিগার করা যায়। প্রতিটি ওয়েবহুকের জন্য আলাদা:
- URL
- Authentication
- Custom Payload

### Authentication টাইপ

#### 1. No Authentication
কোনো authentication header পাঠানো হবে না।

#### 2. Bearer Token
```
Authorization: Bearer YOUR_TOKEN_HERE
```

#### 3. Basic Authentication
```
Authorization: Basic base64(username:password)
```

#### 4. API Key Header
কাস্টম header নামে API key পাঠান:
```
X-API-Key: YOUR_API_KEY
```
অথবা যেকোনো কাস্টম header নাম ব্যবহার করুন।

---

## কাস্টম JSON পেলোড

### ডিফল্ট পেলোড স্ট্রাকচার

কাস্টম পেলোড না দিলে নিচের ফরম্যাট ব্যবহার হবে:

```json
{
    "event": "settings_saved",
    "option_name": "my_plugin_options",
    "panel_id": "my_plugin",
    "timestamp": "2024-01-01T12:00:00+00:00",
    "site_url": "https://example.com",
    "data": {
        "field1": "value1",
        "field2": "value2"
    },
    "changed_fields": {
        "field1": {
            "old": "old_value",
            "new": "new_value"
        }
    }
}
```

### কাস্টম পেলোড উদাহরণ

#### সিম্পল পেলোড
```json
{
    "event": "{{event}}",
    "site": "{{site_url}}",
    "time": "{{timestamp}}"
}
```

#### সব ডাটা সহ
```json
{
    "webhook_type": "settings_update",
    "source": "{{site_name}}",
    "url": "{{site_url}}",
    "settings": {{data}},
    "changes": {{changed_fields}},
    "updated_by": {
        "id": "{{user_id}}",
        "email": "{{user_email}}",
        "name": "{{user_name}}"
    }
}
```

#### নির্দিষ্ট ফিল্ড
```json
{
    "event": "config_changed",
    "api_key": "{{field:api_key}}",
    "debug_mode": "{{field:debug_mode}}",
    "site": "{{site_url}}"
}
```

---

## উপলব্ধ শর্টকোড/প্লেসহোল্ডার

| প্লেসহোল্ডার | বিবরণ | উদাহরণ আউটপুট |
|-------------|--------|---------------|
| `{{event}}` | ইভেন্ট নাম | `settings_saved` |
| `{{timestamp}}` | ISO 8601 ফরম্যাটে সময় | `2024-01-01T12:00:00+00:00` |
| `{{site_url}}` | সাইট URL | `https://example.com` |
| `{{site_name}}` | সাইট নাম | `My Website` |
| `{{panel_id}}` | প্যানেল ID | `my_plugin` |
| `{{option_name}}` | অপশন নাম | `my_plugin_options` |
| `{{data}}` | সব সেটিংস JSON অবজেক্ট | `{"field1": "value1", ...}` |
| `{{changed_fields}}` | পরিবর্তিত ফিল্ড JSON | `{"field1": {"old": "a", "new": "b"}}` |
| `{{user_id}}` | বর্তমান ইউজার ID | `1` |
| `{{user_email}}` | বর্তমান ইউজার ইমেইল | `admin@example.com` |
| `{{user_name}}` | বর্তমান ইউজার নাম | `Admin User` |
| `{{field:FIELD_ID}}` | নির্দিষ্ট ফিল্ডের ভ্যালু | ফিল্ডের ভ্যালু |

### গুরুত্বপূর্ণ নোট

- **JSON অবজেক্ট শর্টকোড**: `{{data}}` এবং `{{changed_fields}}` JSON অবজেক্ট রিটার্ন করে। এগুলো quotes (`"`) এর মধ্যে রাখবেন না।

✅ সঠিক:
```json
{
    "settings": {{data}}
}
```

❌ ভুল:
```json
{
    "settings": "{{data}}"
}
```

---

## প্রোগ্রামেটিক্যালি ওয়েবহুক কনফিগার করা

### PHP দিয়ে ওয়েবহুক সেট করা

```php
<?php
// Multiple webhooks save করুন
$webhooks = array(
    array(
        'url' => 'https://api.example.com/webhook1',
        'enabled' => true,
        'auth_type' => 'bearer',
        'auth_token' => 'your-bearer-token',
        'custom_payload' => '',
    ),
    array(
        'url' => 'https://api.example.com/webhook2',
        'enabled' => true,
        'auth_type' => 'basic',
        'auth_username' => 'user',
        'auth_password' => 'pass',
        'custom_payload' => '{"event": "{{event}}", "data": {{data}}}',
    ),
);

update_option('bizzplugin_webhooks_' . $option_name, $webhooks);
```

### Filter দিয়ে পেলোড মডিফাই করা

```php
<?php
add_filter('bizzplugin_webhook_payload', function($payload, $option_name, $panel_id) {
    // কাস্টম ডাটা যোগ করুন
    $payload['custom_key'] = 'custom_value';
    $payload['environment'] = defined('WP_DEBUG') && WP_DEBUG ? 'development' : 'production';
    
    return $payload;
}, 10, 3);
```

---

## ওয়েবহুক সিকিউরিটি

### Signature Verification

প্রতিটি ওয়েবহুক রিকোয়েস্টে `X-BizzPlugin-Signature` হেডার থাকে যা HMAC SHA256 দিয়ে সাইন করা।

#### রিসিভিং সার্ভারে verify করা

```php
<?php
// রিসিভিং সার্ভারে
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_BIZZPLUGIN_SIGNATURE'] ?? '';
$secret = 'your-webhook-secret'; // API section থেকে কপি করুন

$expected = hash_hmac('sha256', $payload, $secret);

if (hash_equals($expected, $signature)) {
    // Valid signature
    $data = json_decode($payload, true);
    // Process data...
    http_response_code(200);
} else {
    // Invalid signature
    http_response_code(401);
    exit('Invalid signature');
}
```

### URL Validation

Framework স্বয়ংক্রিয়ভাবে SSRF (Server-Side Request Forgery) থেকে সুরক্ষা দেয়:

- Localhost এবং loopback addresses ব্লক করা হয়
- Private IP ranges (192.168.x.x, 10.x.x.x, 172.16-31.x.x) ব্লক করা হয়
- শুধু HTTP এবং HTTPS URL অনুমোদিত

---

## Webhook Events

বর্তমানে শুধু একটি ইভেন্ট সাপোর্ট করে:

| Event | কখন ফায়ার হয় |
|-------|--------------|
| `settings_saved` | সেটিংস সেভ হলে |

---

## এরর হ্যান্ডলিং এবং লগিং

### ওয়েবহুক এরর লগ করা

```php
<?php
// এরর লগ করুন
add_action('bizzplugin_webhook_error', function($error, $webhook_url, $option_name) {
    error_log(sprintf(
        'Webhook Error: %s | URL: %s | Option: %s',
        $error->get_error_message(),
        $webhook_url,
        $option_name
    ));
}, 10, 3);

// সফল ওয়েবহুক লগ করুন
add_action('bizzplugin_webhook_sent', function($response, $webhook_url, $option_name) {
    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code >= 400) {
        error_log(sprintf(
            'Webhook Failed: Status %d | URL: %s',
            $status_code,
            $webhook_url
        ));
    }
}, 10, 3);
```

---

## সম্পর্কিত ডকুমেন্টেশন

- [English Webhook Documentation](../en/webhooks.md)
- [REST API Documentation](../en/api.md)
- [Filters & Hooks](../en/filters-hooks.md)
- [Extending Framework Guide](../extending-framework.md)
