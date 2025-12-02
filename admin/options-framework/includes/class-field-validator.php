<?php
/**
 * BizzPlugin Options Framework - Field Validator
 * 
 * @package BizzPlugin_Options_Framework
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Field Validator Class
 */
class BizzPlugin_Field_Validator {
    
    /**
     * Validation errors
     */
    private $errors = array();
    
    /**
     * Validate field value
     */
    public function validate($value, $field) {
        $type = isset($field['type']) ? $field['type'] : 'text';
        $field_id = isset($field['id']) ? $field['id'] : '';
        $field_title = isset($field['title']) ? $field['title'] : $field_id;
        
        // Check required
        if (!empty($field['required'])) {
            if ($this->is_empty($value)) {
                $this->add_error(
                    $field_id,
                    /* translators: %s: field title */
                    sprintf(__('%s is required.', 'bizzplugin-framework'), $field_title)
                );
                return false;
            }
        }
        
        // Skip further validation if empty and not required
        if ($this->is_empty($value)) {
            return true;
        }
        
        // Custom validation callback
        if (isset($field['validate_callback']) && is_callable($field['validate_callback'])) {
            $result = call_user_func($field['validate_callback'], $value, $field);
            if (is_wp_error($result)) {
                $this->add_error($field_id, $result->get_error_message());
                return false;
            }
            if ($result === false) {
                $this->add_error($field_id, sprintf(__('%s has an invalid value.', 'bizzplugin-framework'), $field_title));
                return false;
            }
        }
        
        // Type-specific validation
        switch ($type) {
            case 'email':
                if (!is_email($value)) {
                    $this->add_error($field_id, sprintf(__('%s must be a valid email address.', 'bizzplugin-framework'), $field_title));
                    return false;
                }
                break;
                
            case 'url':
                if (!filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->add_error($field_id, sprintf(__('%s must be a valid URL.', 'bizzplugin-framework'), $field_title));
                    return false;
                }
                break;
                
            case 'number':
                if (!is_numeric($value)) {
                    $this->add_error($field_id, sprintf(__('%s must be a number.', 'bizzplugin-framework'), $field_title));
                    return false;
                }
                if (isset($field['min']) && $value < $field['min']) {
                    $this->add_error($field_id, sprintf(__('%s must be at least %s.', 'bizzplugin-framework'), $field_title, $field['min']));
                    return false;
                }
                if (isset($field['max']) && $value > $field['max']) {
                    $this->add_error($field_id, sprintf(__('%s must be no more than %s.', 'bizzplugin-framework'), $field_title, $field['max']));
                    return false;
                }
                break;
                
            case 'color':
                if (!preg_match('/^#[a-fA-F0-9]{6}$/', $value)) {
                    $this->add_error($field_id, sprintf(__('%s must be a valid hex color.', 'bizzplugin-framework'), $field_title));
                    return false;
                }
                break;
                
            case 'date':
                $timestamp = strtotime($value);
                if ($timestamp === false) {
                    $this->add_error($field_id, sprintf(__('%s must be a valid date.', 'bizzplugin-framework'), $field_title));
                    return false;
                }
                break;
        }
        
        // Pattern validation
        if (isset($field['pattern']) && !preg_match($field['pattern'], $value)) {
            $pattern_message = isset($field['pattern_message']) 
                ? $field['pattern_message'] 
                : sprintf(__('%s does not match the required pattern.', 'bizzplugin-framework'), $field_title);
            $this->add_error($field_id, $pattern_message);
            return false;
        }
        
        // Min/Max length validation for text fields
        if (in_array($type, array('text', 'textarea', 'password'))) {
            $length = strlen($value);
            if (isset($field['minlength']) && $length < $field['minlength']) {
                $this->add_error($field_id, sprintf(__('%s must be at least %d characters.', 'bizzplugin-framework'), $field_title, $field['minlength']));
                return false;
            }
            if (isset($field['maxlength']) && $length > $field['maxlength']) {
                $this->add_error($field_id, sprintf(__('%s must be no more than %d characters.', 'bizzplugin-framework'), $field_title, $field['maxlength']));
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if value is empty
     */
    private function is_empty($value) {
        if (is_array($value)) {
            return empty($value);
        }
        return $value === '' || $value === null;
    }
    
    /**
     * Add error
     */
    public function add_error($field_id, $message) {
        $this->errors[$field_id] = $message;
    }
    
    /**
     * Get errors
     */
    public function get_errors() {
        return $this->errors;
    }
    
    /**
     * Has errors
     */
    public function has_errors() {
        return !empty($this->errors);
    }
    
    /**
     * Clear errors
     */
    public function clear_errors() {
        $this->errors = array();
    }
}
