<?php
/**
 * BizzPlugin Options Framework - API Handler
 * 
 * @package BizzPlugin_Options_Framework
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * API Handler Class
 */
class BizzPlugin_API_Handler {
    
    /**
     * Framework instance
     */
    private $framework;
    
    /**
     * Get panel-specific API key option name
     */
    private function get_api_key_option($panel_id = null) {
        if ($panel_id) {
            return 'bizzplugin_api_key_' . $panel_id;
        }
        return 'bizzplugin_api_key_global';
    }
    
    /**
     * Constructor
     */
    public function __construct($framework) {
        $this->framework = $framework;
    }
    
    /**
     * Register REST routes
     */
    public function register_routes() {
        // Get all registered panels
        $panels = $this->framework->get_panels();
        
        foreach ($panels as $panel_id => $panel) {
            
            $option_name = $panel->get_option_name();
            $route_namespace = $panel->get_route_namespace();
            // Register GET/POST route for all options
            register_rest_route($route_namespace, $option_name, array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_options'),
                    'permission_callback' => array($this, 'check_permission'),
                    'args' => array(
                        'option_name' => array(
                            'default' => $option_name,
                        ),
                        'panel_id' => array(
                            'default' => $panel_id,
                        ),
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'update_options'),
                    'permission_callback' => array($this, 'check_permission'),
                    'args' => array(
                        'option_name' => array(
                            'default' => $option_name,
                        ),
                        'panel_id' => array(
                            'default' => $panel_id,
                        ),
                    ),
                ),
            ));
            
            // Register GET/POST route for single field
            register_rest_route($route_namespace, '/options/' . $option_name . '/(?P<field_id>[a-zA-Z0-9_-]+)', array(
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_single_option'),
                    'permission_callback' => array($this, 'check_permission'),
                    'args' => array(
                        'option_name' => array(
                            'default' => $option_name,
                        ),
                        'panel_id' => array(
                            'default' => $panel_id,
                        ),
                        'field_id' => array(
                            'required' => true,
                            'validate_callback' => function($param) {
                                return is_string($param) && !empty($param);
                            },
                        ),
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'update_single_option'),
                    'permission_callback' => array($this, 'check_permission'),
                    'args' => array(
                        'option_name' => array(
                            'default' => $option_name,
                        ),
                        'panel_id' => array(
                            'default' => $panel_id,
                        ),
                        'field_id' => array(
                            'required' => true,
                            'validate_callback' => function($param) {
                                return is_string($param) && !empty($param);
                            },
                        ),
                    ),
                ),
            ));
        }
        
        // Register generic options endpoint
        register_rest_route($route_namespace, '/options', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'list_options'),
            'permission_callback' => array($this, 'check_permission'),
        ));
    }
    
    /**
     * Check permission using x-api-key header
     * 
     * API Key authentication: Requests must include 'x-api-key' header with valid API key.
     * The API key can be generated from the API & Webhook settings page.
     */
    public function check_permission($request) {
        // Get API key from x-api-key header
        $api_key = $request->get_header('x-api-key');
        
        if (empty($api_key)) {
            return new WP_Error(
                'missing_api_key',
                __('API key is required. Include x-api-key header in your request.', 'bizzplugin-framework'),
                array('status' => 401)
            );
        }
        
        // Get panel ID from request parameters
        $panel_id = $request->get_param('panel_id');
        
        // Try panel-specific API key first, then global API key
        $api_key_option = $this->get_api_key_option($panel_id);
        $stored_api_key = get_option($api_key_option, '');
        
        // If panel-specific key doesn't exist, try global key for backward compatibility
        if (empty($stored_api_key)) {
            $global_api_key_option = $this->get_api_key_option();
            $stored_api_key = get_option($global_api_key_option, '');
        }
        
        if (empty($stored_api_key)) {
            return new WP_Error(
                'api_key_not_configured',
                sprintf(__('API key is not configured for panel "%s". Please generate an API key from settings.', 'bizzplugin-framework'), $panel_id),
                array('status' => 401)
            );
        }
        
        // Verify API key using timing-safe comparison
        if (!hash_equals($stored_api_key, $api_key)) {
            return new WP_Error(
                'invalid_api_key',
                __('Invalid API key provided.', 'bizzplugin-framework'),
                array('status' => 401)
            );
        }
        
        return true;
    }
    
    /**
     * Get options
     */
    public function get_options($request) {
        $option_name = $request->get_param('option_name');
        $panel_id = $request->get_param('panel_id');
        
        if (empty($option_name)) {
            return new WP_Error('missing_option', __('Option name is required', 'bizzplugin-framework'), array('status' => 400));
        }
        
        $options = get_option($option_name, array());
        
        return new WP_REST_Response(array(
            'success' => true,
            'option_name' => $option_name,
            'panel_id' => $panel_id,
            'data' => $options,
        ), 200);
    }
    
    /**
     * Update options
     */
    public function update_options($request) {
        $option_name = $request->get_param('option_name');
        $panel_id = $request->get_param('panel_id');
        $data = $request->get_json_params();
        
        if (empty($option_name)) {
            return new WP_Error('missing_option', __('Option name is required', 'bizzplugin-framework'), array('status' => 400));
        }
        
        $panel = $this->framework->get_panel($panel_id);
        if (!$panel) {
            return new WP_Error('invalid_panel', __('Panel not found', 'bizzplugin-framework'), array('status' => 404));
        }
        
        // Only sanitize and validate the fields that were submitted
        $ajax_handler = new BizzPlugin_Ajax_Handler();
        $sanitized_data = $ajax_handler->sanitize_partial_options($data, $panel);
        $validation_result = $ajax_handler->validate_partial_options($sanitized_data, $panel);
        
        if (is_wp_error($validation_result)) {
            return new WP_Error(
                'validation_failed',
                $validation_result->get_error_message(),
                array('status' => 400, 'errors' => $validation_result->get_error_data())
            );
        }
        
        // Merge with existing options (only submitted fields will be updated)
        $existing = get_option($option_name, array());
        $merged = array_merge($existing, $sanitized_data);
        
        // Save options
        $saved = update_option($option_name, $merged);
        
        // Trigger webhook
        do_action('bizzplugin_options_saved', $option_name, $merged, $existing, $panel_id);
        
        return new WP_REST_Response(array(
            'success' => true,
            'message' => __('Options updated successfully', 'bizzplugin-framework'),
            'data' => $merged,
        ), 200);
    }
    
    /**
     * Get single option field
     * 
     * Retrieves the value of a single option field. Returns the stored value or
     * the field's default value if not set.
     * 
     * @param WP_REST_Request $request REST API request object
     * @return WP_REST_Response|WP_Error Response with field value or error
     */
    public function get_single_option($request) {
        $option_name = $request->get_param('option_name');
        $panel_id = $request->get_param('panel_id');
        $field_id = $request->get_param('field_id');
        
        if (empty($option_name)) {
            return new WP_Error('missing_option', __('Option name is required', 'bizzplugin-framework'), array('status' => 400));
        }
        
        if (empty($field_id)) {
            return new WP_Error('missing_field', __('Field ID is required', 'bizzplugin-framework'), array('status' => 400));
        }
        
        $panel = $this->framework->get_panel($panel_id);
        if (!$panel) {
            return new WP_Error('invalid_panel', __('Panel not found', 'bizzplugin-framework'), array('status' => 404));
        }
        
        // Check if field exists in panel
        $fields = $panel->get_all_fields();
        if (!isset($fields[$field_id])) {
            return new WP_Error('invalid_field', __('Field not found in this panel', 'bizzplugin-framework'), array('status' => 404));
        }
        
        $options = get_option($option_name, array());
        $field_default = isset($fields[$field_id]['default']) ? $fields[$field_id]['default'] : null;
        $value = isset($options[$field_id]) ? $options[$field_id] : $field_default;
        
        return new WP_REST_Response(array(
            'success' => true,
            'option_name' => $option_name,
            'panel_id' => $panel_id,
            'field_id' => $field_id,
            'value' => $value,
        ), 200);
    }
    
    /**
     * Update single option field
     * 
     * Updates a single option field. Expects {"value": "..."} in the request body.
     * The field is validated and sanitized before saving.
     * 
     * @param WP_REST_Request $request REST API request object
     * @return WP_REST_Response|WP_Error Response with updated value or error
     */
    public function update_single_option($request) {
        $option_name = $request->get_param('option_name');
        $panel_id = $request->get_param('panel_id');
        $field_id = $request->get_param('field_id');
        $data = $request->get_json_params();
        
        if (empty($option_name)) {
            return new WP_Error('missing_option', __('Option name is required', 'bizzplugin-framework'), array('status' => 400));
        }
        
        if (empty($field_id)) {
            return new WP_Error('missing_field', __('Field ID is required', 'bizzplugin-framework'), array('status' => 400));
        }
        
        $panel = $this->framework->get_panel($panel_id);
        if (!$panel) {
            return new WP_Error('invalid_panel', __('Panel not found', 'bizzplugin-framework'), array('status' => 404));
        }
        
        // Check if field exists in panel
        $fields = $panel->get_all_fields();
        if (!isset($fields[$field_id])) {
            return new WP_Error('invalid_field', __('Field not found in this panel', 'bizzplugin-framework'), array('status' => 404));
        }
        
        // Get value from request body
        if (!isset($data['value'])) {
            return new WP_Error('missing_value', __('Value is required in request body', 'bizzplugin-framework'), array('status' => 400));
        }
        
        $value = $data['value'];
        $field = $fields[$field_id];
        
        // Skip premium fields if not premium
        if (!empty($field['premium']) && !$panel->is_premium()) {
            return new WP_Error('premium_required', __('This field requires premium access', 'bizzplugin-framework'), array('status' => 403));
        }
        
        // Sanitize the value
        $sanitized_value = BizzPlugin_Field_Sanitizer::sanitize($value, $field);
        
        // Validate the value
        $validator = new BizzPlugin_Field_Validator();
        $validator->validate($sanitized_value, $field);
        
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
            return new WP_Error(
                'validation_failed',
                $err_message,
                array('status' => 400, 'errors' => $validator->get_errors())
            );
        }
        
        // Get existing options and update the single field
        $existing = get_option($option_name, array());
        $old_value = isset($existing[$field_id]) ? $existing[$field_id] : null;
        $existing[$field_id] = $sanitized_value;
        
        // Save options
        update_option($option_name, $existing);
        
        // Trigger webhook
        do_action('bizzplugin_options_saved', $option_name, $existing, array($field_id => $old_value), $panel_id);
        
        return new WP_REST_Response(array(
            'success' => true,
            'message' => __('Option updated successfully', 'bizzplugin-framework'),
            'field_id' => $field_id,
            'value' => $sanitized_value,
        ), 200);
    }
    
    /**
     * List all registered options
     */
    public function list_options($request) {
        $panels = $this->framework->get_panels();
        $options_list = array();
        
        foreach ($panels as $panel_id => $panel) {
            $options_list[] = array(
                'panel_id' => $panel_id,
                'option_name' => $panel->get_option_name(),
                'endpoint' => rest_url('bizzplugin/v1/options/' . $panel->get_option_name()),
            );
        }
        
        return new WP_REST_Response(array(
            'success' => true,
            'panels' => $options_list,
        ), 200);
    }
    
    /**
     * Generate a new API key using cryptographically secure random bytes
     */
    public static function generate_api_key($panel_id = null) {
        // Use random_bytes for cryptographically secure random data
        $random_bytes = random_bytes(32);
        $api_key = bin2hex($random_bytes);
        
        // Create instance to use non-static method
        $instance = new self(null);
        $api_key_option = $instance->get_api_key_option($panel_id);
        
        update_option($api_key_option, $api_key);
        return $api_key;
    }
    
    /**
     * Get existing API key
     */
    public static function get_api_key($panel_id = null) {
        // Create instance to use non-static method
        $instance = new self(null);
        $api_key_option = $instance->get_api_key_option($panel_id);
        
        $api_key = get_option($api_key_option, '');
        
        // If panel-specific key doesn't exist, try global key for backward compatibility
        if (empty($api_key) && $panel_id) {
            $global_api_key_option = $instance->get_api_key_option();
            $api_key = get_option($global_api_key_option, '');
        }
        
        return $api_key;
    }
    
    /**
     * Delete API key
     */
    public static function delete_api_key($panel_id = null) {
        // Create instance to use non-static method
        $instance = new self(null);
        $api_key_option = $instance->get_api_key_option($panel_id);
        
        return delete_option($api_key_option);
    }
    
    /**
     * Get all API keys for all panels
     */
    public static function get_all_api_keys() {
        global $wpdb;
        
        // Get all API key options
        $api_keys = $wpdb->get_results(
            "SELECT option_name, option_value FROM {$wpdb->options} 
             WHERE option_name LIKE 'bizzplugin_api_key_%'",
            ARRAY_A
        );
        
        $result = array();
        foreach ($api_keys as $key_data) {
            $option_name = $key_data['option_name'];
            $panel_id = str_replace('bizzplugin_api_key_', '', $option_name);
            
            // Skip global key
            if ($panel_id === 'global') {
                $panel_id = 'global';
            }
            
            $result[$panel_id] = $key_data['option_value'];
        }
        
        return $result;
    }
}
