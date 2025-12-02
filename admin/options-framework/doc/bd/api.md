# REST API

BizzPlugin Options Framework বাহ্যিক অ্যাপ্লিকেশন থেকে অপশন পড়া এবং আপডেট করার জন্য একটি বিল্ট-ইন REST API প্রদান করে।

## অথেন্টিকেশন

সব API রিকোয়েস্টের জন্য একটি API কী ব্যবহার করে অথেন্টিকেশন প্রয়োজন। API কী `x-api-key` হেডারে পাস করা হয়।

### API কী জেনারেট করা

1. আপনার প্লাগইনের সেটিংস প্যানেলে যান
2. "API এবং Webhook" সেকশনে নেভিগেট করুন
3. "API কী জেনারেট করুন" এ ক্লিক করুন
4. জেনারেট করা কী কপি করুন এবং নিরাপদে সংরক্ষণ করুন

**গুরুত্বপূর্ণ**: প্রতিটি প্যানেলের নিজস্ব API কী আছে। কী পুনরায় জেনারেট করলে আগেরটি অবৈধ হয়ে যায়।

## API এন্ডপয়েন্ট

বেস URL: `https://your-site.com/wp-json/bizzplugin/v1/options/{option_name}`

যেখানে `{option_name}` হল প্যানেল তৈরি করার সময় আপনার নির্দিষ্ট করা `option_name`।

### GET - সব অপশন পুনরুদ্ধার

```bash
curl -X GET "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options" \
  -H "x-api-key: YOUR_API_KEY"
```

**রেসপন্স**:
```json
{
  "success": true,
  "data": {
    "site_name": "আমার সাইট",
    "enable_feature": "1",
    "primary_color": "#2271b1"
  }
}
```

### GET - একক অপশন পুনরুদ্ধার

```bash
curl -X GET "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options/site_name" \
  -H "x-api-key: YOUR_API_KEY"
```

**রেসপন্স**:
```json
{
  "success": true,
  "data": {
    "field_id": "site_name",
    "value": "আমার সাইট"
  }
}
```

### POST - সব অপশন আপডেট

```bash
curl -X POST "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options" \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "site_name": "নতুন সাইট নাম",
    "enable_feature": "0",
    "primary_color": "#ff0000"
  }'
```

**রেসপন্স**:
```json
{
  "success": true,
  "message": "অপশন সফলভাবে আপডেট হয়েছে",
  "data": {
    "site_name": "নতুন সাইট নাম",
    "enable_feature": "0",
    "primary_color": "#ff0000"
  }
}
```

### POST - একক অপশন আপডেট

```bash
curl -X POST "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options/site_name" \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "value": "আপডেট করা সাইট নাম"
  }'
```

**রেসপন্স**:
```json
{
  "success": true,
  "message": "ফিল্ড সফলভাবে আপডেট হয়েছে",
  "data": {
    "field_id": "site_name",
    "value": "আপডেট করা সাইট নাম"
  }
}
```

## এরর রেসপন্স

### অবৈধ API কী

```json
{
  "code": "rest_forbidden",
  "message": "অবৈধ API কী",
  "data": {
    "status": 401
  }
}
```

### অপশন খুঁজে পাওয়া যায়নি

```json
{
  "code": "rest_not_found",
  "message": "অপশন নাম খুঁজে পাওয়া যায়নি",
  "data": {
    "status": 404
  }
}
```

### ভ্যালিডেশন এরর

```json
{
  "code": "rest_invalid_param",
  "message": "ভ্যালিডেশন ব্যর্থ",
  "data": {
    "status": 400,
    "errors": {
      "email_field": "অবৈধ ইমেইল ঠিকানা"
    }
  }
}
```

## কোড উদাহরণ

### PHP (WordPress)

```php
// WordPress HTTP API ব্যবহার করে
$response = wp_remote_get(
    'https://external-site.com/wp-json/bizzplugin/v1/options/my_plugin_options',
    array(
        'headers' => array(
            'x-api-key' => 'your-api-key-here',
        ),
    )
);

if (!is_wp_error($response)) {
    $body = json_decode(wp_remote_retrieve_body($response), true);
    if ($body['success']) {
        $options = $body['data'];
    }
}
```

### JavaScript (Fetch)

```javascript
// অপশন পান
fetch('https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options', {
    method: 'GET',
    headers: {
        'x-api-key': 'your-api-key-here',
    },
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('অপশন:', data.data);
    }
});

// অপশন আপডেট
fetch('https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options', {
    method: 'POST',
    headers: {
        'x-api-key': 'your-api-key-here',
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        site_name: 'নতুন নাম',
        enable_feature: '1',
    }),
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('সফলভাবে আপডেট হয়েছে');
    }
});
```

### Python

```python
import requests

API_KEY = 'your-api-key-here'
BASE_URL = 'https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options'

# সব অপশন পান
response = requests.get(
    BASE_URL,
    headers={'x-api-key': API_KEY}
)
options = response.json()

# অপশন আপডেট
response = requests.post(
    BASE_URL,
    headers={
        'x-api-key': API_KEY,
        'Content-Type': 'application/json'
    },
    json={
        'site_name': 'নতুন সাইট নাম',
        'enable_feature': '1'
    }
)
result = response.json()
```

### Node.js

```javascript
const axios = require('axios');

const API_KEY = 'your-api-key-here';
const BASE_URL = 'https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options';

// সব অপশন পান
async function getOptions() {
    const response = await axios.get(BASE_URL, {
        headers: { 'x-api-key': API_KEY }
    });
    return response.data;
}

// অপশন আপডেট
async function updateOptions(options) {
    const response = await axios.post(BASE_URL, options, {
        headers: {
            'x-api-key': API_KEY,
            'Content-Type': 'application/json'
        }
    });
    return response.data;
}

// ব্যবহার
getOptions().then(data => console.log(data));
updateOptions({ site_name: 'নতুন নাম' }).then(data => console.log(data));
```

## নিরাপত্তা বিবেচনা

1. **API কী গোপন রাখুন**: ক্লায়েন্ট-সাইড কোডে কখনও API কী প্রকাশ করবেন না
2. **HTTPS ব্যবহার করুন**: সর্বদা API রিকোয়েস্টের জন্য HTTPS ব্যবহার করুন
3. **পর্যায়ক্রমে কী পুনরায় জেনারেট করুন**: যদি আপনি সন্দেহ করেন তাহলে API কী পুনরায় জেনারেট করুন
4. **প্যানেল-নির্দিষ্ট কী**: প্রতিটি প্যানেলের নিজস্ব কী আছে বিচ্ছিন্নতার জন্য
5. **ক্যাপাবিলিটি চেক**: API অপারেশন WordPress ক্যাপাবিলিটি সম্মান করে
6. **ডেটা ভ্যালিডেশন**: সব আগত ডেটা স্যানিটাইজ এবং ভ্যালিডেট করা হয়

## API কী ম্যানেজমেন্ট

### প্রোগ্রাম্যাটিকভাবে জেনারেট করা

```php
// একটি প্যানেলের জন্য API কী জেনারেট করুন
$api_key = BizzPlugin_API_Handler::generate_api_key('my_plugin_panel');

// বিদ্যমান API কী পান
$api_key = BizzPlugin_API_Handler::get_api_key('my_plugin_panel');

// API কী মুছুন
BizzPlugin_API_Handler::delete_api_key('my_plugin_panel');
```

### API কী স্টোরেজ

API কী WordPress অপশন টেবিলে এই প্যাটার্নে সংরক্ষিত থাকে:
- `bizzplugin_api_key_{panel_id}` - প্যানেল-নির্দিষ্ট API কী

---

## পরবর্তী পদক্ষেপ

- [ওয়েবহুক](webhooks.md) - ওয়েবহুক নোটিফিকেশন কনফিগার করুন
- [এক্সপোর্ট/ইমপোর্ট](export-import.md) - সেটিংস ব্যাকআপ এবং রিস্টোর করুন
- [উদাহরণ](examples.md) - আরও কোড উদাহরণ
