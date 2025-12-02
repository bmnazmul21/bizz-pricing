<?php
/**
 * BizzPlugin Options Framework - Field Sanitizer
 * 
 * @package BizzPlugin_Options_Framework
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Field Sanitizer Class
 */
class BizzPlugin_Field_Sanitizer {
    
    /**
     * Sanitize field value based on type
     */
    public static function sanitize($value, $field) {
        $type = isset($field['type']) ? $field['type'] : 'text';
        $sanitize_callback = isset($field['sanitize_callback']) ? $field['sanitize_callback'] : null;
        
        // Use custom sanitize callback if provided
        if ($sanitize_callback && is_callable($sanitize_callback)) {
            return call_user_func($sanitize_callback, $value, $field);
        }
        
        // Apply filter for custom sanitization
        $value = apply_filters('bizzplugin_sanitize_field_' . $type, $value, $field);
        $value = apply_filters('bizzplugin_sanitize_field', $value, $field, $type);
        
        // Default sanitization based on type
        switch ($type) {
            case 'text':
            case 'password':
                return sanitize_text_field($value);
                
            case 'textarea':
                return sanitize_textarea_field($value);
                
            case 'email':
                return sanitize_email($value);
                
            case 'url':
                return esc_url_raw($value);
                
            case 'number':
            case 'slider':
            case 'range':
                return self::sanitize_number($value, $field);
                
            case 'select':
            case 'radio':
            case 'image_select':
            case 'option_select':
                return self::sanitize_choice($value, $field);
                
            case 'multi_select':
            case 'checkbox_group':
                return self::sanitize_multi_choice($value, $field);
                
            case 'checkbox':
            case 'on_off':
            case 'switch':
                return !empty($value) ? '1' : '0';
                
            case 'color':
                return sanitize_hex_color($value);
                
            case 'date':
                return self::sanitize_date($value);
                
            case 'image':
            case 'file':
            case 'post_select':
                return absint($value);
                
            case 'html':
                return ''; // HTML fields don't save values
                
            case 'repeater':
                return self::sanitize_repeater($value, $field);
                
            default:
                return sanitize_text_field($value);
        }
    }
    
    /**
     * Sanitize repeater field
     */
    private static function sanitize_repeater($value, $field) {
        if (!is_array($value)) {
            return array();
        }
        
        $sub_fields = isset($field['fields']) ? $field['fields'] : array();
        $sanitized = array();
        
        // Re-index the array to remove gaps from deleted items
        $value = array_values($value);
        
        foreach ($value as $index => $item) {
            if (!is_array($item)) {
                continue;
            }
            
            $sanitized_item = array();
            
            foreach ($sub_fields as $sub_field) {
                $sub_field_id = $sub_field['id'];
                $sub_value = isset($item[$sub_field_id]) ? $item[$sub_field_id] : '';
                
                // Sanitize sub-field value based on its type
                $sanitized_item[$sub_field_id] = self::sanitize($sub_value, $sub_field);
            }
            
            // Only add items with at least one non-empty value (preserve valid falsy values like '0')
            $has_value = false;
            foreach ($sanitized_item as $val) {
                if ($val !== '' && $val !== null && (!is_array($val) || !empty($val))) {
                    $has_value = true;
                    break;
                }
            }
            if ($has_value) {
                $sanitized[] = $sanitized_item;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize number field
     */
    private static function sanitize_number($value, $field) {
        $value = floatval($value);
        
        // Check min/max bounds
        if (isset($field['min']) && $value < $field['min']) {
            $value = $field['min'];
        }
        if (isset($field['max']) && $value > $field['max']) {
            $value = $field['max'];
        }
        
        // Return integer or float based on step
        if (isset($field['step']) && floor($field['step']) != $field['step']) {
            return floatval($value);
        }
        
        return intval($value);
    }
    
    /**
     * Sanitize choice field (select, radio)
     */
    private static function sanitize_choice($value, $field) {
        $options = isset($field['options']) ? $field['options'] : array();
        
        if (array_key_exists($value, $options)) {
            return $value;
        }
        
        // Return default if value not in options
        return isset($field['default']) ? $field['default'] : '';
    }
    
    /**
     * Sanitize multi-choice field
     */
    private static function sanitize_multi_choice($value, $field) {
        if (!is_array($value)) {
            return array();
        }
        
        $options = isset($field['options']) ? $field['options'] : array();
        $sanitized = array();
        
        foreach ($value as $item) {
            if (array_key_exists($item, $options)) {
                $sanitized[] = $item;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Sanitize date field
     */
    private static function sanitize_date($value) {
        if (empty($value)) {
            return '';
        }
        
        // Try to parse the date
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return '';
        }
        
        return gmdate('Y-m-d', $timestamp);
    }
}
