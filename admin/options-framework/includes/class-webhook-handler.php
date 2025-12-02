<?php
/**
 * BizzPlugin Options Framework - Webhook Handler
 * 
 * @package BizzPlugin_Options_Framework
 * @version 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Webhook Handler Class
 * 
 * Handles sending webhooks when options are saved, with support for:
 * - Multiple webhook URLs
 * - Custom JSON payloads with shortcodes/placeholders
 * - Bearer Token and Basic Authentication
 */
class BizzPlugin_Webhook_Handler {
    
    /**
     * Available shortcodes/placeholders for custom payloads
     * 
     * @var array
     */
    private $available_shortcodes = array(
        '{{event}}' => 'Event name (settings_saved)',
        '{{timestamp}}' => 'ISO 8601 timestamp',
        '{{site_url}}' => 'Site URL',
        '{{site_name}}' => 'Site name',
        '{{panel_id}}' => 'Panel ID',
        '{{option_name}}' => 'Option name',
        '{{data}}' => 'All settings as JSON',
        '{{changed_fields}}' => 'Changed fields as JSON',
        '{{user_id}}' => 'Current user ID',
        '{{user_email}}' => 'Current user email',
        '{{user_name}}' => 'Current user display name',
    );
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('bizzplugin_options_saved', array($this, 'send_webhook'), 10, 4);
    }
    
    /**
     * Get available shortcodes for UI display
     * 
     * @return array
     */
    public static function get_available_shortcodes() {
        return array(
            '{{event}}' => __('Event name (settings_saved)', 'bizzplugin-framework'),
            '{{timestamp}}' => __('ISO 8601 timestamp', 'bizzplugin-framework'),
            '{{site_url}}' => __('Site URL', 'bizzplugin-framework'),
            '{{site_name}}' => __('Site name', 'bizzplugin-framework'),
            '{{panel_id}}' => __('Panel ID', 'bizzplugin-framework'),
            '{{option_name}}' => __('Option name', 'bizzplugin-framework'),
            '{{data}}' => __('All settings as JSON object', 'bizzplugin-framework'),
            '{{changed_fields}}' => __('Changed fields as JSON object', 'bizzplugin-framework'),
            '{{user_id}}' => __('Current user ID', 'bizzplugin-framework'),
            '{{user_email}}' => __('Current user email', 'bizzplugin-framework'),
            '{{user_name}}' => __('Current user display name', 'bizzplugin-framework'),
            '{{field:FIELD_ID}}' => __('Specific field value (replace FIELD_ID)', 'bizzplugin-framework'),
        );
    }
    
    /**
     * Send webhook when options are saved
     * 
     * @param string $option_name Option name
     * @param array  $new_data    New data
     * @param array  $old_data    Old data
     * @param string $panel_id    Panel ID
     */
    public function send_webhook($option_name, $new_data, $old_data, $panel_id) {
        // Get webhooks configuration
        $webhooks = get_option('bizzplugin_webhooks_' . $option_name, array());
        
        // Backward compatibility: check for single webhook URL
        $legacy_webhook_url = get_option('bizzplugin_webhook_' . $option_name, '');
        if (!empty($legacy_webhook_url) && empty($webhooks)) {
            $webhooks = array(
                array(
                    'url' => $legacy_webhook_url,
                    'enabled' => true,
                    'auth_type' => 'none',
                    'custom_payload' => '',
                ),
            );
        }
        
        if (empty($webhooks)) {
            return;
        }
        
        // Get current user once to avoid redundant calls
        $current_user = wp_get_current_user();
        
        // Prepare base payload data for shortcode replacement
        $payload_data = array(
            'event' => 'settings_saved',
            'option_name' => $option_name,
            'panel_id' => $panel_id,
            'timestamp' => gmdate('c'),
            'site_url' => get_site_url(),
            'site_name' => get_bloginfo('name'),
            'data' => $new_data,
            'changed_fields' => $this->get_changed_fields($new_data, $old_data),
            'user_id' => get_current_user_id(),
            'user_email' => $current_user->user_email,
            'user_name' => $current_user->display_name,
        );
        
        // Send each webhook
        foreach ($webhooks as $webhook) {
            $this->send_single_webhook($webhook, $payload_data, $option_name);
        }
    }
    
    /**
     * Send a single webhook
     * 
     * @param array  $webhook      Webhook configuration
     * @param array  $payload_data Base payload data
     * @param string $option_name  Option name
     */
    private function send_single_webhook($webhook, $payload_data, $option_name) {
        // Check if enabled
        if (isset($webhook['enabled']) && !$webhook['enabled']) {
            return;
        }
        
        $webhook_url = isset($webhook['url']) ? $webhook['url'] : '';
        
        if (empty($webhook_url)) {
            return;
        }
        
        // Validate URL format
        if (!filter_var($webhook_url, FILTER_VALIDATE_URL)) {
            return;
        }
        
        // Security: Validate URL to prevent SSRF attacks
        if (!$this->is_safe_url($webhook_url)) {
            do_action('bizzplugin_webhook_error', new WP_Error('unsafe_url', 'Webhook URL is not allowed for security reasons.'), $webhook_url, $option_name);
            return;
        }
        
        // Build payload
        $custom_payload = isset($webhook['custom_payload']) ? $webhook['custom_payload'] : '';
        if (!empty($custom_payload)) {
            $body = $this->build_custom_payload($custom_payload, $payload_data);
        } else {
            // Default payload
            $payload = array(
                'event' => $payload_data['event'],
                'option_name' => $payload_data['option_name'],
                'panel_id' => $payload_data['panel_id'],
                'timestamp' => $payload_data['timestamp'],
                'site_url' => $payload_data['site_url'],
                'data' => $payload_data['data'],
                'changed_fields' => $payload_data['changed_fields'],
            );
            $payload = apply_filters('bizzplugin_webhook_payload', $payload, $option_name, $payload_data['panel_id']);
            $body = wp_json_encode($payload);
        }
        
        // Build headers
        $headers = array(
            'Content-Type' => 'application/json',
            'X-BizzPlugin-Event' => 'settings_saved',
            'X-BizzPlugin-Signature' => $this->generate_signature_from_body($body, $option_name),
        );
        
        // Add authentication headers
        $headers = $this->add_auth_headers($headers, $webhook);
        
        // Send webhook
        $response = wp_remote_post($webhook_url, array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => $headers,
            'body' => $body,
        ));
        
        // Log webhook result
        if (is_wp_error($response)) {
            do_action('bizzplugin_webhook_error', $response, $webhook_url, $option_name);
        } else {
            do_action('bizzplugin_webhook_sent', $response, $webhook_url, $option_name);
        }
    }
    
    /**
     * Build custom payload by replacing shortcodes
     * 
     * @param string $template     Custom payload template
     * @param array  $payload_data Base payload data
     * @return string JSON string
     */
    private function build_custom_payload($template, $payload_data) {
        // Replace simple shortcodes
        $replacements = array(
            '{{event}}' => $payload_data['event'],
            '{{timestamp}}' => $payload_data['timestamp'],
            '{{site_url}}' => $payload_data['site_url'],
            '{{site_name}}' => $payload_data['site_name'],
            '{{panel_id}}' => $payload_data['panel_id'],
            '{{option_name}}' => $payload_data['option_name'],
            '{{user_id}}' => (string) $payload_data['user_id'],
            '{{user_email}}' => $payload_data['user_email'],
            '{{user_name}}' => $payload_data['user_name'],
        );
        
        foreach ($replacements as $shortcode => $value) {
            $template = str_replace($shortcode, $value, $template);
        }
        
        // Replace JSON object shortcodes (need special handling)
        // These should be replaced without quotes since they're JSON objects
        $template = str_replace('"{{data}}"', wp_json_encode($payload_data['data']), $template);
        $template = str_replace("'{{data}}'", wp_json_encode($payload_data['data']), $template);
        $template = str_replace('{{data}}', wp_json_encode($payload_data['data']), $template);
        
        $template = str_replace('"{{changed_fields}}"', wp_json_encode($payload_data['changed_fields']), $template);
        $template = str_replace("'{{changed_fields}}'", wp_json_encode($payload_data['changed_fields']), $template);
        $template = str_replace('{{changed_fields}}', wp_json_encode($payload_data['changed_fields']), $template);
        
        // Replace field-specific shortcodes {{field:field_id}}
        if (preg_match_all('/\{\{field:([a-zA-Z0-9_-]+)\}\}/', $template, $matches)) {
            foreach ($matches[1] as $index => $field_id) {
                $field_value = isset($payload_data['data'][$field_id]) ? $payload_data['data'][$field_id] : '';
                if (is_array($field_value)) {
                    $template = str_replace($matches[0][$index], wp_json_encode($field_value), $template);
                } else {
                    $template = str_replace($matches[0][$index], (string) $field_value, $template);
                }
            }
        }
        
        return $template;
    }
    
    /**
     * Add authentication headers based on webhook config
     * 
     * @param array $headers Existing headers
     * @param array $webhook Webhook configuration
     * @return array Updated headers
     */
    private function add_auth_headers($headers, $webhook) {
        $auth_type = isset($webhook['auth_type']) ? $webhook['auth_type'] : 'none';
        
        switch ($auth_type) {
            case 'bearer':
                $token = isset($webhook['auth_token']) ? $webhook['auth_token'] : '';
                if (!empty($token)) {
                    $headers['Authorization'] = 'Bearer ' . $token;
                }
                break;
                
            case 'basic':
                $username = isset($webhook['auth_username']) ? $webhook['auth_username'] : '';
                $password = isset($webhook['auth_password']) ? $webhook['auth_password'] : '';
                if (!empty($username)) {
                    // Remove any colons from username to prevent Basic Auth format issues
                    // Password can contain colons as per RFC 7617
                    $username = str_replace(':', '', $username);
                    // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Required for Basic Auth
                    $headers['Authorization'] = 'Basic ' . base64_encode($username . ':' . $password);
                }
                break;
                
            case 'api_key':
                $header_name = isset($webhook['auth_header_name']) ? $webhook['auth_header_name'] : 'X-API-Key';
                $api_key = isset($webhook['auth_api_key']) ? $webhook['auth_api_key'] : '';
                if (!empty($api_key)) {
                    $headers[$header_name] = $api_key;
                }
                break;
        }
        
        return $headers;
    }
    
    /**
     * Generate signature from body string
     * 
     * @param string $body        JSON body string
     * @param string $option_name Option name
     * @return string HMAC signature
     */
    private function generate_signature_from_body($body, $option_name) {
        $secret = get_option('bizzplugin_webhook_secret_' . $option_name, wp_generate_password(32, false));
        
        // Save secret if not exists
        if (!get_option('bizzplugin_webhook_secret_' . $option_name)) {
            update_option('bizzplugin_webhook_secret_' . $option_name, $secret);
        }
        
        return hash_hmac('sha256', $body, $secret);
    }
    
    /**
     * Validate URL is safe (prevent SSRF attacks)
     * 
     * @param string $url The URL to validate
     * @return bool True if URL is safe, false otherwise
     */
    private function is_safe_url($url) {
        // Parse the URL
        $parsed = wp_parse_url($url);
        
        if (!$parsed || empty($parsed['host'])) {
            return false;
        }
        
        $host = strtolower($parsed['host']);
        $scheme = isset($parsed['scheme']) ? strtolower($parsed['scheme']) : '';
        
        // Only allow HTTPS (and HTTP for backward compatibility, though HTTPS is recommended)
        if (!in_array($scheme, array('http', 'https'), true)) {
            return false;
        }
        
        // Block localhost and loopback addresses
        $blocked_hosts = array(
            'localhost',
            '127.0.0.1',
            '0.0.0.0',
            '::1',
            '[::1]',
        );
        
        if (in_array($host, $blocked_hosts, true)) {
            return false;
        }
        
        // Block private IP ranges
        $ip = gethostbyname($host);
        if ($ip !== $host) {
            // Check if IP is in private ranges
            if ($this->is_private_ip($ip)) {
                return false;
            }
        }
        
        // Block internal domains
        $blocked_patterns = array(
            '/^192\.168\./',
            '/^10\./',
            '/^172\.(1[6-9]|2[0-9]|3[0-1])\./',
            '/^169\.254\./',
            '/\.local$/',
            '/\.internal$/',
            '/\.localhost$/',
        );
        
        foreach ($blocked_patterns as $pattern) {
            if (preg_match($pattern, $host) || preg_match($pattern, $ip)) {
                return false;
            }
        }
        
        // Allow filtering for custom validation
        return apply_filters('bizzplugin_webhook_url_allowed', true, $url, $host);
    }
    
    /**
     * Check if IP is in private range
     * 
     * @param string $ip IP address to check
     * @return bool True if IP is private
     */
    private function is_private_ip($ip) {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
    
    /**
     * Get changed fields
     */
    private function get_changed_fields($new_data, $old_data) {
        $changed = array();
        
        foreach ($new_data as $key => $value) {
            $old_value = isset($old_data[$key]) ? $old_data[$key] : null;
            if ($value !== $old_value) {
                $changed[$key] = array(
                    'old' => $old_value,
                    'new' => $value,
                );
            }
        }
        
        return $changed;
    }
}

// Initialize webhook handler
new BizzPlugin_Webhook_Handler();
