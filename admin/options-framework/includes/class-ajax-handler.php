<?php
/**
 * BizzPlugin Options Framework - AJAX Handler
 * 
 * @package BizzPlugin_Options_Framework
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX Handler Class
 * 
 * Handles sanitization and validation of option fields for AJAX requests and REST API.
 */
class BizzPlugin_Ajax_Handler {
    
    /**
     * Check if a field should be processed (not HTML and not locked premium)
     * 
     * @param array          $field Field configuration array
     * @param BizzPlugin_Panel $panel Panel instance
     * @return bool True if the field should be processed, false otherwise
     */
    private function should_process_field($field, $panel) {
        $non_editable_types = array('html', 'info', 'plugins', 'link', 'heading', 'divider', 'notice');
        // Skip HTML fields
        if (isset($field['type']) && in_array( $field['type'], $non_editable_types, true)) {
            return false;
        }
        
        // Skip premium fields if not premium
        if (!empty($field['premium']) && !$panel->is_premium()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Handle webhook URL if present in data
     * 
     * Saves the webhook URL as a separate option if present in the submitted data.
     * Also handles advanced webhook configuration (multiple webhooks, auth, custom payload).
     * 
     * @param array          $data  Submitted data array
     * @param BizzPlugin_Panel $panel Panel instance
     * @return void
     */
    private function handle_webhook_url($data, $panel) {
        $option_name = $panel->get_option_name();
        
        // Handle legacy single webhook URL (backward compatibility)
        if (isset($data['bizzplugin_webhook_url'])) {
            $webhook_url = esc_url_raw($data['bizzplugin_webhook_url']);
            update_option('bizzplugin_webhook_' . $option_name, $webhook_url);
        }
        
        // Handle advanced webhooks configuration
        if (isset($data['bizzplugin_webhooks']) && is_array($data['bizzplugin_webhooks'])) {
            $webhooks = array();
            
            foreach ($data['bizzplugin_webhooks'] as $webhook) {
                // Skip if URL is empty
                if (empty($webhook['url'])) {
                    continue;
                }
                
                $sanitized_webhook = array(
                    'url' => esc_url_raw($webhook['url']),
                    'enabled' => isset($webhook['enabled']) ? (bool) $webhook['enabled'] : true,
                    'auth_type' => isset($webhook['auth_type']) ? sanitize_key($webhook['auth_type']) : 'none',
                    'custom_payload' => isset($webhook['custom_payload']) ? $this->sanitize_json_template($webhook['custom_payload']) : '',
                );
                
                // Handle auth fields based on type
                switch ($sanitized_webhook['auth_type']) {
                    case 'bearer':
                        $sanitized_webhook['auth_token'] = isset($webhook['auth_token']) ? sanitize_text_field($webhook['auth_token']) : '';
                        break;
                    case 'basic':
                        $sanitized_webhook['auth_username'] = isset($webhook['auth_username']) ? sanitize_text_field($webhook['auth_username']) : '';
                        $sanitized_webhook['auth_password'] = isset($webhook['auth_password']) ? sanitize_text_field($webhook['auth_password']) : '';
                        break;
                    case 'api_key':
                        $sanitized_webhook['auth_header_name'] = isset($webhook['auth_header_name']) ? sanitize_text_field($webhook['auth_header_name']) : 'X-API-Key';
                        $sanitized_webhook['auth_api_key'] = isset($webhook['auth_api_key']) ? sanitize_text_field($webhook['auth_api_key']) : '';
                        break;
                }
                
                $webhooks[] = $sanitized_webhook;
            }
            
            update_option('bizzplugin_webhooks_' . $option_name, $webhooks);
        }
    }
    
    /**
     * Sanitize JSON template
     * 
     * Validates and sanitizes JSON template while preserving shortcodes.
     * Removes potentially dangerous content while allowing valid JSON structures.
     * 
     * @param string $template JSON template string
     * @return string Sanitized template
     */
    private function sanitize_json_template($template) {
        // Trim whitespace
        $template = trim($template);
        
        if (empty($template)) {
            return '';
        }
        
        // Remove NULL bytes and other control characters except newlines and tabs
        $template = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $template);
        
        // Strip any HTML/script tags but preserve JSON structure
        // First, temporarily replace shortcodes to protect them
        $shortcode_placeholder = '___BIZZPLUGIN_SC_';
        $shortcodes = array();
        $template = preg_replace_callback('/\{\{[^}]+\}\}/', function($match) use (&$shortcodes, $shortcode_placeholder) {
            $index = count($shortcodes);
            $shortcodes[$index] = $match[0];
            return $shortcode_placeholder . $index . '___';
        }, $template);
        
        // Remove script tags and event handlers
        $template = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $template);
        $template = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $template);
        
        // Restore shortcodes
        foreach ($shortcodes as $index => $shortcode) {
            $template = str_replace($shortcode_placeholder . $index . '___', $shortcode, $template);
        }
        
        return $template;
    }
    
    /**
     * Sanitize all options
     * 
     * Sanitizes all fields defined in the panel, setting empty values for fields
     * not present in the submitted data.
     * 
     * @param array          $data  Submitted data array
     * @param BizzPlugin_Panel $panel Panel instance
     * @return array Sanitized options array
     */
    public function sanitize_options($data, $panel) {
        $fields = $panel->get_all_fields();
        $sanitized = array();
        
        foreach ($fields as $field_id => $field) {
            if (!$this->should_process_field($field, $panel)) {
                continue;
            }
            
            // Get value from submitted data
            $value = isset($data[$field_id]) ? $data[$field_id] : '';
            
            // Sanitize the value
            $sanitized[$field_id] = BizzPlugin_Field_Sanitizer::sanitize($value, $field);
        }
        
        $this->handle_webhook_url($data, $panel);
        
        return $sanitized;
    }
    
    /**
     * Sanitize only submitted options (partial update)
     * 
     * Only sanitizes fields that are present in the submitted data.
     * Fields not included in the data are not processed, allowing partial updates.
     * 
     * @param array          $data  Submitted data array with field_id => value pairs
     * @param BizzPlugin_Panel $panel Panel instance
     * @return array Sanitized options array containing only the submitted fields
     */
    public function sanitize_partial_options($data, $panel) {
        $fields = $panel->get_all_fields();
        $sanitized = array();
        
        foreach ($data as $field_id => $value) {
            // Skip webhook URL - handled separately
            if ($field_id === 'bizzplugin_webhook_url') {
                continue;
            }
            
            // Skip if field doesn't exist in panel
            if (!isset($fields[$field_id])) {
                continue;
            }
            
            $field = $fields[$field_id];
            
            if (!$this->should_process_field($field, $panel)) {
                continue;
            }
            
            // Sanitize the value
            $sanitized[$field_id] = BizzPlugin_Field_Sanitizer::sanitize($value, $field);
        }
        
        $this->handle_webhook_url($data, $panel);
        
        return $sanitized;
    }
    
    /**
     * Validate all options
     * 
     * Validates all fields defined in the panel.
     * 
     * @param array          $data  Data array to validate
     * @param BizzPlugin_Panel $panel Panel instance
     * @return true|WP_Error True on success, WP_Error on validation failure
     */
    public function validate_options($data, $panel) {
        $fields = $panel->get_all_fields();
        $validator = new BizzPlugin_Field_Validator();
        
        foreach ($fields as $field_id => $field) {
            if (!$this->should_process_field($field, $panel)) {
                continue;
            }
            
            $value = isset($data[$field_id]) ? $data[$field_id] : '';
            $validator->validate($value, $field);
        }
        
        if ($validator->has_errors()) {
            $erros = $validator->get_errors();
            $err_message = 'Validation failed '; 
            if(!empty($erros) && is_array($erros)){
                foreach($erros as $key => $error){
                    // error_log('Validation error for field ' . $key . ': ' . $error);
                    error_log(print_r($erros, true));
                    $err_message .= "\n" . 'Field: ' . $key . ': ' . $error;
                }
            }
            return new WP_Error('validation_failed', $err_message, $validator->get_errors());
        }
        
        return true;
    }
    
    /**
     * Validate only submitted options (partial update)
     * 
     * Only validates fields that are present in the submitted data.
     * Fields not included in the data are not validated, allowing partial updates.
     * 
     * @param array          $data  Data array to validate with field_id => value pairs
     * @param BizzPlugin_Panel $panel Panel instance
     * @return true|WP_Error True on success, WP_Error on validation failure
     */
    public function validate_partial_options($data, $panel) {
        $fields = $panel->get_all_fields();
        $validator = new BizzPlugin_Field_Validator();
        
        foreach ($data as $field_id => $value) {
            // Skip if field doesn't exist in panel
            if (!isset($fields[$field_id])) {
                continue;
            }
            
            $field = $fields[$field_id];
            
            if (!$this->should_process_field($field, $panel)) {
                continue;
            }
            
            $validator->validate($value, $field);
        }
        
        if ($validator->has_errors()) {
            $err_message = 'Validation failed '; 
            if(!empty($erros) && is_array($erros)){
                foreach($erros as $key => $error){
                    // error_log('Validation error for field ' . $key . ': ' . $error);
                    error_log(print_r($erros, true));
                    $err_message .= "\n" . 'Field: ' . $key . ': ' . $error;
                }
            }
            return new WP_Error('validation_failed', $err_message, $validator->get_errors());
        }
        
        return true;
    }
}
