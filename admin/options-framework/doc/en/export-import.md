# Export/Import Settings

The BizzPlugin Options Framework provides built-in functionality to export and import settings, making it easy to backup, restore, or transfer configurations.

## Using Export/Import

### Accessing Export/Import

1. Go to your plugin's settings panel
2. Click on "Export/Import" in the navigation menu

### Exporting Settings

1. Click the "Export Settings" button
2. A JSON file will be downloaded to your computer
3. The file is named with the pattern: `{option_name}_export_{date}.json`

### Importing Settings

1. Click the "Import Settings" button
2. Select a previously exported JSON file
3. The settings will be loaded into the form
4. Click "SAVE" to apply the imported settings

**Important**: Imported settings are not automatically saved. You must click the Save button to apply them.

## Export File Format

The exported JSON file contains:

```json
{
  "panel_id": "my_plugin_settings",
  "option_name": "my_plugin_options",
  "exported_at": "2024-01-15T12:30:45.000Z",
  "site_url": "https://your-site.com",
  "version": "1.0.0",
  "data": {
    "site_name": "My Site",
    "enable_feature": "1",
    "primary_color": "#2271b1",
    "posts_per_page": 10
  }
}
```

### Export Fields

| Field | Description |
|-------|-------------|
| `panel_id` | The panel ID that created the export |
| `option_name` | WordPress option name |
| `exported_at` | ISO 8601 timestamp of export |
| `site_url` | URL of the source site |
| `version` | Plugin version at time of export |
| `data` | All saved option values |

## Programmatic Export/Import

### Export via PHP

```php
// Get current options
$options = get_option('my_plugin_options', array());

// Create export data
$export_data = array(
    'panel_id'    => 'my_plugin_settings',
    'option_name' => 'my_plugin_options',
    'exported_at' => gmdate('c'),
    'site_url'    => get_site_url(),
    'version'     => MY_PLUGIN_VERSION,
    'data'        => $options,
);

// Convert to JSON
$json = wp_json_encode($export_data, JSON_PRETTY_PRINT);

// Save to file or output
file_put_contents('/path/to/export.json', $json);
```

### Import via PHP

```php
// Read import file
$json = file_get_contents('/path/to/import.json');
$import_data = json_decode($json, true);

// Validate import data
if (!isset($import_data['data']) || !is_array($import_data['data'])) {
    wp_die('Invalid import file');
}

// Optional: Validate panel_id matches
if ($import_data['panel_id'] !== 'my_plugin_settings') {
    // Handle mismatch - maybe show warning
}

// Import the options
update_option('my_plugin_options', $import_data['data']);
```

### Export via REST API

```bash
curl -X GET "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options" \
  -H "x-api-key: YOUR_API_KEY" \
  -o export.json
```

### Import via REST API

```bash
curl -X POST "https://your-site.com/wp-json/bizzplugin/v1/options/my_plugin_options" \
  -H "x-api-key: YOUR_API_KEY" \
  -H "Content-Type: application/json" \
  -d @import.json
```

## Use Cases

### 1. Backup Before Updates

```php
// Add action before plugin update
add_action('upgrader_pre_install', function($upgrader, $options) {
    if (isset($options['plugin']) && $options['plugin'] === 'my-plugin/my-plugin.php') {
        // Create backup
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

### 2. Sync Settings Across Sites

```php
// Export from source site
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

### 3. Default Settings Template

```php
// Load default settings from template file
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

### 4. Multisite Settings Distribution

```php
// Copy settings from main site to all subsites
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

## Security Considerations

1. **Sensitive Data**: Export files may contain sensitive data (API keys, passwords). Handle them securely.

2. **Validation**: Always validate imported data before saving:
   ```php
   // Validate data structure
   if (!is_array($import_data['data'])) {
       return new WP_Error('invalid_data', 'Invalid import data');
   }
   
   // Sanitize values
   $framework = bizzplugin_framework();
   $panel = $framework->get_panel('my_plugin_settings');
   $sanitized = $ajax_handler->sanitize_options($import_data['data'], $panel);
   ```

3. **Panel ID Verification**: Check that the export came from the same panel:
   ```php
   if ($import_data['panel_id'] !== $current_panel_id) {
       // Show warning or reject import
   }
   ```

4. **File Upload Security**: When handling file uploads for import:
   - Verify file type (JSON only)
   - Check file size limits
   - Validate JSON structure

## Troubleshooting

### Import Not Working?

1. **Check JSON validity**: Ensure the file is valid JSON
2. **Check file encoding**: File should be UTF-8
3. **Verify panel_id**: Make sure it matches your panel
4. **Check browser console**: Look for JavaScript errors
5. **Remember to save**: Imported settings require clicking Save

### Export Empty?

1. **Check if options exist**: Verify `get_option('option_name')` returns data
2. **Check permissions**: User needs `manage_options` capability
3. **Check for JavaScript errors**: Look in browser console

---

## Next Steps

- [REST API](api.md) - API endpoints
- [Webhooks](webhooks.md) - Webhook notifications
- [Examples](examples.md) - Code examples
