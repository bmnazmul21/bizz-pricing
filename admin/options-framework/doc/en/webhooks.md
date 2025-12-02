# Webhooks

Webhooks allow you to notify external services when settings are saved in your plugin.

## Configuration

### Setting Up a Webhook

1. Go to your plugin's settings panel
2. Navigate to the "API & Webhook" section
3. Enter your webhook URL
4. (Optional) Use the "Test Webhook" button to verify

### Webhook URL

The webhook URL should be an HTTPS endpoint that can receive POST requests.

```
https://your-external-service.com/webhook/endpoint
```

## Webhook Payload

When settings are saved, the webhook receives a POST request with the following JSON payload:

```json
{
  "event": "settings_saved",
  "option_name": "my_plugin_options",
  "timestamp": "2024-01-15T12:30:45Z",
  "site_url": "https://your-wordpress-site.com",
  "data": {
    "site_name": "My Site",
    "enable_feature": "1",
    "primary_color": "#2271b1"
  },
  "changed_fields": {
    "site_name": {
      "old": "Old Site Name",
      "new": "My Site"
    }
  }
}
```

### Payload Fields

| Field | Type | Description |
|-------|------|-------------|
| `event` | string | Event type (always `settings_saved` for now) |
| `option_name` | string | The WordPress option name |
| `timestamp` | string | ISO 8601 timestamp of when the save occurred |
| `site_url` | string | URL of the WordPress site |
| `data` | object | All current option values |
| `changed_fields` | object | Fields that were changed with old/new values |

## Webhook Headers

The webhook request includes these headers:

| Header | Description |
|--------|-------------|
| `Content-Type` | `application/json` |
| `X-BizzPlugin-Event` | Event type (`settings_saved` or `webhook_test`) |
| `X-BizzPlugin-Signature` | HMAC signature (if webhook secret is configured) |

## Webhook Secret & Signature Verification

When a webhook secret is generated, each webhook request includes a signature header that you can use to verify the request authenticity.

### How Signature Works

1. A webhook secret is automatically generated when you first configure a webhook URL
2. The secret is securely stored in the WordPress database (in the `wp_options` table as `bizzplugin_webhook_secret_{option_name}`)
3. Each webhook payload is signed using HMAC-SHA256 with the secret
4. The signature is included in the `X-BizzPlugin-Signature` header

### Secret Generation & Storage

- **Automatic Generation**: A 32-character cryptographically secure secret is generated when you save a webhook URL
- **Storage**: Stored in WordPress options table with the key `bizzplugin_webhook_secret_{option_name}`
- **Display**: The secret is partially masked in the UI (shows first 8 and last 8 characters)
- **Copy**: Use the copy button to copy the full secret

### Secret Rotation Best Practices

1. **Regenerate periodically**: Consider regenerating secrets every 3-6 months
2. **After key personnel changes**: Regenerate if someone with access leaves
3. **After suspected compromise**: Immediately regenerate if you suspect the secret was exposed
4. **Update endpoints**: After regeneration, update your webhook endpoint with the new secret

### Security Recommendations

1. **Store secrets securely**: Never hardcode secrets in code repositories
2. **Use environment variables**: Store secrets in environment variables on your receiving server
3. **Use HTTPS**: Always use HTTPS for webhook endpoints
4. **Implement timing-safe comparison**: Use `hash_equals()` (PHP) or `crypto.timingSafeEqual()` (Node.js)

### Verifying the Signature (PHP Example)

```php
<?php
// Your webhook secret (from the API & Webhook settings)
$webhook_secret = 'your-webhook-secret';

// Get the signature from the request header
$signature = $_SERVER['HTTP_X_BIZZPLUGIN_SIGNATURE'] ?? '';

// Get the raw POST body
$payload = file_get_contents('php://input');

// Calculate expected signature
$expected_signature = hash_hmac('sha256', $payload, $webhook_secret);

// Compare signatures
if (hash_equals($expected_signature, $signature)) {
    // Valid request - process the webhook
    $data = json_decode($payload, true);
    
    // Handle the event
    if ($data['event'] === 'settings_saved') {
        // Process saved settings
        $changed_fields = $data['changed_fields'];
        // Your logic here...
    }
    
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    // Invalid signature - reject the request
    http_response_code(401);
    echo json_encode(['error' => 'Invalid signature']);
}
```

### Verifying the Signature (Node.js Example)

```javascript
const crypto = require('crypto');
const express = require('express');
const app = express();

const WEBHOOK_SECRET = 'your-webhook-secret';

app.use(express.raw({ type: 'application/json' }));

app.post('/webhook', (req, res) => {
    const signature = req.headers['x-bizzplugin-signature'];
    const payload = req.body.toString();
    
    // Calculate expected signature
    const expectedSignature = crypto
        .createHmac('sha256', WEBHOOK_SECRET)
        .update(payload)
        .digest('hex');
    
    // Verify signature
    if (crypto.timingSafeEqual(
        Buffer.from(signature),
        Buffer.from(expectedSignature)
    )) {
        // Valid request
        const data = JSON.parse(payload);
        console.log('Settings saved:', data.changed_fields);
        res.json({ success: true });
    } else {
        // Invalid signature
        res.status(401).json({ error: 'Invalid signature' });
    }
});

app.listen(3000);
```

## Test Webhook

Use the "Test Webhook" button in the API & Webhook section to send a test request.

### Test Payload

```json
{
  "event": "webhook_test",
  "option_name": "my_plugin_options",
  "timestamp": "2024-01-15T12:30:45Z",
  "site_url": "https://your-wordpress-site.com",
  "message": "This is a test webhook from BizzPlugin Options Framework"
}
```

## Use Cases

### 1. Sync Settings to External Service

```php
// External service webhook handler
$data = json_decode(file_get_contents('php://input'), true);

if ($data['event'] === 'settings_saved') {
    // Update external service with new settings
    $api_client->updateSettings([
        'site_name' => $data['data']['site_name'],
        'theme_color' => $data['data']['primary_color'],
    ]);
}
```

### 2. Trigger CI/CD Pipeline

```javascript
// GitHub Actions webhook trigger
app.post('/webhook', (req, res) => {
    const data = req.body;
    
    if (data.event === 'settings_saved') {
        // Trigger GitHub workflow
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

### 3. Send Notifications

```php
// Send Slack notification when settings change
$data = json_decode(file_get_contents('php://input'), true);

if ($data['event'] === 'settings_saved' && !empty($data['changed_fields'])) {
    $changes = [];
    foreach ($data['changed_fields'] as $field => $values) {
        $changes[] = "`{$field}`: {$values['old']} → {$values['new']}";
    }
    
    $message = "Settings updated on {$data['site_url']}:\n" . implode("\n", $changes);
    
    // Post to Slack
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

### 4. Audit Logging

```php
// Log all settings changes to database
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

## Best Practices

1. **Always verify signatures** in production
2. **Use HTTPS** for webhook endpoints
3. **Respond quickly** (< 30 seconds) to avoid timeouts
4. **Implement retry logic** on the receiving end
5. **Log webhook events** for debugging
6. **Handle errors gracefully** and return appropriate status codes

## Troubleshooting

### Webhook not triggering?

1. Check that the webhook URL is accessible
2. Verify URL starts with `https://`
3. Use the "Test Webhook" button to debug
4. Check server logs for errors

### Signature verification failing?

1. Ensure you're using the correct webhook secret
2. Make sure you're reading the raw request body
3. Don't modify the payload before verification

---

## Next Steps

- [REST API](api.md) - API integration
- [Export/Import](export-import.md) - Backup and restore
- [Examples](examples.md) - More code examples
