# REST API

The BizzPlugin Options Framework provides a built-in REST API for reading and updating options from external applications.

## Authentication

All API requests require authentication using an API key. The API key is passed in the `x-api-key` header.

### Generating an API Key

1. Go to your plugin's settings panel
2. Navigate to the "API & Webhook" section
3. Click "Generate API Key"
4. Copy the generated key and store it securely

**Important**: Each panel has its own API key. Regenerating a key invalidates the previous one.

## API Endpoints

Base URL: `https://your-site.com/wp-json/bizzplugin/v1/options/{option_name}`

Where `{option_name}` is the `option_name` you specified when creating the panel.

### GET - Retrieve All Options

```bash
curl -X GET "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options" \
  -H "x-api-key: YOUR_API_KEY"
```

**Response**:
```json
{
  "success": true,
  "data": {
    "site_name": "My Site",
    "enable_feature": "1",
    "primary_color": "#2271b1"
  }
}
```

### GET - Retrieve Single Option

```bash
curl -X GET "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options/site_name" \
  -H "x-api-key: YOUR_API_KEY"
```

**Response**:
```json
{
  "success": true,
  "data": {
    "field_id": "site_name",
    "value": "My Site"
  }
}
```

### POST - Update All Options

```bash
curl -X POST "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options" \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "site_name": "New Site Name",
    "enable_feature": "0",
    "primary_color": "#ff0000"
  }'
```

**Response**:
```json
{
  "success": true,
  "message": "Options updated successfully",
  "data": {
    "site_name": "New Site Name",
    "enable_feature": "0",
    "primary_color": "#ff0000"
  }
}
```

### POST - Update Single Option

```bash
curl -X POST "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options/site_name" \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "value": "Updated Site Name"
  }'
```

**Response**:
```json
{
  "success": true,
  "message": "Field updated successfully",
  "data": {
    "field_id": "site_name",
    "value": "Updated Site Name"
  }
}
```

## Error Responses

### Invalid API Key

```json
{
  "code": "rest_forbidden",
  "message": "Invalid API key",
  "data": {
    "status": 401
  }
}
```

### Option Not Found

```json
{
  "code": "rest_not_found",
  "message": "Option name not found",
  "data": {
    "status": 404
  }
}
```

### Validation Error

```json
{
  "code": "rest_invalid_param",
  "message": "Validation failed",
  "data": {
    "status": 400,
    "errors": {
      "email_field": "Invalid email address"
    }
  }
}
```

## Code Examples

### PHP (WordPress)

```php
// Using WordPress HTTP API
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
// Get options
fetch('https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options', {
    method: 'GET',
    headers: {
        'x-api-key': 'your-api-key-here',
    },
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Options:', data.data);
    }
});

// Update options
fetch('https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options', {
    method: 'POST',
    headers: {
        'x-api-key': 'your-api-key-here',
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        site_name: 'New Name',
        enable_feature: '1',
    }),
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Updated successfully');
    }
});
```

### Python

```python
import requests

API_KEY = 'your-api-key-here'
BASE_URL = 'https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options'

# Get all options
response = requests.get(
    BASE_URL,
    headers={'x-api-key': API_KEY}
)
options = response.json()

# Update options
response = requests.post(
    BASE_URL,
    headers={
        'x-api-key': API_KEY,
        'Content-Type': 'application/json'
    },
    json={
        'site_name': 'New Site Name',
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

// Get all options
async function getOptions() {
    const response = await axios.get(BASE_URL, {
        headers: { 'x-api-key': API_KEY }
    });
    return response.data;
}

// Update options
async function updateOptions(options) {
    const response = await axios.post(BASE_URL, options, {
        headers: {
            'x-api-key': API_KEY,
            'Content-Type': 'application/json'
        }
    });
    return response.data;
}

// Usage
getOptions().then(data => console.log(data));
updateOptions({ site_name: 'New Name' }).then(data => console.log(data));
```

## Security Considerations

1. **Keep API keys secret**: Never expose API keys in client-side code
2. **Use HTTPS**: Always use HTTPS for API requests
3. **Regenerate keys periodically**: Regenerate API keys if you suspect compromise
4. **Panel-specific keys**: Each panel has its own key for isolation
5. **Capability checks**: API operations respect WordPress capabilities
6. **Data validation**: All incoming data is sanitized and validated

## API Key Management

### Generating Programmatically

```php
// Generate API key for a panel
$api_key = BizzPlugin_API_Handler::generate_api_key('my_plugin_panel');

// Get existing API key
$api_key = BizzPlugin_API_Handler::get_api_key('my_plugin_panel');

// Delete API key
BizzPlugin_API_Handler::delete_api_key('my_plugin_panel');
```

### API Key Storage

API keys are stored in the WordPress options table with the pattern:
- `bizzplugin_api_key_{panel_id}` - Panel-specific API key

---

## Next Steps

- [Webhooks](webhooks.md) - Configure webhook notifications
- [Export/Import](export-import.md) - Backup and restore settings
- [Examples](examples.md) - More code examples
